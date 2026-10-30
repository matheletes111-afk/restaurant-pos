<?php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\BarBrand;
use App\Models\BarItemVariation;
use Illuminate\Support\Str;

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
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:category,id',
            'brand_id'       => 'nullable|exists:bar_brands,id',
            'bottle_size_ml' => 'required|numeric|min:1',
            'opening_qty'    => 'required|numeric|min:0',
            'price'          => 'nullable|numeric|min:0',
            'vat_rate'       => 'nullable|numeric|min:0|max:100',
            'description'    => 'nullable|string|max:1000',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ], [
            'name.required'           => 'Please enter the bar item name.',
            'category_id.required'    => 'Please select a bar category.',
            'bottle_size_ml.required' => 'Please enter the standard bottle size in ml (e.g. 750).',
            'opening_qty.required'    => 'Please enter the opening stock in ml.',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        try {
            $bottleSize = floatval($request->bottle_size_ml);
            $openingMl = floatval($request->opening_qty);
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

            // Auto-generate standard bottle variation if price provided
            if ($item->price > 0) {
                BarItemVariation::create([
                    'restaurant_id'   => $restaurantId,
                    'sub_category_id' => $item->id,
                    'name'            => "Full Bottle ({$bottleSize}ml)",
                    'ml'              => $bottleSize,
                    'price'           => $item->price,
                    'status'          => 'A',
                ]);
            }

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
            'id'             => 'required|exists:sub_category,id',
            'name'           => 'required|string|max:255',
            'category_id'    => 'required|exists:category,id',
            'brand_id'       => 'nullable|exists:bar_brands,id',
            'bottle_size_ml' => 'required|numeric|min:1',
            'opening_qty'    => 'required|numeric|min:0',
            'price'          => 'nullable|numeric|min:0',
            'vat_rate'       => 'nullable|numeric|min:0|max:100',
            'description'    => 'nullable|string|max:1000',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
        ], [
            'name.required'           => 'Please enter the bar item name.',
            'category_id.required'    => 'Please select a bar category.',
            'bottle_size_ml.required' => 'Please enter the standard bottle size in ml (e.g. 750).',
            'opening_qty.required'    => 'Please enter the opening stock in ml.',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        $item = SubCategory::where('id', $request->id)
            ->where('restaurant_id', $restaurantId)
            ->firstOrFail();

        try {
            $bottleSize = floatval($request->bottle_size_ml);
            $openingMl = floatval($request->opening_qty);

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
}
