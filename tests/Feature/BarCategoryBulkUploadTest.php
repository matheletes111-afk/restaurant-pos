<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BarCategoryBulkUploadTest extends TestCase
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

    public function test_admin_bar_manage_category_page_is_accessible()
    {
        $response = $this->actingAs($this->user)->get('/admin/bar/manage-category');
        $response->assertStatus(200);
        $response->assertSee('Bar Category Management', false);
        $response->assertSee('Bulk Upload Category', false);
        $response->assertSee('bulkUploadCategoryModal', false);
        $response->assertSee('Bar Category Name', false);
    }

    public function test_bar_category_template_download()
    {
        $response = $this->actingAs($this->user)->get('/admin/bar/manage-category/bulk-upload-template');
        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'bar_category_upload_template.xlsx'));
    }

    public function test_bar_category_bulk_excel_upload_with_bar_category_name_column()
    {
        // 1. Build a test spreadsheet with single column "Bar Category Name"
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Bar Category Name');
        $cat1 = 'Test Whisky ' . rand(1000, 9999);
        $cat2 = 'Test Cocktails ' . rand(1000, 9999);
        $sheet->setCellValue('A2', $cat1);
        $sheet->setCellValue('A3', $cat2);

        $tempPath = tempnam(sys_get_temp_dir(), 'bar_cat_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'test_bar_categories.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($this->user)->post('/admin/bar/manage-category/bulk-upload', [
            'bulk_file' => $uploadedFile,
        ]);

        $response->assertSessionHas('success');

        // Verify categories exist in database with category_type = 'BAR'
        $this->assertDatabaseHas('category', [
            'name' => $cat1,
            'category_type' => 'BAR',
            'restaurant_id' => $this->user->restaurant_id,
        ]);

        $this->assertDatabaseHas('category', [
            'name' => $cat2,
            'category_type' => 'BAR',
            'restaurant_id' => $this->user->restaurant_id,
        ]);

        @unlink($tempPath);
    }
}
