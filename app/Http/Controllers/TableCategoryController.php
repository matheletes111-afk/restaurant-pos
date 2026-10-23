<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TableCategory;
use App\Models\TableManage;
use App\Models\RestaurantMaster;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

class TableCategoryController extends Controller
{
    /**
     * Display listing of table categories (One-page modal-based CRUD).
     */
    public function index()
    {
        $activeRestId = auth()->user()->restaurant_id;

        $data['categories'] = TableCategory::where('restaurant_id', $activeRestId)
            ->where('status', '!=', 'D')
            ->withCount(['tables' => function ($query) use ($activeRestId) {
                $query->where('status', '!=', 'D')->where('restaurant_id', $activeRestId);
            }])
            ->orderBy('id', 'DESC')
            ->get();

        $data['restaurant'] = RestaurantMaster::find($activeRestId);
        $subRestaurantId = auth()->user()->getSubscriptionRestaurantId() ?? $activeRestId;
        $check_plan = Subscription::where('user_id', $subRestaurantId)->active()->first();
        $data['plan_details'] = $check_plan ? Plan::where('id', $check_plan->plan_id)->first() : null;

        // Count uncategorized (General Category) tables
        $data['generalTablesCount'] = TableManage::where('restaurant_id', $activeRestId)
            ->where('status', '!=', 'D')
            ->where(function ($q) {
                $q->whereNull('table_category_id')->orWhere('table_category_id', 0);
            })
            ->count();

        $data['totalTablesCount'] = TableManage::where('restaurant_id', $activeRestId)
            ->where('status', '!=', 'D')
            ->count();

        return view('restaurant.table-category', $data);
    }

    /**
     * Store a newly created table category.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        // Check if duplicate active category exists for this restaurant
        $exists = TableCategory::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->where('name', trim($request->name))
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A table category with this name already exists.');
        }

        TableCategory::create([
            'restaurant_id' => $restaurantId,
            'user_id' => auth()->id(),
            'name' => trim($request->name),
            'description' => $request->description,
            'status' => 'A',
        ]);

        return redirect()->back()->with('success', 'Table category created successfully.');
    }

    /**
     * Update the specified table category.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        $category = TableCategory::where('id', $request->id)
            ->where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->firstOrFail();

        // Check duplicate name excluding current record
        $exists = TableCategory::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->where('id', '!=', $category->id)
            ->where('name', trim($request->name))
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Another table category with this name already exists.');
        }

        $category->name = trim($request->name);
        $category->description = $request->description;
        $category->save();

        return redirect()->back()->with('success', 'Table category updated successfully.');
    }

    /**
     * Toggle active/inactive status.
     */
    public function status($id)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $category = TableCategory::where('id', $id)
            ->where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->first();

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        $category->status = ($category->status === 'A') ? 'I' : 'A';
        $category->save();

        $statusLabel = ($category->status === 'A') ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Table category has been {$statusLabel}.");
    }

    /**
     * Soft delete category (sets status to 'D').
     */
    public function delete($id)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $category = TableCategory::where('id', $id)
            ->where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->first();

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        // Tables attached will naturally fall back to "General Category"
        TableManage::where('table_category_id', $category->id)
            ->where('restaurant_id', $restaurantId)
            ->update(['table_category_id' => null]);

        $category->status = 'D';
        $category->save();

        return redirect()->back()->with('success', 'Table category deleted successfully. Associated tables are now under General Category.');
    }
}
