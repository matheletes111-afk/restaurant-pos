<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Plan;
use App\Models\RestaurantMaster;
use App\Models\Subscription;
use App\Models\StaffDocument;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;

class RestaurantStaffDocumentTest extends TestCase
{
    use DatabaseTransactions;

    protected function setupRestaurantAndAdmin()
    {
        $restaurant = RestaurantMaster::create([
            'name' => 'Doc Restro ' . uniqid(),
            'restaurant_id_unique' => 'REST-DOC-' . rand(100, 999),
            'parent_id' => null,
            'address' => '500 Document Way',
            'status' => 'A'
        ]);

        $admin = User::create([
            'name' => 'Restro Admin ' . uniqid(),
            'email' => 'admin_' . uniqid() . '@example.com',
            'phone' => '98765' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'restaurant_id' => $restaurant->id,
            'status' => 'A'
        ]);

        $plan = Plan::create([
            'name' => 'Staff Plan ' . uniqid(),
            'price' => 999.00,
            'duration_days' => 30,
            'billing_cycle' => 'monthly',
            'is_default_plan' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N',
        ]);

        Subscription::create([
            'user_id' => $restaurant->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(29),
            'amount' => 999.00,
            'currency' => 'INR',
        ]);

        return [$restaurant, $admin];
    }

    protected function createStaffMember($restaurant)
    {
        return User::create([
            'name' => 'John Doc Staff ' . uniqid(),
            'email' => 'staff_' . uniqid() . '@example.com',
            'phone' => '91234' . rand(10000, 99999),
            'password' => bcrypt('password123'),
            'role' => 'STAFF',
            'role_type' => 'Waiter',
            'restaurant_id' => $restaurant->id,
            'status' => 'A',
            'permissions' => json_encode(['staff' => ['view', 'create', 'edit', 'delete']])
        ]);
    }

    public function test_can_view_document_management_tab_on_operations_page()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        // Test direct separate route
        $response = $this->actingAs($admin)
            ->get(route('restaurant.staff.documents', $staff->id));

        $response->assertStatus(200);
        $response->assertSee('Document Management');
        $response->assertSee('Staff Document Repository');
        $response->assertSee('0 of 5 Files Used');
        $response->assertSee('Upload New Document');
        $response->assertSee('name="document_file"', false);
        $response->assertSee('name="title"', false);
        $response->assertSee('name="description"', false);
    }

    public function test_can_upload_valid_document_within_5mb_and_persists()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        // Fake a 1.5MB PDF file (1536 KB)
        $fakeFile = UploadedFile::fake()->create('aadhaar_card.pdf', 1536, 'application/pdf');

        $response = $this->actingAs($admin)
            ->post(route('restaurant.staff.upload-document'), [
                'user_id' => $staff->id,
                'title' => 'Aadhaar Card',
                'description' => 'Original Aadhaar scan verified with QR code.',
                'document_file' => $fakeFile,
            ]);

        $response->assertRedirect(route('restaurant.staff.documents', $staff->id));
        $response->assertSessionHas('success', 'Document uploaded successfully!');

        $this->assertDatabaseHas('staff_documents', [
            'user_id' => $staff->id,
            'restaurant_id' => $restaurant->id,
            'title' => 'Aadhaar Card',
            'description' => 'Original Aadhaar scan verified with QR code.',
            'file_name' => 'aadhaar_card.pdf',
            'file_type' => 'pdf',
        ]);

        $doc = StaffDocument::where('user_id', $staff->id)->first();
        $this->assertNotNull($doc);
        $this->assertEquals('1.50 MB', $doc->formatted_file_size);
        $this->assertFileExists(public_path($doc->file_path));

        // Clean up uploaded test file
        if (file_exists(public_path($doc->file_path))) {
            @unlink(public_path($doc->file_path));
        }
    }

    public function test_cannot_upload_document_exceeding_5mb()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        // Fake a 6MB file (6144 KB, which is > 5120 KB limit)
        $heavyFile = UploadedFile::fake()->create('huge_portfolio.pdf', 6144, 'application/pdf');

        $response = $this->actingAs($admin)
            ->from(route('restaurant.staff.documents', $staff->id))
            ->post(route('restaurant.staff.upload-document'), [
                'user_id' => $staff->id,
                'title' => 'Huge Portfolio',
                'description' => 'Oversized file',
                'document_file' => $heavyFile,
            ]);

        $response->assertRedirect(route('restaurant.staff.documents', $staff->id));
        $response->assertSessionHasErrors('document_file');
        $this->assertDatabaseMissing('staff_documents', [
            'user_id' => $staff->id,
            'title' => 'Huge Portfolio',
        ]);
    }

    public function test_enforces_maximum_5_documents_limit()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        $createdDocs = [];
        // Upload 5 documents (up to limit)
        for ($i = 1; $i <= 5; $i++) {
            $file = UploadedFile::fake()->create("doc_{$i}.pdf", 100, 'application/pdf');
            $this->actingAs($admin)->post(route('restaurant.staff.upload-document'), [
                'user_id' => $staff->id,
                'title' => "Document {$i}",
                'description' => "Description for document {$i}",
                'document_file' => $file,
            ]);
        }

        $this->assertEquals(5, StaffDocument::where('user_id', $staff->id)->count());

        // Attempt 6th document upload
        $extraFile = UploadedFile::fake()->create("doc_6.pdf", 100, 'application/pdf');
        $response = $this->actingAs($admin)->post(route('restaurant.staff.upload-document'), [
            'user_id' => $staff->id,
            'title' => 'Document 6 (Exceeds limit)',
            'description' => 'Should be rejected',
            'document_file' => $extraFile,
        ]);

        $response->assertRedirect(route('restaurant.staff.documents', $staff->id));
        $response->assertSessionHas('error');
        $this->assertEquals(5, StaffDocument::where('user_id', $staff->id)->count());

        // Check page shows limit reached banner and disables new upload
        $pageResponse = $this->actingAs($admin)->get(route('restaurant.staff.documents', $staff->id));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('Maximum Document Limit Reached (5/5)');
        $pageResponse->assertDontSee('id="documentUploadForm"', false);

        // Clean up disk files
        foreach (StaffDocument::where('user_id', $staff->id)->get() as $doc) {
            if (file_exists(public_path($doc->file_path))) {
                @unlink(public_path($doc->file_path));
            }
        }
    }

    public function test_can_download_and_delete_uploaded_document()
    {
        [$restaurant, $admin] = $this->setupRestaurantAndAdmin();
        $staff = $this->createStaffMember($restaurant);

        $fakeFile = UploadedFile::fake()->create('contract.pdf', 300, 'application/pdf');

        $this->actingAs($admin)->post(route('restaurant.staff.upload-document'), [
            'user_id' => $staff->id,
            'title' => 'Employment Contract',
            'description' => 'Signed employment agreement',
            'document_file' => $fakeFile,
        ]);

        $doc = StaffDocument::where('user_id', $staff->id)->where('title', 'Employment Contract')->first();
        $this->assertNotNull($doc);
        $filePathOnDisk = public_path($doc->file_path);
        $this->assertFileExists($filePathOnDisk);

        // Test Download
        $downloadResponse = $this->actingAs($admin)->get(route('restaurant.staff.download-document', $doc->id));
        $downloadResponse->assertStatus(200);
        $this->assertTrue($downloadResponse->headers->has('content-disposition'));

        // Test Delete
        $deleteResponse = $this->actingAs($admin)->get(route('restaurant.staff.delete-document', $doc->id));
        $deleteResponse->assertRedirect(route('restaurant.staff.documents', $staff->id));
        $deleteResponse->assertSessionHas('success', 'Document deleted successfully!');

        $this->assertDatabaseMissing('staff_documents', ['id' => $doc->id]);
        $this->assertFileDoesNotExist($filePathOnDisk);
    }
}
