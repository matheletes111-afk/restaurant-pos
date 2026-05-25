<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubCategory; // Your Dish/Product model
use App\Models\Category;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class DishApiController extends Controller
{
    // Get all dishes by category
    public function index($category_id = null)
    {
        $response = [];
        try {
            $query = SubCategory::where('status', '!=', 'D')
                ->where('restaurant_id', auth()->user()->restaurant_id);
            
            // If category_id is provided, filter by category
            if ($category_id) {
                // Verify category belongs to this restaurant
                $category = Category::where('id', $category_id)
                    ->where('restaurant_id', auth()->user()->restaurant_id)
                    ->first();
                
                if (!$category) {
                    $response['success'] = false;
                    $response['message'] = 'Category not found or unauthorized';
                    return Response::json($response, 404);
                }
                
                $query->where('category_id', $category_id);
                $response['category_details'] = $category;
            }
            
            $data = $query->orderBy('id', 'desc')->get();
            
            $response['success'] = true;
            $response['data'] = $data;
            $response['image_url'] = url('storage/app/public/category/');
            
            // Optional: Include plan details if needed
            $check_plan = Subscription::where('user_id', auth()->user()->restaurant_id)
                ->where('status', 'active')
                ->first();
            
            if ($check_plan) {
                $plan_details = Plan::where('id', $check_plan->plan_id)->first();
                $response['plan_details'] = $plan_details;
            }
            
            return Response::json($response);
            
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
            return Response::json($response, 500);
        }
    }

    // Get single dish details
    public function show($id)
    {
        $response = [];
        try {
            $data = SubCategory::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->where('status', '!=', 'D')
                ->first();
            
            if (!$data) {
                $response['success'] = false;
                $response['message'] = 'Dish not found';
                return Response::json($response, 404);
            }
            
            // Get category details
            $category = Category::where('id', $data->category_id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->first();
            
            $response['success'] = true;
            $response['data'] = $data;
            $response['category_details'] = $category;
            $response['image_url'] = url('storage/app/public/category/');
            
            return Response::json($response);
            
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
            return Response::json($response, 500);
        }
    }

    // Create new dish
    public function store(Request $request)
    {
        $response = [];
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|integer',
                'price' => 'required|numeric|min:0',
                // 'gst_rate' => 'nullable|numeric|min:0|max:100',
                'food_type' => 'required|in:Veg,Non-Veg,Egg',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
            
            // Verify category belongs to this restaurant
            $category = Category::where('id', $request->category_id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->first();
            
            if (!$category) {
                $response['success'] = false;
                $response['message'] = 'Category not found or unauthorized';
                return Response::json($response, 404);
            }
            
            $new = new SubCategory;
            $new->name = $request->name;
            $new->price = $request->price;
            // $new->gst_rate = $request->gst_rate ?? 0;
            $new->food_type = $request->food_type;
            $new->category_id = $request->category_id;
            $new->user_id = auth()->user()->id;
            $new->restaurant_id = auth()->user()->restaurant_id;
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '-' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                $image->move("storage/app/public/category",$filename);
                $new->image = $filename;
            }
            
            
            
            $new->save();
            
            $response['success'] = true;
            $response['message'] = 'Dish created successfully';
            $response['data'] = SubCategory::where('id', $new->id)->first();
            $response['image_url'] = url('storage/app/public/category/');
            
            return Response::json($response, 201);
            
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
            return Response::json($response, 500);
        }
    }

    // Update dish
    public function update(Request $request, $id)
    {
        $response = [];
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'category_id' => 'required|integer',
                'price' => 'required|numeric|min:0',
                
                'food_type' => 'required|in:Veg,Non-Veg,Egg',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                
            ]);
            
            $dish = SubCategory::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->first();
            
            if (!$dish) {
                $response['success'] = false;
                $response['message'] = 'Dish not found';
                return Response::json($response, 404);
            }
            
            // Verify new category belongs to this restaurant
            $category = Category::where('id', $request->category_id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->first();
            
            if (!$category) {
                $response['success'] = false;
                $response['message'] = 'Category not found or unauthorized';
                return Response::json($response, 404);
            }
            
            $upd = [];
            $upd['name'] = $request->name;
            $upd['price'] = $request->price;
            
            $upd['food_type'] = $request->food_type;
            $upd['category_id'] = $request->category_id;
            
            
            
            if ($request->hasFile('image')) {
                // Delete old image
                if ($dish->image && file_exists(storage_path('app/public/category/' . $dish->image))) {
                    @unlink(storage_path('app/public/category/' . $dish->image));
                }
                
                $image = $request->file('image');
                $filename = time() . '-' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                $image->move("storage/app/public/category",$filename);
                $upd['image'] = $filename;
            }
            
            SubCategory::where('id', $id)->update($upd);
            
            $response['success'] = true;
            $response['message'] = 'Dish updated successfully';
            $response['data'] = SubCategory::where('id', $id)->first();
            $response['image_url'] = url('storage/app/public/category/');
            
            return Response::json($response);
            
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
            return Response::json($response, 500);
        }
    }

    // Delete dish (soft delete)
    public function destroy($id)
    {
        $response = [];
        try {
            $dish = SubCategory::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->first();
            
            if (!$dish) {
                $response['success'] = false;
                $response['message'] = 'Dish not found';
                return Response::json($response, 404);
            }
            
            // Delete image file
            if ($dish->image && file_exists(storage_path('app/public/category/' . $dish->image))) {
                @unlink(storage_path('app/public/category/' . $dish->image));
            }
            
            // Soft delete by updating status
            SubCategory::where('id', $id)->update(['status' => 'D']);
            
            $response['success'] = true;
            $response['message'] = 'Dish deleted successfully';
            
            return Response::json($response);
            
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
            return Response::json($response, 500);
        }
    }

    // Update dish status (Active/Inactive)
    public function updateStatus($id)
    {
        $response = [];
        try {
            $dish = SubCategory::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->first();
            
            if (!$dish) {
                $response['success'] = false;
                $response['message'] = 'Dish not found';
                return Response::json($response, 404);
            }
            
            $newStatus = $dish->status === 'A' ? 'I' : 'A';
            SubCategory::where('id', $id)->update(['status' => $newStatus]);
            
            $response['success'] = true;
            $response['message'] = 'Status updated successfully';
            $response['new_status'] = $newStatus;
            $response['status_text'] = $newStatus === 'A' ? 'Active' : 'Inactive';
            
            return Response::json($response);
            
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
            return Response::json($response, 500);
        }
    }

        public function updateDiscount(Request $request)
    {
        $response = [];
        try {
            $product = SubCategory::where('id', $request->id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->first();
            
            if (!$product) {
                $response['success'] = false;
                $response['message'] = 'Product not found';
                return Response::json($response, 404);
            }
            
            $discount = $request->discount_percentage ?? 0;
            $product->discount_percentage = $discount;
            $product->status = $request->status;
            $product->save();
            
            $response['success'] = true;
            $response['message'] = "Discount updated to {$discount}% successfully";
            $response['discount_percentage'] = $discount;
            
            return Response::json($response);
            
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['message'] = 'Something went wrong: ' . $e->getMessage();
            return Response::json($response, 500);
        }
    }


}