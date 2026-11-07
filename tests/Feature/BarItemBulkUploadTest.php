<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\BarBrand;
use App\Models\SubCategory;
use App\Models\BarItemVariation;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BarItemBulkUploadTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::whereNotNull('restaurant_id')->first();
        if (!$this->user) {
            $this->user = User::factory()->create();
        }
        $this->user->role = 'SA'; // Bypass subscription check
    }

    public function test_admin_bar_items_page_shows_bulk_upload_modal_and_button_and_full_bottle_price()
    {
        $response = $this->actingAs($this->user)->get('/admin/bar/items');
        $response->assertStatus(200);
        $response->assertSee('Bulk Excel Upload', false);
        $response->assertSee('bulkUploadItemModal', false);
        $response->assertSee('Product Name *', false);
        $response->assertSee('Bar Category *', false);
        $response->assertSee('Full Bottle Price *', false);
        $response->assertSee('Full Bottle Price (₹)', false);
        $response->assertSee('30ml Peg Price (₹)', false);
        $response->assertSee('60ml Large Price (₹)', false);
        $response->assertSee('90ml Patiala Price (₹)', false);
    }

    public function test_bar_item_template_download_includes_full_bottle_price()
    {
        $response = $this->actingAs($this->user)->get('/admin/bar/items/bulk-upload-template');
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'bar_items_upload_template.xlsx'));
    }

    public function test_bar_item_store_requires_full_bottle_price_and_auto_creates_variation()
    {
        // 1. Missing price should fail validation
        $category = Category::where('restaurant_id', $this->user->restaurant_id)
            ->where('category_type', 'BAR')
            ->first();

        if (!$category) {
            $category = Category::create([
                'name' => 'Test Bar Cat',
                'category_type' => 'BAR',
                'restaurant_id' => $this->user->restaurant_id,
                'status' => 'A'
            ]);
        }

        $responseFail = $this->actingAs($this->user)->post('/admin/bar/items/store', [
            'name' => 'Whisky Without Price ' . rand(1000, 9999),
            'category_id' => $category->id,
            'bottle_size_ml' => 750,
            // 'price' omitted
        ]);
        $responseFail->assertSessionHasErrors(['price']);

        // 2. Valid price with peg prices should create item and auto-create "Full Bottle" and peg variations
        $uniqueName = 'Single Malt Auto Var ' . rand(1000, 9999);
        $responseSuccess = $this->actingAs($this->user)->post('/admin/bar/items/store', [
            'name' => $uniqueName,
            'category_id' => $category->id,
            'bottle_size_ml' => 750,
            'price' => 3500,
            'price_30ml' => 250,
            'price_60ml' => 480,
            'price_90ml' => 700,
            'opening_bottle_qty' => 2,
            'extra_ml' => 0,
            'vat_rate' => 20,
        ]);

        $responseSuccess->assertSessionHas('success');

        $item = SubCategory::where('name', $uniqueName)
            ->where('restaurant_id', $this->user->restaurant_id)
            ->first();

        $this->assertNotNull($item);
        $this->assertEquals(3500, floatval($item->price));

        // Verify "Full Bottle" variation was automatically created
        $variation = BarItemVariation::where('sub_category_id', $item->id)
            ->where('name', 'Full Bottle')
            ->first();

        $this->assertNotNull($variation);
        $this->assertEquals(750, floatval($variation->ml));
        $this->assertEquals(3500, floatval($variation->price));
        $this->assertEquals('A', $variation->status);

        // Verify "30ml Peg" variation was automatically created
        $var30 = BarItemVariation::where('sub_category_id', $item->id)->where('ml', 30)->first();
        $this->assertNotNull($var30);
        $this->assertEquals(250, floatval($var30->price));

        // Verify "60ml Large" variation was automatically created
        $var60 = BarItemVariation::where('sub_category_id', $item->id)->where('ml', 60)->first();
        $this->assertNotNull($var60);
        $this->assertEquals(480, floatval($var60->price));

        // Verify "90ml Patiala" variation was automatically created
        $var90 = BarItemVariation::where('sub_category_id', $item->id)->where('ml', 90)->first();
        $this->assertNotNull($var90);
        $this->assertEquals(700, floatval($var90->price));

        // 3. Update item price and size and peg prices, verify variations update automatically
        $responseUpdate = $this->actingAs($this->user)->post('/admin/bar/items/update', [
            'id' => $item->id,
            'name' => $uniqueName . ' Updated',
            'category_id' => $category->id,
            'bottle_size_ml' => 1000,
            'price' => 4500,
            'price_30ml' => 300,
            'price_60ml' => 580,
            'price_90ml' => 850,
            'opening_bottle_qty' => 3,
            'extra_ml' => 0,
            'vat_rate' => 20,
        ]);

        $responseUpdate->assertSessionHas('success');

        $variation->refresh();
        $this->assertEquals(1000, floatval($variation->ml));
        $this->assertEquals(4500, floatval($variation->price));

        $var30->refresh();
        $this->assertEquals(300, floatval($var30->price));

        $var60->refresh();
        $this->assertEquals(580, floatval($var60->price));

        $var90->refresh();
        $this->assertEquals(850, floatval($var90->price));
    }

    public function test_bar_item_bulk_excel_upload_with_price_and_auto_variation()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 12-Column Headers
        $sheet->setCellValue('A1', 'Product Name *');
        $sheet->setCellValue('B1', 'Bar Category *');
        $sheet->setCellValue('C1', 'Brand Name (Optional)');
        $sheet->setCellValue('D1', 'Bottle Size in ml *');
        $sheet->setCellValue('E1', 'Full Bottle Price *');
        $sheet->setCellValue('F1', '30ml Price (Optional)');
        $sheet->setCellValue('G1', '60ml Price (Optional)');
        $sheet->setCellValue('H1', '90ml Price (Optional)');
        $sheet->setCellValue('I1', 'Opening Bottle Qty');
        $sheet->setCellValue('J1', 'Extra ML');
        $sheet->setCellValue('K1', 'State VAT Rate (%)');
        $sheet->setCellValue('L1', 'Description (Optional)');

        $uniqueSuffix = rand(10000, 99999);
        $product1 = 'Glenfiddich 18 ' . $uniqueSuffix;
        $category1 = 'Single Malt Whisky ' . $uniqueSuffix;
        $brand1 = 'Glenfiddich Distillery ' . $uniqueSuffix;

        $product2 = 'Grey Goose VX ' . $uniqueSuffix;
        $category2 = 'Vodka Collection ' . $uniqueSuffix;

        // Row 2: Glenfiddich 18, 750ml, ₹5800, 30ml=₹320, 60ml=₹600, 90ml=₹850, 3 bottles, 200 extra ml, 20% VAT
        $sheet->setCellValue('A2', $product1);
        $sheet->setCellValue('B2', $category1);
        $sheet->setCellValue('C2', $brand1);
        $sheet->setCellValue('D2', 750);
        $sheet->setCellValue('E2', 5800);
        $sheet->setCellValue('F2', 320);
        $sheet->setCellValue('G2', 600);
        $sheet->setCellValue('H2', 850);
        $sheet->setCellValue('I2', 3);
        $sheet->setCellValue('J2', 200);
        $sheet->setCellValue('K2', 20);
        $sheet->setCellValue('L2', 'Aged 18 years in Spanish Oloroso wood.');

        // Row 3: Grey Goose VX, 1000ml, ₹6200, no pegs, 2 bottles, 0 extra ml, 22% VAT, no brand
        $sheet->setCellValue('A3', $product2);
        $sheet->setCellValue('B3', $category2);
        $sheet->setCellValue('C3', '');
        $sheet->setCellValue('D3', 1000);
        $sheet->setCellValue('E3', 6200);
        $sheet->setCellValue('F3', '');
        $sheet->setCellValue('G3', '');
        $sheet->setCellValue('H3', '');
        $sheet->setCellValue('I3', 2);
        $sheet->setCellValue('J3', 0);
        $sheet->setCellValue('K3', 22);
        $sheet->setCellValue('L3', 'Masterful French vodka crafted with cognac.');

        $tempPath = tempnam(sys_get_temp_dir(), 'bar_item_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'test_bar_items.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($this->user)->post('/admin/bar/items/bulk-upload', [
            'bulk_file' => $uploadedFile,
        ]);

        $response->assertSessionHas('success');

        // Verify Auto-created Category 1
        $cat1 = Category::where('name', $category1)->where('restaurant_id', $this->user->restaurant_id)->first();
        $this->assertNotNull($cat1);

        // Verify Auto-created Brand 1
        $b1 = BarBrand::where('name', $brand1)->where('restaurant_id', $this->user->restaurant_id)->first();
        $this->assertNotNull($b1);

        // Verify Product 1 in sub_category
        $item1 = SubCategory::where('name', $product1)->where('restaurant_id', $this->user->restaurant_id)->first();
        $this->assertNotNull($item1);
        $this->assertEquals(5800, floatval($item1->price));
        $this->assertEquals(750, floatval($item1->bottle_size_ml));
        $this->assertEquals(2450, floatval($item1->current_stock_ml)); // (750 * 3) + 200

        // Verify Auto-created "Full Bottle" Variation for Product 1
        $var1 = BarItemVariation::where('sub_category_id', $item1->id)->where('name', 'Full Bottle')->first();
        $this->assertNotNull($var1);
        $this->assertEquals(750, floatval($var1->ml));
        $this->assertEquals(5800, floatval($var1->price));

        // Verify Auto-created Peg Variations for Product 1
        $var1_30 = BarItemVariation::where('sub_category_id', $item1->id)->where('ml', 30)->first();
        $this->assertNotNull($var1_30);
        $this->assertEquals(320, floatval($var1_30->price));

        $var1_60 = BarItemVariation::where('sub_category_id', $item1->id)->where('ml', 60)->first();
        $this->assertNotNull($var1_60);
        $this->assertEquals(600, floatval($var1_60->price));

        $var1_90 = BarItemVariation::where('sub_category_id', $item1->id)->where('ml', 90)->first();
        $this->assertNotNull($var1_90);
        $this->assertEquals(850, floatval($var1_90->price));

        // Verify Product 2
        $item2 = SubCategory::where('name', $product2)->where('restaurant_id', $this->user->restaurant_id)->first();
        $this->assertNotNull($item2);
        $this->assertEquals(6200, floatval($item2->price));
        $this->assertEquals(1000, floatval($item2->bottle_size_ml));
        $this->assertEquals(2000, floatval($item2->current_stock_ml)); // (1000 * 2)

        // Verify Auto-created "Full Bottle" Variation for Product 2
        $var2 = BarItemVariation::where('sub_category_id', $item2->id)->where('name', 'Full Bottle')->first();
        $this->assertNotNull($var2);
        $this->assertEquals(1000, floatval($var2->ml));
        $this->assertEquals(6200, floatval($var2->price));

        // Product 2 had empty peg prices, so verify no peg variations for product 2
        $this->assertNull(BarItemVariation::where('sub_category_id', $item2->id)->where('ml', 30)->first());
        $this->assertNull(BarItemVariation::where('sub_category_id', $item2->id)->where('ml', 60)->first());
        $this->assertNull(BarItemVariation::where('sub_category_id', $item2->id)->where('ml', 90)->first());

        @unlink($tempPath);
    }
}
