<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DishAddon;
use App\Models\RestaurantMaster;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DishAddonController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of dish addons.
     */
    public function index(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $query = DishAddon::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->with(['dishes' => function($q) {
                $q->select('sub_category.id', 'sub_category.name', 'sub_category.price', 'sub_category.food_type', 'sub_category.category_id')
                  ->where('sub_category.status', '!=', 'D');
            }]);

        // Search Filter (Addon name, description, or mapped dish name)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('dishes', function ($dq) use ($search) {
                      $dq->where('sub_category.name', 'like', "%{$search}%")
                         ->where('sub_category.status', '!=', 'D');
                  });
            });
        }

        // Mapped Dish Dropdown Filter
        if ($request->filled('dish_id')) {
            $dishId = (int)$request->dish_id;
            $query->whereHas('dishes', function ($dq) use ($dishId) {
                $dq->where('sub_category.id', $dishId)
                   ->where('sub_category.status', '!=', 'D');
            });
        }

        // Food Type Filter
        if ($request->filled('food_type') && in_array(strtoupper($request->food_type), ['VEG', 'NON-VEG'])) {
            $query->where('food_type', strtoupper($request->food_type));
        }

        // Status Filter
        if ($request->filled('status') && in_array(strtoupper($request->status), ['A', 'I'])) {
            $query->where('status', strtoupper($request->status));
        }

        $addons = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Stats counts for header summary
        $statsBase = DishAddon::where('restaurant_id', $restaurantId)->where('status', '!=', 'D');
        $totalCount = (clone $statsBase)->count();
        $vegCount = (clone $statsBase)->where('food_type', 'VEG')->count();
        $nonVegCount = (clone $statsBase)->where('food_type', 'NON-VEG')->count();
        $activeCount = (clone $statsBase)->where('status', 'A')->count();

        // Fetch all categories and dishes for mapping modal
        $categories = \App\Models\Category::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->with(['subcategories' => function($q) {
                $q->where('status', '!=', 'D')->orderBy('name', 'asc');
            }])
            ->orderBy('name', 'asc')
            ->get();

        return view('dish_addon.index', compact(
            'addons',
            'totalCount',
            'vegCount',
            'nonVegCount',
            'activeCount',
            'categories'
        ));
    }

    /**
     * Store a newly created dish addon.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191',
            'price' => 'required|numeric|min:0',
            'food_type' => 'required|string|in:VEG,NON-VEG,veg,non-veg',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|in:A,I',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        $addon = DishAddon::create([
            'restaurant_id' => $restaurantId,
            'user_id' => auth()->id(),
            'name' => trim($request->name),
            'description' => $request->description ? trim($request->description) : null,
            'price' => (float)$request->price,
            'food_type' => strtoupper(trim($request->food_type)),
            'status' => $request->status ? strtoupper($request->status) : 'A',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dish addon created successfully!',
                'addon' => $addon
            ]);
        }

        return redirect()->route('addon.index')->with('success', 'Dish addon added successfully!');
    }

    /**
     * Return JSON for editing.
     */
    public function edit($id)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $addon = DishAddon::where('restaurant_id', $restaurantId)
            ->where('id', $id)
            ->where('status', '!=', 'D')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'addon' => $addon
        ]);
    }

    /**
     * Update the specified dish addon.
     */
    public function update(Request $request, $id)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $addon = DishAddon::where('restaurant_id', $restaurantId)
            ->where('id', $id)
            ->where('status', '!=', 'D')
            ->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:191',
            'price' => 'required|numeric|min:0',
            'food_type' => 'required|string|in:VEG,NON-VEG,veg,non-veg',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|in:A,I',
        ]);

        $addon->update([
            'name' => trim($request->name),
            'description' => $request->description ? trim($request->description) : null,
            'price' => (float)$request->price,
            'food_type' => strtoupper(trim($request->food_type)),
            'status' => $request->status ? strtoupper($request->status) : $addon->status,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dish addon updated successfully!',
                'addon' => $addon
            ]);
        }

        return redirect()->route('addon.index')->with('success', 'Dish addon updated successfully!');
    }

    /**
     * Toggle active / inactive status.
     */
    public function toggleStatus($id)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $addon = DishAddon::where('restaurant_id', $restaurantId)
            ->where('id', $id)
            ->where('status', '!=', 'D')
            ->firstOrFail();

        $newStatus = ($addon->status === 'A') ? 'I' : 'A';
        $addon->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => 'Addon status updated to ' . ($newStatus === 'A' ? 'Active' : 'Inactive')
        ]);
    }

    /**
     * Remove the specified dish addon (Soft Delete).
     */
    public function destroy($id)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $addon = DishAddon::where('restaurant_id', $restaurantId)
            ->where('id', $id)
            ->where('status', '!=', 'D')
            ->firstOrFail();

        $addon->update(['status' => 'D']);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dish addon deleted successfully!'
            ]);
        }

        return redirect()->route('addon.index')->with('success', 'Dish addon deleted successfully!');
    }

    /**
     * Get all categories and dishes with mapped status for a specific addon.
     */
    public function getDishes($id)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $addon = DishAddon::where('restaurant_id', $restaurantId)
            ->where('id', $id)
            ->where('status', '!=', 'D')
            ->firstOrFail();

        $mappedDishIds = $addon->dishes()->pluck('sub_category.id')->toArray();

        $categories = \App\Models\Category::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->with(['subcategories' => function($q) {
                $q->where('status', '!=', 'D')->orderBy('name', 'asc');
            }])
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'addon' => [
                'id' => $addon->id,
                'name' => $addon->name,
                'food_type' => $addon->food_type,
                'price' => $addon->price,
            ],
            'mapped_dish_ids' => $mappedDishIds,
            'categories' => $categories
        ]);
    }

    /**
     * Map dishes to a specific addon.
     */
    public function mapDishes(Request $request, $id)
    {
        $restaurantId = auth()->user()->restaurant_id;
        $addon = DishAddon::where('restaurant_id', $restaurantId)
            ->where('id', $id)
            ->where('status', '!=', 'D')
            ->firstOrFail();

        $request->validate([
            'dish_ids' => 'nullable|array',
            'dish_ids.*' => 'integer',
        ]);

        $dishIds = $request->input('dish_ids', []);
        
        // Ensure only dishes belonging to this restaurant and not deleted are mapped
        $validDishIds = \App\Models\SubCategory::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->whereIn('id', $dishIds)
            ->pluck('id')
            ->toArray();

        $addon->dishes()->sync($validDishIds);

        // Fetch refreshed mapped dishes for clean UI response
        $mappedDishes = $addon->dishes()
            ->select('sub_category.id', 'sub_category.name')
            ->where('sub_category.status', '!=', 'D')
            ->orderBy('sub_category.name', 'asc')
            ->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Dishes mapped to "' . $addon->name . '" successfully!',
                'mapped_dishes' => $mappedDishes,
                'count' => count($validDishIds),
                'names_string' => $mappedDishes->pluck('name')->implode(', ')
            ]);
        }

        return redirect()->route('addon.index')->with('success', 'Dishes mapped successfully to ' . $addon->name . '!');
    }

    /**
     * Download Excel Sample Template for Dish Addon Bulk Upload.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Dish Addons');

        // Header Row
        $sheet->setCellValue('A1', 'Addon Name');
        $sheet->setCellValue('B1', 'Base Price (₹)');
        $sheet->setCellValue('C1', 'Food Type (VEG/NON-VEG)');
        $sheet->setCellValue('D1', 'Description (Optional)');

        // Sample Rows
        $sheet->setCellValue('A2', 'Extra Cheese Slice');
        $sheet->setCellValue('B2', 30);
        $sheet->setCellValue('C2', 'VEG');
        $sheet->setCellValue('D2', 'Melted cheddar cheese slice');

        $sheet->setCellValue('A3', 'Extra Peri Peri Dip');
        $sheet->setCellValue('B3', 25);
        $sheet->setCellValue('C3', 'VEG');
        $sheet->setCellValue('D3', 'Spicy peri peri sauce');

        $sheet->setCellValue('A4', 'Extra Chicken Patty');
        $sheet->setCellValue('B4', 80);
        $sheet->setCellValue('C4', 'NON-VEG');
        $sheet->setCellValue('D4', 'Crispy fried chicken patty');

        $sheet->setCellValue('A5', 'Smoked Bacon Strips');
        $sheet->setCellValue('B5', 65);
        $sheet->setCellValue('C5', 'NON-VEG');
        $sheet->setCellValue('D5', 'Double smoked crispy bacon');

        // Header Styling
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFF5E14']
            ]
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        foreach (['A', 'B', 'C', 'D'] as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = "dish_addons_template.xlsx";

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Bulk Upload Dish Addons via Excel / CSV.
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'bulk_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        try {
            $file = $request->file('bulk_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $addonsToInsert = [];
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
                if (!$headerSkipped && (
                    strtolower($firstCol) === 'addon name' || 
                    strtolower($firstCol) === 'name' || 
                    strtolower($firstCol) === 'product name'
                )) {
                    $headerSkipped = true;
                    continue;
                }

                $name = trim((string)($row[0] ?? ''));
                $priceRaw = $row[1] ?? null;
                $price = is_null($priceRaw) ? '' : trim((string)$priceRaw);
                $foodTypeRaw = trim((string)($row[2] ?? ''));
                $foodType = strtoupper($foodTypeRaw);
                $description = isset($row[3]) ? trim((string)$row[3]) : null;

                // Validations
                if ($name === '') {
                    $errors[] = "Row {$rowNum}: Addon Name is required.";
                    continue;
                }

                if ($price === '' || !is_numeric($price) || (float)$price < 0) {
                    $errors[] = "Row {$rowNum} ('{$name}'): Invalid price '{$priceRaw}'. Base price must be a valid non-negative number.";
                    continue;
                }

                // Default food type if empty to VEG
                if ($foodType === '') {
                    $foodType = 'VEG';
                }

                if (!in_array($foodType, ['VEG', 'NON-VEG'])) {
                    $errors[] = "Row {$rowNum} ('{$name}'): Food Type must be 'VEG' or 'NON-VEG' (found '{$foodTypeRaw}').";
                    continue;
                }

                $addonsToInsert[] = [
                    'restaurant_id' => $restaurantId,
                    'user_id' => auth()->id(),
                    'name' => $name,
                    'price' => (float)$price,
                    'food_type' => $foodType,
                    'description' => $description ?: null,
                    'status' => 'A',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Return all validation errors if any occurred
            if (!empty($errors)) {
                $errorMsg = "<strong>Bulk upload failed! No addons were inserted because of the following error(s):</strong><br><br>" . implode("<br>", $errors);
                return redirect()->back()->with('error', $errorMsg);
            }

            if (empty($addonsToInsert)) {
                return redirect()->back()->with('error', 'No valid addon rows found in the uploaded file.');
            }

            DB::beginTransaction();
            try {
                foreach ($addonsToInsert as $addonData) {
                    DishAddon::create($addonData);
                }
                DB::commit();

                return redirect()->back()->with('success', "Bulk upload successful! Successfully added " . count($addonsToInsert) . " dish addons.");
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Database error during upload: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error processing file: ' . $e->getMessage());
        }
    }
}
