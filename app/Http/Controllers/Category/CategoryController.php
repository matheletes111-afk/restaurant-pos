<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Plan;
use App\Models\SubCategory;
use App\Models\Subscription;
use App\Models\Exam;
use Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class CategoryController extends Controller
{
    public function index()
    {
        $data = [];
        $data['data'] = Category::where('status','!=','D')
            ->where('restaurant_id',auth()->user()->restaurant_id)
            ->withCount(['subcategories' => function($query) {
                $query->where('status', '!=', 'D');
            }])
            ->get();
        $subRestaurantId = auth()->user()->getSubscriptionRestaurantId() ?? auth()->user()->restaurant_id;
        $check_plan = Subscription::where('user_id', $subRestaurantId)->active()->first();
        $data['plan_details'] = $check_plan ? Plan::where('id',$check_plan->plan_id)->first() : null;
        return view('category_admin',$data);
    }

    public function insert(Request $request)
    {
        \Log::info('Category Insert Request Data:', $request->except('image'));
        $request->validate([
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ], [
            'image.image' => 'The category file must be an image.',
            'image.mimes' => 'The category image must be a file of type: jpg, jpeg, png, webp, gif.',
            'image.max'   => 'The category image must not be greater than 5MB.',
        ]);

        try {
            $new = new Category;
            $new->name = $request->name;
            $new->user_id = auth()->user()->id;
            $new->restaurant_id = auth()->user()->restaurant_id;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '-' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                \Log::info('Category Insert: Uploading image', ['filename' => $filename]);
                //real image
                $image->move(storage_path('app/public/category'),$filename);    
                $new->image = $filename;
            }
            $new->save();
            $upd = [];
            $upd['slug'] = Str::slug($request->name).'-'.$new->id;
            Category::where('id',$new->id)->update($upd);
            \Log::info('Category Insert Success:', ['id' => $new->id]);
            return redirect()->back()->with('success','Category inserted successfully');
        } catch (\Exception $e) {
            \Log::error('Category Insert Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to insert category: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        \Log::info('Category Update Request Data:', $request->except('image'));
        $request->validate([
            'id'    => 'required',
            'name'  => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ], [
            'image.image' => 'The category file must be an image.',
            'image.mimes' => 'The category image must be a file of type: jpg, jpeg, png, webp, gif.',
            'image.max'   => 'The category image must not be greater than 5MB.',
        ]);

        try {
            $upd = [];
            $upd['name'] = $request->name;
            $upd['slug'] = Str::slug($request->name).'-'.$request->id;
            if ($request->hasFile('image')) {
                $check = Category::where('id',$request->id)->first();
                if ($check && $check->image) {
                    $oldImagePath = storage_path('app/public/category/'.$check->image);
                    \Log::info('Category Update: Unlinking old image', ['path' => $oldImagePath]);
                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath);
                    }
                }
                $image = $request->file('image');
                $filename = time() . '-' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                \Log::info('Category Update: Uploading image', ['filename' => $filename]);
                //real image
                $image->move(storage_path('app/public/category'),$filename);    
                $upd['image'] = $filename;
            }
            Category::where('id',$request->id)->update($upd);
            \Log::info('Category Update Success:', ['id' => $request->id]);
            return redirect()->back()->with('success','Category updated successfully');
        } catch (\Exception $e) {
            \Log::error('Category Update Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $check = Category::where('id',$id)->where('restaurant_id',auth()->user()->restaurant_id)->first();
            if ($check) {
                if ($check->image && file_exists(storage_path('app/public/category/'.$check->image))) {
                    @unlink(storage_path('app/public/category/'.$check->image));
                }
                Category::where('id',$id)->update(['status'=>'D']);
                return redirect()->back()->with('success','Category deleted successfully');
            }
            return redirect()->back()->with('error','Category not found or unauthorized access');
        } catch (\Exception $e) {
            \Log::error('Category Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error','Failed to delete category');
        }
    }

    public function subCategory($id)
    {
        $data = [];
        $restaurantId = auth()->user()->restaurant_id;
        $data['data'] = SubCategory::where('category_id',$id)->where('restaurant_id',$restaurantId)->where('status','!=','D')->get();
        $data['details'] = Category::where('id',$id)->where('restaurant_id',$restaurantId)->first();
        if ($data['details']=="") {
           return redirect()->back()->with('error','Unauthorized Access');
        }
        $subRestaurantId = auth()->user()->getSubscriptionRestaurantId() ?? $restaurantId;
        $check_plan = Subscription::where('user_id', $subRestaurantId)->active()->first();
        $data['plan_details'] = $check_plan ? Plan::where('id',$check_plan->plan_id)->first() : null;
        
        // Total active dishes across the ENTIRE restaurant (all categories)
        $data['total_dishes'] = SubCategory::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->count();

        $data['id'] = $id;
        return view('sub_index',$data);
    }

    public function subCategoryinsert(Request $request)
    {
        \Log::info('SubCategory Insert Request Data:', $request->except('image'));
        $request->validate([
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'food_type'   => 'required',
            'category_id' => 'required',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ], [
            'image.image' => 'The food item file must be an image.',
            'image.mimes' => 'The food item image must be a file of type: jpg, jpeg, png, webp, gif.',
            'image.max'   => 'The food item image must not be greater than 5MB.',
        ]);

        $restaurantId = auth()->user()->restaurant_id;
        $subRestaurantId = auth()->user()->getSubscriptionRestaurantId() ?? $restaurantId;
        $check_plan = Subscription::where('user_id', $subRestaurantId)->active()->first();
        $plan_details = $check_plan ? Plan::where('id', $check_plan->plan_id)->first() : null;

        // Check overall restaurant dish limit (across all categories)
        if ($plan_details && !empty($plan_details->total_number_of_dishes) && $plan_details->total_number_of_dishes > 0) {
            $totalRestaurantDishes = SubCategory::where('restaurant_id', $restaurantId)
                ->where('status', '!=', 'D')
                ->count();

            if ($totalRestaurantDishes >= (int)$plan_details->total_number_of_dishes) {
                return redirect()->back()->with('error', "Dish limit reached! Your plan allows a maximum of {$plan_details->total_number_of_dishes} dishes for your restaurant overall ({$totalRestaurantDishes} currently used across all categories). Please upgrade your plan to add more dishes.");
            }
        }

        try {
            $new = new SubCategory;
            $new->name = $request->name;
            $new->price = $request->price;
            $new->gst_rate = $request->gst_rate ?? 0;
            $new->food_type = $request->food_type;
            $new->category_id = $request->category_id;
            $new->user_id = auth()->user()->id;
            $new->restaurant_id = auth()->user()->restaurant_id;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '-' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                \Log::info('SubCategory Insert: Uploading image', ['filename' => $filename]);
                //real image
                $image->move(storage_path('app/public/category'),$filename);    
                $new->image = $filename;
            }
            $new->save();
            \Log::info('SubCategory Insert Success:', ['id' => $new->id]);
            return redirect()->back()->with('success','Product inserted successfully');
        } catch (\Exception $e) {
            \Log::error('SubCategory Insert Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to insert product: ' . $e->getMessage());
        }
    }

    public function subCategoryupdate(Request $request)
    {
        \Log::info('SubCategory Update Request Data:', $request->except('image'));
        $request->validate([
            'id'        => 'required',
            'name'      => 'required|string|max:255',
            'price'     => 'required|numeric|min:0',
            'food_type' => 'required',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ], [
            'image.image' => 'The food item file must be an image.',
            'image.mimes' => 'The food item image must be a file of type: jpg, jpeg, png, webp, gif.',
            'image.max'   => 'The food item image must not be greater than 5MB.',
        ]);

        try {
            $upd = [];
            $upd['name'] = $request->name;
            $upd['price'] = $request->price;
            $upd['gst_rate'] = $request->gst_rate ?? 0;
            $upd['food_type'] = $request->food_type;
            if ($request->hasFile('image')) {
                $check = SubCategory::where('id',$request->id)->first();
                if ($check && $check->image) {
                    $oldImagePath = storage_path('app/public/category/'.$check->image);
                    \Log::info('SubCategory Update: Unlinking old image', ['path' => $oldImagePath]);
                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath);
                    }
                }
                $image = $request->file('image');
                $filename = time() . '-' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                \Log::info('SubCategory Update: Uploading image', ['filename' => $filename]);
                //real image
                $image->move(storage_path('app/public/category'),$filename);    
                $upd['image'] = $filename;
            }
            SubCategory::where('id',$request->id)->update($upd);
            \Log::info('SubCategory Update Success:', ['id' => $request->id]);
            return redirect()->back()->with('success','Product updated successfully');
        } catch (\Exception $e) {
            \Log::error('SubCategory Update Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    public function subCategorydelete($id)
    {
        try {
            $check = SubCategory::where('id',$id)->where('restaurant_id',auth()->user()->restaurant_id)->first();
            if ($check) {
                if ($check->image) {
                    @unlink(storage_path('app/public/category/'.$check->image));
                }
                SubCategory::where('id',$id)->where('restaurant_id',auth()->user()->restaurant_id)->update(['status'=>'D']);
                return redirect()->back()->with('success','Product deleted successfully');
            }
            return redirect()->back()->with('error','Product not found or unauthorized access');
        } catch (\Exception $e) {
            \Log::error('SubCategory Delete Error: ' . $e->getMessage());
            return redirect()->back()->with('error','Failed to delete product');
        }
    }

    public function subCategorystatus($id)
    {
        $product = SubCategory::find($id);
        if ($product) {
            $product->status = $product->status === 'A' ? 'I' : 'A';
            $product->save();
            return redirect()->back()->with('success', 'Status updated successfully.');
        }
        return redirect()->back()->with('error', 'Record not found.');
    }

    public function bulkUploadCategory(Request $request)
    {
        $request->validate([
            'bulk_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $restaurantId = auth()->user()->restaurant_id;
        $subRestaurantId = auth()->user()->getSubscriptionRestaurantId() ?? $restaurantId;
        $check_plan = Subscription::where('user_id', $subRestaurantId)->active()->first();
        $plan_details = $check_plan ? Plan::where('id', $check_plan->plan_id)->first() : null;

        $maxCategories = ($plan_details && !empty($plan_details->category_number)) ? (int)$plan_details->category_number : 0;
        $currentCategoriesCount = Category::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->count();

        // Check if category limit already reached
        if ($maxCategories > 0 && $currentCategoriesCount >= $maxCategories) {
            return redirect()->back()->with('error', "Category limit reached! Your restaurant currently has {$currentCategoriesCount} / {$maxCategories} categories. Please upgrade your plan to add more categories.");
        }

        try {
            $file = $request->file('bulk_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $categoryNames = [];
            $headerSkipped = false;

            // Existing category names lowercased for deduplication
            $existingCategories = Category::where('restaurant_id', $restaurantId)
                ->where('status', '!=', 'D')
                ->pluck('name')
                ->map(fn($n) => strtolower(trim($n)))
                ->toArray();

            foreach ($rows as $index => $row) {
                if (empty(array_filter($row, fn($val) => !is_null($val) && trim((string)$val) !== ''))) {
                    continue;
                }

                $rawName = trim((string)($row[0] ?? ''));

                // Skip header row
                if (!$headerSkipped && in_array(strtolower($rawName), ['category name', 'category', 'name'])) {
                    $headerSkipped = true;
                    continue;
                }

                if ($rawName === '') {
                    continue;
                }

                // Skip duplicates within file batch
                if (in_array(strtolower($rawName), array_map('strtolower', $categoryNames))) {
                    continue;
                }

                // Skip if category already exists in database
                if (in_array(strtolower($rawName), $existingCategories)) {
                    continue;
                }

                $categoryNames[] = $rawName;
            }

            if (empty($categoryNames)) {
                return redirect()->back()->with('error', 'No new valid categories found to upload. Please ensure category names are not empty or already existing.');
            }

            // Enforce plan limits strictly
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
                return redirect()->back()->with('warning', "Successfully added {$successCount} categories. Reached your plan limit of {$maxCategories} categories ({$skippedCount} categories skipped).");
            }

            return redirect()->back()->with('success', "Successfully imported {$successCount} categories!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing file: ' . $e->getMessage());
        }
    }

    public function downloadCategoryTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Single Column Header: Category Name
        $sheet->setCellValue('A1', 'Category Name');

        // Sample Data
        $sheet->setCellValue('A2', 'Starters');
        $sheet->setCellValue('A3', 'Main Course');
        $sheet->setCellValue('A4', 'Beverages');
        $sheet->setCellValue('A5', 'Desserts');
        $sheet->setCellValue('A6', 'Breads');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFF5E14']
            ]
        ];

        $sheet->getStyle('A1')->applyFromArray($headerStyle);
        $sheet->getColumnDimension('A')->setWidth(30);

        $writer = new Xlsx($spreadsheet);
        $filename = "category_bulk_upload_template.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function bulkUpload(Request $request)
    {
        $request->validate([
            'bulk_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $restaurantId = auth()->user()->restaurant_id;
        $subRestaurantId = auth()->user()->getSubscriptionRestaurantId() ?? $restaurantId;
        $check_plan = Subscription::where('user_id', $subRestaurantId)->active()->first();
        $plan_details = $check_plan ? Plan::where('id', $check_plan->plan_id)->first() : null;

        $maxDishes = ($plan_details && !empty($plan_details->total_number_of_dishes)) ? (int)$plan_details->total_number_of_dishes : 0;
        $currentTotalDishes = SubCategory::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->count();

        // Check if restaurant has already reached or exceeded overall dish limit
        if ($maxDishes > 0 && $currentTotalDishes >= $maxDishes) {
            return redirect()->back()->with('error', "Dish limit reached! Your restaurant currently has {$currentTotalDishes} / {$maxDishes} dishes across all categories. Please upgrade your plan to upload more dishes.");
        }

        // Active categories mapping for this restaurant
        $categories = Category::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->get();

        $catMap = [];
        foreach ($categories as $cat) {
            $catMap[strtolower(trim($cat->name))] = $cat->id;
        }

        // Optional fallback category if uploaded from a specific category page
        $fallbackCategory = null;
        if ($request->filled('category_id')) {
            $fallbackCategory = Category::where('id', $request->category_id)
                ->where('restaurant_id', $restaurantId)
                ->where('status', '!=', 'D')
                ->first();
        }

        try {
            $file = $request->file('bulk_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $itemsToInsert = [];
            $errors = [];
            $headerSkipped = false;

            foreach ($rows as $index => $row) {
                // Skip completely empty rows
                if (empty(array_filter($row, fn($val) => !is_null($val) && trim((string)$val) !== ''))) {
                    continue;
                }

                $rowNum = $index + 1;
                $firstCol = trim((string)($row[0] ?? ''));

                // Skip header row
                if (!$headerSkipped && strtolower($firstCol) === 'product name') {
                    $headerSkipped = true;
                    continue;
                }

                $name = trim((string)($row[0] ?? ''));
                $priceRaw = $row[1] ?? null;
                $price = is_null($priceRaw) ? '' : trim((string)$priceRaw);
                $foodTypeRaw = trim((string)($row[2] ?? ''));
                $foodType = strtoupper($foodTypeRaw);

                // Determine Category Name (4-column format: Product Name, Price, Food Type, Category)
                $categoryName = isset($row[3]) ? trim((string)$row[3]) : '';
                if ($categoryName === '' && $fallbackCategory) {
                    $categoryName = $fallbackCategory->name;
                }

                // Row-level validations
                if ($name === '') {
                    $errors[] = "Row {$rowNum}: Product Name is required.";
                    continue;
                }

                if ($price === '' || !is_numeric($price) || (float)$price < 0) {
                    $errors[] = "Row {$rowNum} ('{$name}'): Invalid price '{$priceRaw}'. Price must be a valid non-negative number.";
                    continue;
                }

                if (!in_array($foodType, ['VEG', 'NON-VEG'])) {
                    $errors[] = "Row {$rowNum} ('{$name}'): Food Type must be 'VEG' or 'NON-VEG' (found '{$foodTypeRaw}').";
                    continue;
                }

                if ($categoryName === '') {
                    $errors[] = "Row {$rowNum} ('{$name}'): Category name is missing. Please provide an active category name.";
                    continue;
                }

                // Strict Category Match Check
                $catKey = strtolower($categoryName);
                if (!isset($catMap[$catKey])) {
                    $errors[] = "Row {$rowNum} ('{$name}'): Category '{$categoryName}' does not exist for your restaurant.";
                    continue;
                }

                $targetCategoryId = $catMap[$catKey];

                $itemsToInsert[] = [
                    'name' => $name,
                    'price' => (float)$price,
                    'food_type' => $foodType,
                    'category_id' => $targetCategoryId,
                    'row' => $rowNum,
                ];
            }

            // IF ANY ERROR OCCURRED: DO NOT INSERT ANYTHING, RETURN CLEAR ERROR
            if (!empty($errors)) {
                $errorMsg = "<strong>Bulk upload failed! No dishes were inserted because of the following error(s):</strong><br><br>" . implode("<br>", $errors);
                return redirect()->back()->with('error', $errorMsg);
            }

            if (empty($itemsToInsert)) {
                return redirect()->back()->with('error', 'No valid dish rows found in the uploaded file.');
            }

            // Check overall restaurant dish limit for all items in file
            if ($maxDishes > 0 && ($currentTotalDishes + count($itemsToInsert)) > $maxDishes) {
                $uploadCount = count($itemsToInsert);
                $available = max(0, $maxDishes - $currentTotalDishes);
                return redirect()->back()->with('error', "Bulk upload failed! Overall restaurant dish limit exceeded. Your plan allows a maximum of {$maxDishes} dishes. You currently have {$currentTotalDishes} dishes and can only add {$available} more (file contains {$uploadCount} dishes). No dishes were inserted.");
            }

            // All validations passed -> insert in transaction
            \DB::beginTransaction();
            try {
                foreach ($itemsToInsert as $item) {
                    $subCategory = new SubCategory();
                    $subCategory->name = $item['name'];
                    $subCategory->price = $item['price'];
                    $subCategory->gst_rate = 0;
                    $subCategory->food_type = $item['food_type'];
                    $subCategory->category_id = $item['category_id'];
                    $subCategory->user_id = auth()->user()->id;
                    $subCategory->restaurant_id = $restaurantId;
                    $subCategory->status = 'A';
                    $subCategory->save();
                }
                \DB::commit();
                return redirect()->back()->with('success', "Bulk upload successful! Successfully added " . count($itemsToInsert) . " dishes.");
            } catch (\Exception $e) {
                \DB::rollBack();
                return redirect()->back()->with('error', 'Database error during upload: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing file: ' . $e->getMessage());
        }
    }

    public function downloadTemplate($id = null)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 4 Columns: Product Name, Price (₹), Food Type, Category
        $sheet->setCellValue('A1', 'Product Name');
        $sheet->setCellValue('B1', 'Price (₹)');
        $sheet->setCellValue('C1', 'Food Type (VEG/NON-VEG)');
        $sheet->setCellValue('D1', 'Category');

        // Fetch existing categories of current restaurant to populate sample
        $restaurantId = auth()->user()->restaurant_id;
        $categoryNames = Category::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->pluck('name')
            ->toArray();

        $cat1 = $categoryNames[0] ?? 'Starters';
        $cat2 = $categoryNames[1] ?? ($categoryNames[0] ?? 'Main Course');

        // If specific category ID passed, use that category name
        if ($id) {
            $specificCat = Category::where('id', $id)->where('restaurant_id', $restaurantId)->first();
            if ($specificCat) {
                $cat1 = $specificCat->name;
                $cat2 = $specificCat->name;
            }
        }

        // Add sample data
        $sheet->setCellValue('A2', 'Paneer Butter Masala');
        $sheet->setCellValue('B2', 250);
        $sheet->setCellValue('C2', 'VEG');
        $sheet->setCellValue('D2', $cat1);

        $sheet->setCellValue('A3', 'Chicken Biryani');
        $sheet->setCellValue('B3', 320);
        $sheet->setCellValue('C3', 'NON-VEG');
        $sheet->setCellValue('D3', $cat2);

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFF5E14']
            ]
        ];

        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        foreach (['A', 'B', 'C', 'D'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "dishes_bulk_upload_template.xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
