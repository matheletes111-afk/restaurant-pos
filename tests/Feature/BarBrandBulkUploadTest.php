<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\BarBrand;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BarBrandBulkUploadTest extends TestCase
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

    public function test_admin_bar_brands_page_is_accessible()
    {
        $response = $this->actingAs($this->user)->get('/admin/bar/brands');
        $response->assertStatus(200);
        $response->assertSee('Bar Brand Management', false);
        $response->assertSee('Bulk Excel Upload', false);
        $response->assertSee('bulkUploadBrandModal', false);
        $response->assertSee('Brand Name *', false);
        $response->assertSee('Description (Optional)', false);
    }

    public function test_bar_brand_template_download()
    {
        $response = $this->actingAs($this->user)->get('/admin/bar/brands/bulk-upload-template');
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'bar_brands_upload_template.xlsx'));
    }

    public function test_bar_brand_bulk_excel_upload_with_name_and_description()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Brand Name *');
        $sheet->setCellValue('B1', 'Description (Optional)');

        $brand1 = 'Test Brand Alpha ' . rand(1000, 9999);
        $desc1 = 'Premium single malt test distillery';
        $brand2 = 'Test Brand Beta ' . rand(1000, 9999);
        $desc2 = 'Craft beer test brewery';

        $sheet->setCellValue('A2', $brand1);
        $sheet->setCellValue('B2', $desc1);
        $sheet->setCellValue('A3', $brand2);
        $sheet->setCellValue('B3', $desc2);

        $tempPath = tempnam(sys_get_temp_dir(), 'bar_brand_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'test_bar_brands.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($this->user)->post('/admin/bar/brands/bulk-upload', [
            'bulk_file' => $uploadedFile,
        ]);

        $response->assertSessionHas('success');

        // Verify brands exist in database
        $this->assertDatabaseHas('bar_brands', [
            'name' => $brand1,
            'description' => $desc1,
            'restaurant_id' => $this->user->restaurant_id,
            'status' => 'A',
        ]);

        $this->assertDatabaseHas('bar_brands', [
            'name' => $brand2,
            'description' => $desc2,
            'restaurant_id' => $this->user->restaurant_id,
            'status' => 'A',
        ]);

        @unlink($tempPath);
    }
}

