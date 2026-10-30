<?php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Plan;
use App\Models\SubCategory;
use App\Models\Subscription;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BarCategoryController extends Controller
{
    /**
     * Display a listing of Bar categories (category_type = 'BAR')
     */
    public function index()
    {
        $restaurantId = auth()->user()->restaurant_id;
        $data = [];

        $data['data'] = Category::where('status', '!=', 'D')
            ->where('restaurant_id', $restaurantId)
            ->where('category_type', 'BAR')
            ->withCount(['subcategories' => function ($query) {
                $query->where('status', '!=', 'D');
            }])
            ->orderBy('id', 'desc')
            ->get();

        $subRestaurantId = auth()->user()->getSubscriptionRestaurantId() ?? $restaurantId;
        $check_plan = Subscription::where('user_id', $subRestaurantId)->active()->first();
        $data['plan_details'] = $check_plan ? Plan::where('id', $check_plan->plan_id)->first() : null;

        // Count overall categories in restaurant to verify plan limits
        $data['total_all_categories'] = Category::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->count();

        $data['category_type'] = 'BAR';

        return view('bar.category_index', $data);
    }

    /**
     * Insert a new Bar category (strictly forces category_type = 'BAR')
     */
    public function insert(Request $request)
    {
        \Log::info('Bar Category Insert Request:', $request->except('image'));

        $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ], [
            'name.required' => 'Please enter the bar category name.',
            'image.image'   => 'The file must be an image.',
            'image.mimes'   => 'The category image must be of type: jpg, jpeg, png, webp, gif.',
            'image.max'     => 'The category image must not exceed 5MB.',
        ]);

        $restaurantId = auth()->user()->restaurant_id;
        $subRestaurantId = auth()->user()->getSubscriptionRestaurantId() ?? $restaurantId;
        $check_plan = Subscription::where('user_id', $subRestaurantId)->active()->first();
        $plan_details = $check_plan ? Plan::where('id', $check_plan->plan_id)->first() : null;

        if ($plan_details && !empty($plan_details->category_number) && $plan_details->category_number > 0) {
            $currentCategoriesCount = Category::where('restaurant_id', $restaurantId)
                ->where('status', '!=', 'D')
                ->count();

            if ($currentCategoriesCount >= (int)$plan_details->category_number) {
                return redirect()->back()->with('error', "Category limit reached! Your plan allows a maximum of {$plan_details->category_number} categories across your establishment.");
            }
        }

        try {
            $new = new Category();
            $new->name = trim($request->name);
            $new->category_type = 'BAR'; // In this section, category_type is strictly BAR
            $new->user_id = auth()->user()->id;
            $new->restaurant_id = $restaurantId;
            $new->status = 'A';

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = 'bar-' . time() . '-' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                $image->move(storage_path('app/public/category'), $filename);
                $new->image = $filename;
            }

            $new->save();

            $new->slug = Str::slug($request->name) . '-' . $new->id;
            $new->save();

            \Log::info('Bar Category Created Successfully:', ['id' => $new->id, 'name' => $new->name]);

            return redirect()->back()->with('success', 'Bar Category "' . $new->name . '" created successfully with category_type BAR.');
        } catch (\Exception $e) {
            \Log::error('Bar Category Insert Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create bar category: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing Bar category
     */
    public function update(Request $request)
    {
        \Log::info('Bar Category Update Request:', $request->except('image'));

        $request->validate([
            'id'    => 'required|exists:category,id',
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ], [
            'name.required' => 'Please enter the bar category name.',
            'image.image'   => 'The file must be an image.',
            'image.mimes'   => 'The category image must be of type: jpg, jpeg, png, webp, gif.',
            'image.max'     => 'The category image must not exceed 5MB.',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        try {
            $check = Category::where('id', $request->id)
                ->where('restaurant_id', $restaurantId)
                ->firstOrFail();

            $upd = [
                'name'          => trim($request->name),
                'slug'          => Str::slug($request->name) . '-' . $request->id,
                'category_type' => 'BAR', // Maintain BAR category type
            ];

            if ($request->hasFile('image')) {
                if ($check->image && file_exists(storage_path('app/public/category/' . $check->image))) {
                    @unlink(storage_path('app/public/category/' . $check->image));
                }

                $image = $request->file('image');
                $filename = 'bar-' . time() . '-' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                $image->move(storage_path('app/public/category'), $filename);
                $upd['image'] = $filename;
            }

            Category::where('id', $request->id)->update($upd);

            \Log::info('Bar Category Updated:', ['id' => $request->id]);

            return redirect()->back()->with('success', 'Bar Category updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Bar Category Update Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update bar category: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete a Bar category
     */
    public function delete($id)
    {
        $restaurantId = auth()->user()->restaurant_id;

        try {
            $check = Category::where('id', $id)
                ->where('restaurant_id', $restaurantId)
                ->first();

            if ($check) {
                if ($check->image && file_exists(storage_path('app/public/category/' . $check->image))) {
                    @unlink(storage_path('app/public/category/' . $check->image));
                }

                Category::where('id', $id)->update(['status' => 'D']);
                return redirect()->back()->with('success', 'Bar Category deleted successfully.');
            }

            return redirect()->back()->with('error', 'Category not found or unauthorized access.');
        } catch (\Exception $e) {
            \Log::error('Bar Category Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete bar category.');
        }
    }

    /**
     * Bulk upload Bar categories from Excel file (assigns category_type = 'BAR')
     */
    public function bulkUploadCategory(Request $request)
    {
        $request->validate([
            'bulk_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'bulk_file.required' => 'Please select an Excel or CSV file to upload.',
            'bulk_file.mimes'    => 'The file must be a spreadsheet (.xlsx, .xls, .csv).',
            'bulk_file.max'      => 'The file size cannot exceed 10MB.',
        ]);

        $restaurantId = auth()->user()->restaurant_id;
        $subRestaurantId = auth()->user()->getSubscriptionRestaurantId() ?? $restaurantId;
        $check_plan = Subscription::where('user_id', $subRestaurantId)->active()->first();
        $plan_details = $check_plan ? Plan::where('id', $check_plan->plan_id)->first() : null;

        $maxCategories = ($plan_details && !empty($plan_details->category_number)) ? (int)$plan_details->category_number : 0;
        $currentCategoriesCount = Category::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->count();

        if ($maxCategories > 0 && $currentCategoriesCount >= $maxCategories) {
            return redirect()->back()->with('error', "Category limit reached! Your plan allows a maximum of {$maxCategories} categories.");
        }

        try {
            $file = $request->file('bulk_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $categoryNames = [];
            $headerSkipped = false;

            $existingCategories = Category::where('restaurant_id', $restaurantId)
                ->where('status', '!=', 'D')
                ->pluck('name')
                ->map(fn($n) => strtolower(trim($n)))
                ->toArray();

            foreach ($rows as $row) {
                if (empty(array_filter($row, fn($val) => !is_null($val) && trim((string)$val) !== ''))) {
                    continue;
                }

                $rawName = trim((string)($row[0] ?? ''));

                if (!$headerSkipped && in_array(strtolower($rawName), ['category name', 'category', 'name', 'bar category', 'bar category name'])) {
                    $headerSkipped = true;
                    continue;
                }

                if ($rawName === '') {
                    continue;
                }

                if (in_array(strtolower($rawName), array_map('strtolower', $categoryNames))) {
                    continue;
                }

                if (in_array(strtolower($rawName), $existingCategories)) {
                    continue;
                }

                $categoryNames[] = $rawName;
            }

            if (empty($categoryNames)) {
                return redirect()->back()->with('error', 'No new valid bar categories found in the uploaded file.');
            }

            $availableSlots = ($maxCategories > 0) ? max(0, $maxCategories - $currentCategoriesCount) : count($categoryNames);
            $toInsert = array_slice($categoryNames, 0, $availableSlots);
            $skippedCount = count($categoryNames) - count($toInsert);

            if (empty($toInsert)) {
                return redirect()->back()->with('error', "Category limit reached! Your plan allows a maximum of {$maxCategories} categories.");
            }

            $successCount = 0;
            \DB::beginTransaction();
            try {
                foreach ($toInsert as $catName) {
                    $new = new Category();
                    $new->name = $catName;
                    $new->category_type = 'BAR'; // Force BAR
                    $new->user_id = auth()->user()->id;
                    $new->restaurant_id = $restaurantId;
                    $new->status = 'A';
                    $new->save();

                    $new->slug = Str::slug($catName) . '-' . $new->id;
                    $new->save();

                    $successCount++;
                }
                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
                return redirect()->back()->with('error', 'Failed to insert categories: ' . $e->getMessage());
            }

            if ($skippedCount > 0) {
                return redirect()->back()->with('warning', "Successfully added {$successCount} Bar categories. Reached plan limit of {$maxCategories} categories ({$skippedCount} skipped).");
            }

            return redirect()->back()->with('success', "Successfully imported {$successCount} Bar categories with category_type BAR!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing spreadsheet: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for Bar category bulk upload
     */
    public function downloadCategoryTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Category Name');

        // Sample Bar categories
        $sheet->setCellValue('A2', 'Cocktails & Mixology');
        $sheet->setCellValue('A3', 'Beers & Draughts');
        $sheet->setCellValue('A4', 'Single Malt Whiskeys');
        $sheet->setCellValue('A5', 'Vodka & Gin');
        $sheet->setCellValue('A6', 'Wines & Champagnes');
        $sheet->setCellValue('A7', 'Mocktails & Soft Drinks');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF8B5CF6'], // Elegant Purple / Bar Tone
            ],
        ];

        $sheet->getStyle('A1')->applyFromArray($headerStyle);
        $sheet->getColumnDimension('A')->setAutoSize(true);

        $writer = new Xlsx($spreadsheet);
        $filename = "bar_category_upload_template.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
