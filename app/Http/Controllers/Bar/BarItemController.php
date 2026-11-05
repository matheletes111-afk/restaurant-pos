<?php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\BarBrand;
use App\Models\BarItemVariation;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BarItemController extends Controller
{
    /**
     * Display Bar Items listing (optional filter by bar category)
     */
    public function index(Request $request, $category_id = null)
    {
        $restaurantId = auth()->user()->restaurant_id;

        // Query active bar categories
        $barCategories = Category::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->where('category_type', 'BAR')
            ->orderBy('name', 'asc')
            ->get();

        $brands = BarBrand::where('restaurant_id', $restaurantId)
            ->where('status', 'A')
            ->orderBy('name', 'asc')
            ->get();

        $query = SubCategory::with(['category', 'brand', 'variations'])
            ->where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->whereHas('category', function($q) {
                $q->where('category_type', 'BAR');
            });

        $selectedCategory = null;
        if ($category_id) {
            $selectedCategory = Category::where('id', $category_id)
                ->where('restaurant_id', $restaurantId)
                ->where('category_type', 'BAR')
                ->first();

            if ($selectedCategory) {
                $query->where('category_id', $selectedCategory->id);
            }
        }

        $items = $query->orderBy('id', 'desc')->get();

        // Calculate KPI Statistics
        $totalItems = $items->count();
        $availableItems = $items->where('status', 'A')->count();
        $unavailableItems = $items->where('status', '!=', 'A')->count();
        $totalStockMl = $items->sum('current_stock_ml');
        $totalOpeningMl = $items->sum('opening_qty');
        $itemsWithVariations = $items->filter(fn($i) => $i->variations->count() > 0)->count();

        $stats = [
            'total_items'           => $totalItems,
            'available_items'       => $availableItems,
            'unavailable_items'     => $unavailableItems,
            'total_stock_ml'        => $totalStockMl,
            'total_opening_ml'      => $totalOpeningMl,
            'items_with_variations' => $itemsWithVariations,
        ];

        $restaurant = \App\Models\RestaurantMaster::find($restaurantId);
        $defaultVatRate = floatval($restaurant->vat_percentage ?? 0);

        return view('bar.item_index', compact('items', 'barCategories', 'brands', 'selectedCategory', 'stats', 'defaultVatRate'));
    }

    /**
     * Store a newly created Bar Item
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'               => 'required|string|max:255',
            'category_id'        => 'required|exists:category,id',
            'brand_id'           => 'nullable|exists:bar_brands,id',
            'bottle_size_ml'     => 'required|numeric|min:1',
            'price'              => 'required|numeric|min:0',
            'price_30ml'         => 'nullable|numeric|min:0',
            'price_60ml'         => 'nullable|numeric|min:0',
            'price_90ml'         => 'nullable|numeric|min:0',
            'opening_bottle_qty' => 'nullable|numeric|min:0',
            'extra_ml'           => 'nullable|numeric|min:0',
            'opening_qty'        => 'nullable|numeric|min:0',
            'vat_rate'           => 'nullable|numeric|min:0|max:100',
            'description'        => 'nullable|string|max:1000',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ], [
            'name.required'           => 'Please enter the bar item name.',
            'category_id.required'    => 'Please select a bar category.',
            'bottle_size_ml.required' => 'Please enter the standard bottle size in ml (e.g. 750).',
            'price.required'          => 'Please enter the full bottle price.',
            'price.numeric'           => 'Full bottle price must be a valid number.',
            'price.min'               => 'Full bottle price cannot be negative.',
            'price_30ml.numeric'      => '30ml price must be a valid number.',
            'price_60ml.numeric'      => '60ml price must be a valid number.',
            'price_90ml.numeric'      => '90ml price must be a valid number.',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        try {
            $bottleSize = floatval($request->bottle_size_ml);

            // Calculate opening ML: (Bottle Size * Opening Bottle Qty) + Extra ML
            if ($request->filled('opening_bottle_qty') || $request->filled('extra_ml')) {
                $bottleQty = floatval($request->opening_bottle_qty ?? 0);
                $extraMl = floatval($request->extra_ml ?? 0);
                $openingMl = ($bottleSize * $bottleQty) + $extraMl;
            } else {
                $openingMl = floatval($request->opening_qty ?? 0);
            }

            $currentStockMl = $openingMl;
            $bottleCount = $bottleSize > 0 ? round($openingMl / $bottleSize, 1) : 0;

            $restaurant = \App\Models\RestaurantMaster::find($restaurantId);
            $defaultVatRate = floatval($restaurant->vat_percentage ?? 0);
            $vatRate = $request->filled('vat_rate') ? floatval($request->vat_rate) : $defaultVatRate;

            $item = new SubCategory();
            $item->name = trim($request->name);
            $item->category_id = $request->category_id;
            $item->brand_id = $request->brand_id ?: null;
            $item->bottle_size_ml = $bottleSize;
            $item->opening_qty = $openingMl;
            $item->current_stock_ml = $currentStockMl;
            $item->price = $request->price ? floatval($request->price) : 0;
            $item->vat_rate = $vatRate;
            $item->gst_rate = 0.00; // As per Indian law, liquor items are subject to State VAT, not GST
            $item->food_type = 'BAR';
            $item->description = $request->description ? trim($request->description) : null;
            $item->restaurant_id = $restaurantId;
            $item->user_id = auth()->id();
            $item->status = ($request->filled('status') && $request->status === 'I') ? 'I' : 'A';

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = 'bar-item-' . time() . '-' . rand(100, 999) . '.' . $image->getClientOriginalExtension();
                $image->move(storage_path('app/public/category'), $filename);
                $item->image = $filename;
            }

            $item->save();

            // Automatically sync/update "Full Bottle" serving variation with full bottle ml and price
            $bottleVar = BarItemVariation::where('sub_category_id', $item->id)
                ->where('restaurant_id', $restaurantId)
                ->where(function($q) {
                    $q->where('name', 'Full Bottle')
                      ->orWhere('name', 'LIKE', '%Full Bottle%');
                })
                ->first();

            if ($bottleVar) {
                $bottleVar->ml = $bottleSize;
                $bottleVar->price = floatval($request->price);
                $bottleVar->save();
            } else {
                $bottleVar = new BarItemVariation();
                $bottleVar->restaurant_id = $restaurantId;
                $bottleVar->sub_category_id = $item->id;
                $bottleVar->name = 'Full Bottle';
                $bottleVar->ml = $bottleSize;
                $bottleVar->price = floatval($request->price);
                $bottleVar->status = 'A';
                $bottleVar->save();
            }

            // Automatically sync/create default peg variations (30ml, 60ml, 90ml) if prices are provided
            $defaultPegs = [
                ['name' => '30ml Peg', 'ml' => 30, 'price' => $request->price_30ml],
                ['name' => '60ml Large', 'ml' => 60, 'price' => $request->price_60ml],
                ['name' => '90ml Patiala', 'ml' => 90, 'price' => $request->price_90ml],
            ];

            foreach ($defaultPegs as $peg) {
                if ($peg['price'] !== null && $peg['price'] !== '' && is_numeric($peg['price'])) {
                    $pVal = floatval($peg['price']);
                    $existingVar = BarItemVariation::where('sub_category_id', $item->id)
                        ->where('restaurant_id', $restaurantId)
                        ->where(function($q) use ($peg) {
                            $q->where('ml', $peg['ml'])
                              ->orWhere('name', $peg['name'])
                              ->orWhere('name', 'LIKE', '%' . $peg['ml'] . 'ml%');
                        })
                        ->first();

                    if ($existingVar) {
                        $existingVar->name = $peg['name'];
                        $existingVar->ml = $peg['ml'];
                        $existingVar->price = $pVal;
                        $existingVar->status = 'A';
                        $existingVar->save();
                    } else {
                        $newVar = new BarItemVariation();
                        $newVar->restaurant_id = $restaurantId;
                        $newVar->sub_category_id = $item->id;
                        $newVar->name = $peg['name'];
                        $newVar->ml = $peg['ml'];
                        $newVar->price = $pVal;
                        $newVar->status = 'A';
                        $newVar->save();
                    }
                }
            }

            // Sync with Purchase Management products (unit = ML)
            \App\Models\Product::syncBarItems($restaurantId);

            return redirect()->back()->with('success', "Bar Item \"{$item->name}\" created successfully with opening stock of {$currentStockMl} ml ({$bottleCount} bottles eq.) and {$vatRate}% VAT.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save bar item: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing Bar Item
     */
    public function update(Request $request)
    {
        $request->validate([
            'id'                 => 'required|exists:sub_category,id',
            'name'               => 'required|string|max:255',
            'category_id'        => 'required|exists:category,id',
            'brand_id'           => 'nullable|exists:bar_brands,id',
            'bottle_size_ml'     => 'required|numeric|min:1',
            'price'              => 'required|numeric|min:0',
            'price_30ml'         => 'nullable|numeric|min:0',
            'price_60ml'         => 'nullable|numeric|min:0',
            'price_90ml'         => 'nullable|numeric|min:0',
            'opening_bottle_qty' => 'nullable|numeric|min:0',
            'extra_ml'           => 'nullable|numeric|min:0',
            'opening_qty'        => 'nullable|numeric|min:0',
            'vat_rate'           => 'nullable|numeric|min:0|max:100',
            'description'        => 'nullable|string|max:1000',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ], [
            'name.required'           => 'Please enter the bar item name.',
            'category_id.required'    => 'Please select a bar category.',
            'bottle_size_ml.required' => 'Please enter the standard bottle size in ml (e.g. 750).',
            'price.required'          => 'Please enter the full bottle price.',
            'price.numeric'           => 'Full bottle price must be a valid number.',
            'price.min'               => 'Full bottle price cannot be negative.',
            'price_30ml.numeric'      => '30ml price must be a valid number.',
            'price_60ml.numeric'      => '60ml price must be a valid number.',
            'price_90ml.numeric'      => '90ml price must be a valid number.',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        $item = SubCategory::where('id', $request->id)
            ->where('restaurant_id', $restaurantId)
            ->firstOrFail();

        try {
            $bottleSize = floatval($request->bottle_size_ml);

            // Calculate opening ML: (Bottle Size * Opening Bottle Qty) + Extra ML
            if ($request->filled('opening_bottle_qty') || $request->filled('extra_ml')) {
                $bottleQty = floatval($request->opening_bottle_qty ?? 0);
                $extraMl = floatval($request->extra_ml ?? 0);
                $openingMl = ($bottleSize * $bottleQty) + $extraMl;
            } else {
                $openingMl = floatval($request->opening_qty ?? 0);
            }

            $oldStockMl = floatval($item->current_stock_ml);
            $oldOpeningMl = floatval($item->opening_qty);

            // If opening stock changed, adjust current stock accordingly
            if ($oldOpeningMl != $openingMl) {
                $diff = $openingMl - $oldOpeningMl;
                $item->current_stock_ml = max(0, $oldStockMl + $diff);
            }

            $item->name = trim($request->name);
            $item->category_id = $request->category_id;
            $item->brand_id = $request->brand_id ?: null;
            $item->bottle_size_ml = $bottleSize;
            $item->opening_qty = $openingMl;
            $item->price = $request->price ? floatval($request->price) : 0;
            $item->vat_rate = $request->filled('vat_rate') ? floatval($request->vat_rate) : 0.00;
            $item->gst_rate = 0.00; // As per Indian law, liquor items are subject to State VAT, not GST
            $item->description = $request->description ? trim($request->description) : null;
            if ($request->filled('status')) {
                $item->status = $request->status === 'A' ? 'A' : 'I';
            }

            if ($request->hasFile('image')) {
                if ($item->image && file_exists(storage_path('app/public/category/' . $item->image))) {
                    @unlink(storage_path('app/public/category/' . $item->image));
                }
                $image = $request->file('image');
                $filename = 'bar-item-' . time() . '-' . rand(100, 999) . '.' . $image->getClientOriginalExtension();
                $image->move(storage_path('app/public/category'), $filename);
                $item->image = $filename;
            }

            $item->save();

            // Automatically sync/update "Full Bottle" serving variation with full bottle ml and price
            $bottleVar = BarItemVariation::where('sub_category_id', $item->id)
                ->where('restaurant_id', $restaurantId)
                ->where(function($q) {
                    $q->where('name', 'Full Bottle')
                      ->orWhere('name', 'LIKE', '%Full Bottle%');
                })
                ->first();

            if ($bottleVar) {
                $bottleVar->ml = $bottleSize;
                $bottleVar->price = floatval($request->price);
                $bottleVar->save();
            } else {
                $bottleVar = new BarItemVariation();
                $bottleVar->restaurant_id = $restaurantId;
                $bottleVar->sub_category_id = $item->id;
                $bottleVar->name = 'Full Bottle';
                $bottleVar->ml = $bottleSize;
                $bottleVar->price = floatval($request->price);
                $bottleVar->status = 'A';
                $bottleVar->save();
            }

            // Automatically sync/create default peg variations (30ml, 60ml, 90ml) if prices are provided
            $defaultPegs = [
                ['name' => '30ml Peg', 'ml' => 30, 'price' => $request->price_30ml],
                ['name' => '60ml Large', 'ml' => 60, 'price' => $request->price_60ml],
                ['name' => '90ml Patiala', 'ml' => 90, 'price' => $request->price_90ml],
            ];

            foreach ($defaultPegs as $peg) {
                if ($peg['price'] !== null && $peg['price'] !== '' && is_numeric($peg['price'])) {
                    $pVal = floatval($peg['price']);
                    $existingVar = BarItemVariation::where('sub_category_id', $item->id)
                        ->where('restaurant_id', $restaurantId)
                        ->where(function($q) use ($peg) {
                            $q->where('ml', $peg['ml'])
                              ->orWhere('name', $peg['name'])
                              ->orWhere('name', 'LIKE', '%' . $peg['ml'] . 'ml%');
                        })
                        ->first();

                    if ($existingVar) {
                        $existingVar->name = $peg['name'];
                        $existingVar->ml = $peg['ml'];
                        $existingVar->price = $pVal;
                        $existingVar->status = 'A';
                        $existingVar->save();
                    } else {
                        $newVar = new BarItemVariation();
                        $newVar->restaurant_id = $restaurantId;
                        $newVar->sub_category_id = $item->id;
                        $newVar->name = $peg['name'];
                        $newVar->ml = $peg['ml'];
                        $newVar->price = $pVal;
                        $newVar->status = 'A';
                        $newVar->save();
                    }
                }
            }

            // Sync with Purchase Management products (unit = ML)
            \App\Models\Product::syncBarItems($restaurantId);

            return redirect()->back()->with('success', "Bar Item \"{$item->name}\" updated successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update bar item: ' . $e->getMessage());
        }
    }

    /**
     * Toggle availability status for a Bar Item (Available 'A' <-> Unavailable 'I')
     * Supports both AJAX requests and standard HTTP requests.
     */
    public function toggleStatus(Request $request, $id)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $item = SubCategory::where('id', $id)
            ->where('restaurant_id', $restaurantId)
            ->first();

        if (!$item) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bar item not found or unauthorized.'
                ], 404);
            }
            return redirect()->back()->with('error', 'Bar item not found or unauthorized.');
        }

        // Set explicit status if provided ('A' or 'I'), otherwise toggle: 'A' -> 'I', any other -> 'A'
        if ($request->filled('status') && in_array($request->status, ['A', 'I'])) {
            $newStatus = $request->status;
        } else {
            $newStatus = ($item->status === 'A') ? 'I' : 'A';
        }
        $item->status = $newStatus;
        $item->save();

        $isAvailable = ($newStatus === 'A');
        $statusText = $isAvailable ? 'Available' : 'Unavailable';
        $msg = "Item \"{$item->name}\" is now {$statusText}.";

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success'      => true,
                'status'       => $newStatus,
                'is_available' => $isAvailable,
                'status_text'  => $statusText,
                'item_id'      => $item->id,
                'item_name'    => $item->name,
                'message'      => $msg,
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Soft delete a Bar Item
     */
    public function delete($id)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $item = SubCategory::where('id', $id)
            ->where('restaurant_id', $restaurantId)
            ->first();

        if ($item) {
            $item->status = 'D';
            $item->save();

            \App\Models\Product::where('sub_category_id', $item->id)
                ->where('restaurant_id', $restaurantId)
                ->update(['status' => 'D']);

            return redirect()->back()->with('success', "Bar Item \"{$item->name}\" deleted successfully.");
        }

        return redirect()->back()->with('error', 'Item not found or unauthorized.');
    }

    /**
     * Get variations for an item (AJAX)
     */
    public function getVariations($item_id)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $item = SubCategory::with('brand')
            ->where('id', $item_id)
            ->where('restaurant_id', $restaurantId)
            ->firstOrFail();

        $variations = BarItemVariation::where('sub_category_id', $item_id)
            ->where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->orderBy('ml', 'asc')
            ->get();

        return response()->json([
            'success'    => true,
            'item'       => $item,
            'variations' => $variations,
        ]);
    }

    /**
     * Store a new Variation for a Bar Item (e.g. "30ml Peg", "60ml Large")
     */
    public function storeVariation(Request $request)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:sub_category,id',
            'name'            => 'required|string|max:100',
            'ml'              => 'required|numeric|min:1',
            'price'           => 'required|numeric|min:0',
        ], [
            'name.required'  => 'Please enter the variation name (e.g. 30ml Peg).',
            'ml.required'    => 'Please enter the volume in ml.',
            'price.required' => 'Please enter the price for this variation.',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        $item = SubCategory::where('id', $request->sub_category_id)
            ->where('restaurant_id', $restaurantId)
            ->firstOrFail();

        try {
            $variation = new BarItemVariation();
            $variation->restaurant_id = $restaurantId;
            $variation->sub_category_id = $item->id;
            $variation->name = trim($request->name);
            $variation->ml = floatval($request->ml);
            $variation->price = floatval($request->price);
            $variation->status = 'A';
            $variation->save();

            return response()->json([
                'success'   => true,
                'message'   => "Variation \"{$variation->name}\" added successfully.",
                'variation' => $variation,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add variation: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a Variation
     */
    public function deleteVariation($id)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $variation = BarItemVariation::where('id', $id)
            ->where('restaurant_id', $restaurantId)
            ->first();

        if ($variation) {
            $variation->status = 'D';
            $variation->save();

            return response()->json([
                'success' => true,
                'message' => 'Variation deleted successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Variation not found.',
        ], 404);
    }

    /**
     * Download Excel template for Bar Items bulk upload
     * Includes mandatory Full Bottle Price and automatic Full Bottle & Peg (30ml, 60ml, 90ml) variation support.
     * Cover image is intentionally excluded as requested.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Bar Items Template');

        // Column Headers (Without Cover Image)
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

        $samples = [
            ['Johnnie Walker Black Label 12 Yrs', 'Single Malt Whiskeys', 'Johnnie Walker', 750, 4200, 260, 500, 720, 2, 0, 20, 'Premium blended Scotch whisky'],
            ['Glenfiddich 12 Year Old', 'Single Malt Whiskeys', 'Glenfiddich', 750, 5600, 320, 600, 880, 1, 150, 20, 'Signature single malt scotch with fresh pear notes'],
            ['Grey Goose Original Vodka', 'Vodka & Gin', 'Grey Goose', 1000, 4800, 250, 480, 700, 3, 0, 20, 'Distilled in France from French winter wheat'],
            ['Bombay Sapphire London Dry Gin', 'Vodka & Gin', 'Bombay Sapphire', 750, 2900, 180, 350, 500, 2, 60, 20, 'Vapour infused with 10 hand-selected botanicals'],
            ['Corona Extra Beer', 'Beers & Draughts', 'Corona Extra', 330, 280, '', '', '', 24, 0, 10, 'Crisp Mexican pale lager served with lime'],
            ['Classic Smoked Old Fashioned', 'Cocktails & Mixology', '', 750, 650, '', '', '', 1, 0, 20, 'Craft bourbon cocktail with aromatic bitters'],
        ];

        $r = 2;
        foreach ($samples as $s) {
            $sheet->setCellValue('A' . $r, $s[0]);
            $sheet->setCellValue('B' . $r, $s[1]);
            $sheet->setCellValue('C' . $r, $s[2]);
            $sheet->setCellValue('D' . $r, $s[3]);
            $sheet->setCellValue('E' . $r, $s[4]);
            $sheet->setCellValue('F' . $r, $s[5]);
            $sheet->setCellValue('G' . $r, $s[6]);
            $sheet->setCellValue('H' . $r, $s[7]);
            $sheet->setCellValue('I' . $r, $s[8]);
            $sheet->setCellValue('J' . $r, $s[9]);
            $sheet->setCellValue('K' . $r, $s[10]);
            $sheet->setCellValue('L' . $r, $s[11]);
            $r++;
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF8B5CF6'], // Bar Tone Purple
            ],
        ];

        $sheet->getStyle('A1:L1')->applyFromArray($headerStyle);
        $sheet->getColumnDimension('A')->setWidth(35);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(18);
        $sheet->getColumnDimension('G')->setWidth(18);
        $sheet->getColumnDimension('H')->setWidth(18);
        $sheet->getColumnDimension('I')->setWidth(18);
        $sheet->getColumnDimension('J')->setWidth(12);
        $sheet->getColumnDimension('K')->setWidth(18);
        $sheet->getColumnDimension('L')->setWidth(40);

        $filename = "bar_items_upload_template.xlsx";

        return response()->streamDownload(function() use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Bulk upload Bar Items from Excel/CSV spreadsheet
     * Automatically inserts Category and Brand if not found, and gets their IDs.
     * Automatically creates "Full Bottle" and peg variations (30ml, 60ml, 90ml) if provided.
     * Cover Image is intentionally skipped as requested.
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'bulk_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'bulk_file.required' => 'Please select an Excel or CSV file to upload.',
            'bulk_file.mimes'    => 'File must be a valid spreadsheet (.xlsx, .xls, .csv).',
            'bulk_file.max'      => 'File size cannot exceed 10MB.',
        ]);

        $restaurantId = auth()->user()->restaurant_id;
        $restaurant = \App\Models\RestaurantMaster::find($restaurantId);
        $defaultVatRate = floatval($restaurant->vat_percentage ?? 20);

        try {
            $file = $request->file('bulk_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $headerSkipped = false;
            $importedCount = 0;
            $newCategoriesCount = 0;
            $newBrandsCount = 0;

            // Default column index mapping (supports 12-column with 30/60/90ml, 9-column with price, and 8-column legacy)
            $colMap = [
                'name'        => 0,
                'category'    => 1,
                'brand'       => 2,
                'bottle_size' => 3,
                'price'       => 4,
                'price_30'    => 5,
                'price_60'    => 6,
                'price_90'    => 7,
                'bottle_qty'  => 8,
                'extra_ml'    => 9,
                'vat_rate'    => 10,
                'description' => 11,
            ];

            // Load existing categories and brands for this restaurant into memory caches
            $categoriesCache = Category::where('restaurant_id', $restaurantId)
                ->where('status', '!=', 'D')
                ->where('category_type', 'BAR')
                ->get()
                ->keyBy(fn($c) => strtolower(trim($c->name)));

            $brandsCache = BarBrand::where('restaurant_id', $restaurantId)
                ->where('status', '!=', 'D')
                ->get()
                ->keyBy(fn($b) => strtolower(trim($b->name)));

            \DB::beginTransaction();

            foreach ($rows as $index => $row) {
                if (empty(array_filter($row, fn($val) => !is_null($val) && trim((string)$val) !== ''))) {
                    continue; // Skip empty row
                }

                $firstCell = strtolower(trim((string)($row[0] ?? '')));

                // Detect and skip header row
                if (!$headerSkipped && in_array($firstCell, [
                    'product name *', 'product name', 'item name *', 'item name', 'name', 'product', 'item'
                ])) {
                    $headerSkipped = true;
                    // Check if headers indicate custom positions or presence of price/peg columns
                    $hasPriceHeader = false;
                    $hasPegHeaders = false;
                    foreach ($row as $colIdx => $colVal) {
                        $c = strtolower(trim((string)$colVal));
                        if (str_contains($c, 'product') || str_contains($c, 'item name')) {
                            $colMap['name'] = $colIdx;
                        } elseif (str_contains($c, 'category')) {
                            $colMap['category'] = $colIdx;
                        } elseif (str_contains($c, 'brand')) {
                            $colMap['brand'] = $colIdx;
                        } elseif (str_contains($c, 'bottle size') || str_contains($c, 'size in ml') || str_contains($c, 'size')) {
                            $colMap['bottle_size'] = $colIdx;
                        } elseif (str_contains($c, 'vat') || str_contains($c, 'tax')) {
                            $colMap['vat_rate'] = $colIdx;
                        } elseif (str_contains($c, '30ml') || str_contains($c, '30 ml') || str_contains($c, '30 peg') || str_contains($c, 'peg 30')) {
                            $colMap['price_30'] = $colIdx;
                            $hasPegHeaders = true;
                        } elseif (str_contains($c, '60ml') || str_contains($c, '60 ml') || str_contains($c, '60 large') || str_contains($c, 'large 60')) {
                            $colMap['price_60'] = $colIdx;
                            $hasPegHeaders = true;
                        } elseif (str_contains($c, '90ml') || str_contains($c, '90 ml') || str_contains($c, '90 patiala') || str_contains($c, 'patiala 90')) {
                            $colMap['price_90'] = $colIdx;
                            $hasPegHeaders = true;
                        } elseif (str_contains($c, 'price') || str_contains($c, 'mrp') || str_contains($c, 'bottle price')) {
                            $colMap['price'] = $colIdx;
                            $hasPriceHeader = true;
                        } elseif (str_contains($c, 'bottle qty') || str_contains($c, 'opening bottle')) {
                            $colMap['bottle_qty'] = $colIdx;
                        } elseif (str_contains($c, 'extra ml') || str_contains($c, 'loose')) {
                            $colMap['extra_ml'] = $colIdx;
                        } elseif (str_contains($c, 'desc')) {
                            $colMap['description'] = $colIdx;
                        }
                    }

                    // If header row had no peg headers (legacy file), map columns accordingly
                    if (!$hasPegHeaders) {
                        $colMap['price_30'] = null;
                        $colMap['price_60'] = null;
                        $colMap['price_90'] = null;
                        if ($hasPriceHeader) {
                            $colMap['bottle_qty'] = 5;
                            $colMap['extra_ml'] = 6;
                            $colMap['vat_rate'] = 7;
                            $colMap['description'] = 8;
                        } else {
                            $colMap['price'] = null;
                            $colMap['bottle_qty'] = 4;
                            $colMap['extra_ml'] = 5;
                            $colMap['vat_rate'] = 6;
                            $colMap['description'] = 7;
                        }
                    }
                    continue;
                }

                $rawName       = trim((string)($row[$colMap['name']] ?? ''));
                $rawCategory   = trim((string)($row[$colMap['category']] ?? ''));
                $rawBrand      = trim((string)($row[$colMap['brand']] ?? ''));
                $rawBottleSize = trim((string)($row[$colMap['bottle_size']] ?? ''));
                $rawPrice      = ($colMap['price'] !== null && isset($row[$colMap['price']])) ? trim((string)$row[$colMap['price']]) : '';
                $rawPrice30    = ($colMap['price_30'] !== null && isset($row[$colMap['price_30']])) ? trim((string)$row[$colMap['price_30']]) : '';
                $rawPrice60    = ($colMap['price_60'] !== null && isset($row[$colMap['price_60']])) ? trim((string)$row[$colMap['price_60']]) : '';
                $rawPrice90    = ($colMap['price_90'] !== null && isset($row[$colMap['price_90']])) ? trim((string)$row[$colMap['price_90']]) : '';
                $rawBottleQty  = trim((string)($row[$colMap['bottle_qty']] ?? ''));
                $rawExtraMl    = trim((string)($row[$colMap['extra_ml']] ?? ''));
                $rawVatRate    = trim((string)($row[$colMap['vat_rate']] ?? ''));
                $rawDesc       = trim((string)($row[$colMap['description']] ?? ''));

                if ($rawName === '') {
                    continue; // Product name is mandatory
                }

                // 1. Resolve Category (Auto-create if not found)
                $catName = $rawCategory !== '' ? $rawCategory : 'Bar Beverages';
                $catKey = strtolower($catName);
                if (isset($categoriesCache[$catKey])) {
                    $categoryId = $categoriesCache[$catKey]->id;
                } else {
                    $newCat = new Category();
                    $newCat->name = $catName;
                    $newCat->category_type = 'BAR';
                    $newCat->user_id = auth()->id();
                    $newCat->restaurant_id = $restaurantId;
                    $newCat->status = 'A';
                    $newCat->save();
                    $newCat->slug = Str::slug($catName) . '-' . $newCat->id;
                    $newCat->save();

                    $categoryId = $newCat->id;
                    $categoriesCache[$catKey] = $newCat;
                    $newCategoriesCount++;
                }

                // 2. Resolve Brand (Auto-create if provided and not found)
                $brandId = null;
                if ($rawBrand !== '') {
                    $brandKey = strtolower($rawBrand);
                    if (isset($brandsCache[$brandKey])) {
                        $brandId = $brandsCache[$brandKey]->id;
                    } else {
                        $newBrand = new BarBrand();
                        $newBrand->name = $rawBrand;
                        $newBrand->restaurant_id = $restaurantId;
                        $newBrand->status = 'A';
                        $newBrand->save();

                        $brandId = $newBrand->id;
                        $brandsCache[$brandKey] = $newBrand;
                        $newBrandsCount++;
                    }
                }

                // 3. Inventory calculation
                $bottleSize = is_numeric($rawBottleSize) && floatval($rawBottleSize) > 0 
                    ? floatval($rawBottleSize) 
                    : 750;
                
                $bottlePrice = is_numeric($rawPrice) ? floatval($rawPrice) : 0;
                $bottleQty   = is_numeric($rawBottleQty) ? floatval($rawBottleQty) : 1;
                $extraMl     = is_numeric($rawExtraMl) ? floatval($rawExtraMl) : 0;
                $openingMl   = ($bottleSize * $bottleQty) + $extraMl;
                $currentStockMl = $openingMl;

                $vatRate = is_numeric($rawVatRate) ? floatval($rawVatRate) : $defaultVatRate;

                // 4. Create Bar Item (SubCategory model)
                $item = new SubCategory();
                $item->name = $rawName;
                $item->category_id = $categoryId;
                $item->brand_id = $brandId;
                $item->bottle_size_ml = $bottleSize;
                $item->opening_qty = $openingMl;
                $item->current_stock_ml = $currentStockMl;
                $item->price = $bottlePrice;
                $item->vat_rate = $vatRate;
                $item->gst_rate = 0.00; // As per Indian excise law, liquor items are subject to State VAT, not GST
                $item->food_type = 'BAR';
                $item->description = $rawDesc !== '' ? $rawDesc : null;
                $item->restaurant_id = $restaurantId;
                $item->user_id = auth()->id();
                $item->status = 'A';
                // Cover Image is intentionally skipped as requested
                $item->save();

                // 5. Automatically create "Full Bottle" serving variation with full bottle ml and price
                $variation = new BarItemVariation();
                $variation->restaurant_id = $restaurantId;
                $variation->sub_category_id = $item->id;
                $variation->name = 'Full Bottle';
                $variation->ml = $bottleSize;
                $variation->price = $bottlePrice;
                $variation->status = 'A';
                $variation->save();

                // 6. Automatically create default peg variations (30ml, 60ml, 90ml) if prices are provided
                $defaultPegs = [
                    ['name' => '30ml Peg', 'ml' => 30, 'price' => $rawPrice30],
                    ['name' => '60ml Large', 'ml' => 60, 'price' => $rawPrice60],
                    ['name' => '90ml Patiala', 'ml' => 90, 'price' => $rawPrice90],
                ];

                foreach ($defaultPegs as $peg) {
                    if ($peg['price'] !== '' && is_numeric($peg['price'])) {
                        $pegVar = new BarItemVariation();
                        $pegVar->restaurant_id = $restaurantId;
                        $pegVar->sub_category_id = $item->id;
                        $pegVar->name = $peg['name'];
                        $pegVar->ml = $peg['ml'];
                        $pegVar->price = floatval($peg['price']);
                        $pegVar->status = 'A';
                        $pegVar->save();
                    }
                }

                $importedCount++;
            }

            \DB::commit();

            // Sync with Purchase Management products (unit = ML)
            \App\Models\Product::syncBarItems($restaurantId);

            $msg = "Successfully imported {$importedCount} bar items with Full Bottle and Peg variations!";
            if ($newCategoriesCount > 0 || $newBrandsCount > 0) {
                $msg .= " (Auto-created {$newCategoriesCount} new categories and {$newBrandsCount} new brands).";
            }

            return redirect()->back()->with('success', $msg);

        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Error importing bar items spreadsheet: ' . $e->getMessage());
        }
    }

        } catch (\Exception $e) {
            \DB::rollBack();
            return redirect()->back()->with('error', 'Error importing bar items spreadsheet: ' . $e->getMessage());
        }
    }
}
