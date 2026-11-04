<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\BarBrand;
use App\Models\SubCategory;
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

    public function test_admin_bar_items_page_shows_bulk_upload_modal_and_button()
    {
        $response = $this->actingAs($this->user)->get('/admin/bar/items');
        $response->assertStatus(200);
        $response->assertSee('Bulk Excel Upload', false);
        $response->assertSee('bulkUploadItemModal', false);
        $response->assertSee('Product Name *', false);
        $response->assertSee('Bar Category *', false);
        $response->assertSee('Brand Name (Optional)', false);
    }

    public function test_bar_item_template_download()
    {
        $response = $this->actingAs($this->user)->get('/admin/bar/items/bulk-upload-template');
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'bar_items_upload_template.xlsx'));
    }

    public function test_bar_item_bulk_excel_upload_auto_creates_category_and_brand_and_skips_cover_image()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'Product Name *');
        $sheet->setCellValue('B1', 'Bar Category *');
        $sheet->setCellValue('C1', 'Brand Name (Optional)');
        $sheet->setCellValue('D1', 'Bottle Size in ml *');
        $sheet->setCellValue('E1', 'Opening Bottle Qty');
        $sheet->setCellValue('F1', 'Extra ML');
        $sheet->setCellValue('G1', 'State VAT Rate (%)');
        $sheet->setCellValue('H1', 'Description (Optional)');

        $uniqueSuffix = rand(10000, 99999);
        $product1 = 'Glenfiddich 18 ' . $uniqueSuffix;
        $category1 = 'Single Malt Whisky ' . $uniqueSuffix;
        $brand1 = 'Glenfiddich Distillery ' . $uniqueSuffix;

        $product2 = 'Grey Goose VX ' . $uniqueSuffix;
        $category2 = 'Vodka Collection ' . $uniqueSuffix;
        // brand2 left empty to test optional brand

        // Row 2: Glenfiddich 18, 750ml, 3 bottles, 200 extra ml, 20% VAT
        $sheet->setCellValue('A2', $product1);
        $sheet->setCellValue('B2', $category1);
        $sheet->setCellValue('C2', $brand1);
        $sheet->setCellValue('D2', 750);
        $sheet->setCellValue('E2', 3);
        $sheet->setCellValue('F2', 200);
        $sheet->setCellValue('G2', 20);
        $sheet->setCellValue('H2', 'Aged 18 years in Spanish Oloroso wood and American oak.');

        // Row 3: Grey Goose VX, 1000ml, 2 bottles, 0 extra ml, 22% VAT, no brand
        $sheet->setCellValue('A3', $product2);
        $sheet->setCellValue('B3', $category2);
        $sheet->setCellValue('C3', '');
        $sheet->setCellValue('D3', 1000);
        $sheet->setCellValue('E3', 2);
        $sheet->setCellValue('F3', 0);
        $sheet->setCellValue('G3', 22);
        $sheet->setCellValue('H3', 'Masterful French vodka crafted with cognac.');

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
        $this->assertDatabaseHas('category', [
            'name' => $category1,
            'category_type' => 'BAR',
            'restaurant_id' => $this->user->restaurant_id,
            'status' => 'A',
        ]);
        $cat1 = Category::where('name', $category1)->where('restaurant_id', $this->user->restaurant_id)->first();
        $this->assertNotNull($cat1);

        // Verify Auto-created Brand 1
        $this->assertDatabaseHas('bar_brands', [
            'name' => $brand1,
            'restaurant_id' => $this->user->restaurant_id,
            'status' => 'A',
        ]);
        $b1 = BarBrand::where('name', $brand1)->where('restaurant_id', $this->user->restaurant_id)->first();
        $this->assertNotNull($b1);

        // Verify Product 1 in sub_category with resolved category_id and brand_id
        // Stock: (750 * 3) + 200 = 2450 ML
        $this->assertDatabaseHas('sub_category', [
            'name' => $product1,
            'category_id' => $cat1->id,
            'brand_id' => $b1->id,
            'bottle_size_ml' => 750,
            'opening_qty' => 2450,
            'current_stock_ml' => 2450,
            'food_type' => 'BAR',
            'restaurant_id' => $this->user->restaurant_id,
            'image' => null, // Cover image skipped
        ]);

        // Verify Category 2
        $this->assertDatabaseHas('category', [
            'name' => $category2,
            'category_type' => 'BAR',
            'restaurant_id' => $this->user->restaurant_id,
        ]);
        $cat2 = Category::where('name', $category2)->where('restaurant_id', $this->user->restaurant_id)->first();
        $this->assertNotNull($cat2);

        // Verify Product 2 with brand_id null
        // Stock: (1000 * 2) + 0 = 2000 ML
        $this->assertDatabaseHas('sub_category', [
            'name' => $product2,
            'category_id' => $cat2->id,
            'brand_id' => null,
            'bottle_size_ml' => 1000,
            'opening_qty' => 2000,
            'current_stock_ml' => 2000,
            'food_type' => 'BAR',
            'restaurant_id' => $this->user->restaurant_id,
            'image' => null,
        ]);

        @unlink($tempPath);
    }
}

