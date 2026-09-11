<?php

namespace Tests\Feature;

use App\Models\DemoLead;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class CrmBulkUploadTest extends TestCase
{
    protected function getSuperAdmin()
    {
        $superAdmin = new User();
        $superAdmin->id = 1;
        $superAdmin->name = 'Super Admin';
        $superAdmin->email = 'admin@example.com';
        $superAdmin->role = 'SA';
        $superAdmin->permissions = ['admin_crm'];
        return $superAdmin;
    }

    public function test_super_admin_can_download_sample_template()
    {
        $superAdmin = $this->getSuperAdmin();

        $response = $this->actingAs($superAdmin)->get(route('admin.crm.download-sample'));
        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-type'), 'spreadsheetml') ||
            str_contains($response->headers->get('content-disposition'), 'crm_leads_import_sample.xlsx')
        );
    }

    public function test_bulk_upload_creates_leads_when_all_rows_are_valid()
    {
        $superAdmin = $this->getSuperAdmin();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Full Name *');
        $sheet->setCellValue('B1', 'Email Address *');
        $sheet->setCellValue('C1', 'Phone Number');
        $sheet->setCellValue('D1', 'Restaurant Name');
        $sheet->setCellValue('E1', 'Source');
        $sheet->setCellValue('F1', 'Followup Notes');

        $email1 = 'bulk_valid_1_' . time() . '@example.com';
        $email2 = 'bulk_valid_2_' . time() . '@example.com';

        $sheet->setCellValue('A2', 'Bulk User One');
        $sheet->setCellValue('B2', $email1);
        $sheet->setCellValue('C2', '+91 9999988888');
        $sheet->setCellValue('D2', 'Cafe One');
        $sheet->setCellValue('E2', 'Social Media');
        $sheet->setCellValue('F2', 'Note 1');

        $sheet->setCellValue('A3', 'Bulk User Two');
        $sheet->setCellValue('B3', $email2);
        $sheet->setCellValue('C3', '+91 9999977777');
        $sheet->setCellValue('D3', 'Cafe Two');
        $sheet->setCellValue('E3', 'Search Engine');
        $sheet->setCellValue('F3', 'Note 2');

        $tempFile = tempnam(sys_get_temp_dir(), 'test_leads_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_leads.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($superAdmin)->post(route('admin.crm.bulk-upload'), [
            'excel_file' => $uploadedFile,
            'default_source' => 'Bulk Upload'
        ]);

        $response->assertRedirect(route('admin.crm.index'));
        $response->assertSessionHas('success');

        $lead1 = DemoLead::where('email_address', $email1)->first();
        $this->assertNotNull($lead1);
        $this->assertEquals('Bulk User One', $lead1->full_name);
        $this->assertEquals('Contacted', $lead1->status);

        $lead2 = DemoLead::where('email_address', $email2)->first();
        $this->assertNotNull($lead2);
        $this->assertEquals('Bulk User Two', $lead2->full_name);
        $this->assertEquals('Contacted', $lead2->status);

        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    public function test_bulk_upload_rejects_and_saves_nothing_if_email_already_exists_in_database()
    {
        $superAdmin = $this->getSuperAdmin();

        $existingEmail = 'existing_lead_' . time() . '@example.com';
        DemoLead::create([
            'full_name' => 'Existing Database Lead',
            'email_address' => $existingEmail,
            'status' => 'Contacted'
        ]);

        $newEmail = 'new_lead_' . time() . '@example.com';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Full Name');
        $sheet->setCellValue('B1', 'Email');

        // Row 2: Brand new lead
        $sheet->setCellValue('A2', 'Brand New User');
        $sheet->setCellValue('B2', $newEmail);

        // Row 3: Already existing email in database
        $sheet->setCellValue('A3', 'Duplicate DB User');
        $sheet->setCellValue('B3', $existingEmail);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_exist_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_exist.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($superAdmin)->post(route('admin.crm.bulk-upload'), [
            'excel_file' => $uploadedFile,
        ]);

        // Assert error and session has import errors
        $response->assertSessionHas('error');
        $response->assertSessionHas('import_errors');

        // Verify that the NEW lead from row 2 was NOT saved into database because of the error in row 3!
        $newLeadInDb = DemoLead::where('email_address', $newEmail)->first();
        $this->assertNull($newLeadInDb, 'No leads should be saved to database when any error/duplicate occurs.');

        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    public function test_bulk_upload_rejects_and_saves_nothing_if_duplicate_email_in_same_file()
    {
        $superAdmin = $this->getSuperAdmin();

        $dupEmail = 'duplicate_in_file_' . time() . '@example.com';

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Full Name');
        $sheet->setCellValue('B1', 'Email');

        // Row 2: First occurrence
        $sheet->setCellValue('A2', 'First Occurrence');
        $sheet->setCellValue('B2', $dupEmail);

        // Row 3: Second occurrence with same email
        $sheet->setCellValue('A3', 'Second Occurrence');
        $sheet->setCellValue('B3', $dupEmail);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_dup_') . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        $uploadedFile = new UploadedFile(
            $tempFile,
            'test_dup.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );

        $response = $this->actingAs($superAdmin)->post(route('admin.crm.bulk-upload'), [
            'excel_file' => $uploadedFile,
        ]);

        $response->assertSessionHas('error');
        $response->assertSessionHas('import_errors');

        // Neither record should be created
        $lead = DemoLead::where('email_address', $dupEmail)->first();
        $this->assertNull($lead, 'Duplicate in file must abort and save nothing.');

        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }
}
