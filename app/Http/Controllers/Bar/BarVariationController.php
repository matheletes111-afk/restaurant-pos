<?php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarItemVariation;
use App\Models\SubCategory;
use App\Models\Category;

class BarVariationController extends Controller
{
    /**
     * Display a listing of Bar Item Variations
     */
    public function index(Request $request, $item_id = null)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $itemId = $item_id ?: $request->get('item_id');
        $categoryId = $request->get('category_id');
        $search = $request->get('search');

        // Available Bar Categories for Filter & Dropdowns
        $barCategories = Category::where('restaurant_id', $restaurantId)
            ->where('category_type', 'BAR')
            ->where('status', '!=', 'D')
            ->orderBy('name', 'asc')
            ->get();

        // Available Bar Items for Filter & Modal Select
        $barItems = SubCategory::with(['brand', 'category'])
            ->where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->whereHas('category', function($q) {
                $q->where('category_type', 'BAR');
            })
            ->orderBy('name', 'asc')
            ->get();

        // Variations Query
        $query = BarItemVariation::with(['item.brand', 'item.category'])
            ->where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D');

        if ($itemId) {
            $query->where('sub_category_id', $itemId);
        }

        if ($categoryId) {
            $query->whereHas('item', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('item', function($iq) use ($search) {
                      $iq->where('name', 'like', "%{$search}%")
                         ->orWhereHas('brand', function($bq) use ($search) {
                             $bq->where('name', 'like', "%{$search}%");
                         });
                  });
            });
        }

        $variations = $query->orderBy('sub_category_id', 'asc')
            ->orderBy('ml', 'asc')
            ->get();

        // KPI Statistics
        $allRestaurantVariations = BarItemVariation::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->get();

        $stats = [
            'total_variations'  => $allRestaurantVariations->count(),
            'distinct_items'    => $allRestaurantVariations->pluck('sub_category_id')->unique()->count(),
            'avg_price'         => $allRestaurantVariations->count() > 0 ? $allRestaurantVariations->avg('price') : 0,
            'standard_pegs'     => $allRestaurantVariations->whereIn('ml', [30, 60, 90])->count(),
        ];

        $selectedItem = $itemId ? $barItems->firstWhere('id', $itemId) : null;

        return view('bar.variation_index', compact(
            'variations',
            'barCategories',
            'barItems',
            'stats',
            'itemId',
            'categoryId',
            'search',
            'selectedItem'
        ));
    }

    /**
     * Store a newly created variation (Modal Form)
     */
    public function store(Request $request)
    {
        $request->validate([
            'sub_category_id' => 'required|exists:sub_category,id',
            'name'            => 'required|string|max:100',
            'ml'              => 'required|numeric|min:1',
            'price'           => 'required|numeric|min:0',
        ], [
            'sub_category_id.required' => 'Please select the parent bar product.',
            'name.required'            => 'Please provide a serving format name (e.g. 30ml Peg).',
            'ml.required'              => 'Please provide the volume in ml.',
            'price.required'           => 'Please enter the selling price for this serving.',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        // Verify product belongs to current restaurant
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

            return redirect()->back()->with('success', "Serving variation \"{$variation->name}\" ({$variation->ml}ml) added successfully for {$item->name}.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save variation: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing variation (Modal Form)
     */
    public function update(Request $request)
    {
        $request->validate([
            'id'              => 'required|exists:bar_item_variations,id',
            'sub_category_id' => 'required|exists:sub_category,id',
            'name'            => 'required|string|max:100',
            'ml'              => 'required|numeric|min:1',
            'price'           => 'required|numeric|min:0',
        ], [
            'sub_category_id.required' => 'Please select the parent bar product.',
            'name.required'            => 'Please provide a serving format name.',
            'ml.required'              => 'Please provide the volume in ml.',
            'price.required'           => 'Please enter the selling price.',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        $variation = BarItemVariation::where('id', $request->id)
            ->where('restaurant_id', $restaurantId)
            ->firstOrFail();

        // Verify item belongs to this restaurant
        $item = SubCategory::where('id', $request->sub_category_id)
            ->where('restaurant_id', $restaurantId)
            ->firstOrFail();

        try {
            $variation->sub_category_id = $item->id;
            $variation->name = trim($request->name);
            $variation->ml = floatval($request->ml);
            $variation->price = floatval($request->price);
            $variation->save();

            return redirect()->back()->with('success', "Variation \"{$variation->name}\" updated successfully.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update variation: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete a variation
     */
    public function delete($id)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $variation = BarItemVariation::where('id', $id)
            ->where('restaurant_id', $restaurantId)
            ->first();

        if ($variation) {
            $name = $variation->name;
            $variation->status = 'D';
            $variation->save();
            $variation->delete(); // Eloquent soft delete

            return redirect()->back()->with('success', "Serving variation \"{$name}\" removed successfully.");
        }

        return redirect()->back()->with('error', 'Variation not found.');
    }
}
