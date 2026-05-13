<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

class CategoryApiController extends Controller
{
    public function index()
    {
        $response = [];
        try {
            $data = Category::where('status', '!=', 'D')
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->get();
            
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

    public function store(Request $request)
    {
        $response = [];
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            
            $new = new Category;
            $new->name = $request->name;
            $new->user_id = auth()->user()->id;
            $new->restaurant_id = auth()->user()->restaurant_id;
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '-' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                $image->move(storage_path('app/public/category'), $filename);
                $new->image = $filename;
            }
            
            $new->save();
            
            // Update slug
            $upd['slug'] = Str::slug($request->name) . '-' . $new->id;
            Category::where('id', $new->id)->update($upd);
            
            $response['success'] = true;
            $response['message'] = 'Category created successfully';
            $response['data'] = Category::where('id', $new->id)->first();
            $response['image_url'] = url('storage/app/public/category/');
            
            return Response::json($response, 201);
            
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
            return Response::json($response, 500);
        }
    }

    public function show($id)
    {
        $response = [];
        try {
            $data = Category::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->where('status', '!=', 'D')
                ->first();
            
            if (!$data) {
                $response['success'] = false;
                $response['message'] = 'Category not found';
                return Response::json($response, 404);
            }
            
            $response['success'] = true;
            $response['data'] = $data;
            $response['image_url'] = url('storage/app/public/category/');
            
            return Response::json($response);
            
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
            return Response::json($response, 500);
        }
    }

    public function update(Request $request, $id)
    {
        $response = [];
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
            
            $category = Category::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->first();
            
            if (!$category) {
                $response['success'] = false;
                $response['message'] = 'Category not found';
                return Response::json($response, 404);
            }
            
            $upd = [];
            $upd['name'] = $request->name;
            $upd['slug'] = Str::slug($request->name) . '-' . $id;
            
            if ($request->hasFile('image')) {
                // Delete old image
                if ($category->image && file_exists(storage_path('app/public/category/' . $category->image))) {
                    @unlink(storage_path('app/public/category/' . $category->image));
                }
                
                $image = $request->file('image');
                $filename = time() . '-' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
                $image->move(storage_path('app/public/category'), $filename);
                $upd['image'] = $filename;
            }
            
            Category::where('id', $id)->update($upd);
            
            $response['success'] = true;
            $response['message'] = 'Category updated successfully';
            $response['data'] = Category::where('id', $id)->first();
            $response['image_url'] = url('storage/app/public/category/');
            
            return Response::json($response);
            
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
            return Response::json($response, 500);
        }
    }

    public function destroy($id)
    {
        $response = [];
        try {
            $category = Category::where('id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->first();
            
            if (!$category) {
                $response['success'] = false;
                $response['message'] = 'Category not found';
                return Response::json($response, 404);
            }
            
            // Delete image file
            if ($category->image && file_exists(storage_path('app/public/category/' . $category->image))) {
                @unlink(storage_path('app/public/category/' . $category->image));
            }
            
            // Soft delete by updating status
            Category::where('id', $id)->update(['status' => 'D']);
            
            $response['success'] = true;
            $response['message'] = 'Category deleted successfully';
            
            return Response::json($response);
            
        } catch (\Exception $e) {
            $response['error'] = $e->getMessage();
            return Response::json($response, 500);
        }
    }
}