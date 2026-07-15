<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SecureRestaurantData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return $next($request);
        }

        // Only restrict for restaurant level accounts (role == 'RES')
        if ($user->role !== 'RES') {
            return $next($request);
        }

        // Get the route parameters
        $parameters = $request->route()->parameters();
        if (empty($parameters)) {
            return $next($request);
        }

        // Map URL path segments to their database tables
        $modelMapping = [
            'restaurant-staff' => ['table' => 'users', 'column' => 'id', 'restro_col' => 'restaurant_id'],
            'manage-menu-category' => ['table' => 'category', 'column' => 'id', 'restro_col' => 'restaurant_id'],
            'manage-category' => ['table' => 'sub_category', 'column' => 'id', 'restro_col' => 'restaurant_id'],
            'table-manage' => ['table' => 'table_management', 'column' => 'id', 'restro_col' => 'restaurant_id'],
            'products' => ['table' => 'products', 'column' => 'id', 'restro_col' => 'restaurant_id'],
            'inventory' => ['table' => 'inventories', 'column' => 'id', 'restro_col' => 'restaurant_id'],
            'suppliers' => ['table' => 'suppliers', 'column' => 'id', 'restro_col' => 'restaurant_id'],
            'purchases' => ['table' => 'purchases', 'column' => 'id', 'restro_col' => 'restaurant_id'],
            'stock-outs' => ['table' => 'stock_outs', 'column' => 'id', 'restro_col' => 'restaurant_id'],
            'debit-notes' => ['table' => 'debit_notes', 'column' => 'id', 'restro_col' => 'restaurant_id'],
            'manage-units' => ['table' => 'units', 'column' => 'id', 'restro_col' => 'restaurant_id'],
            'restaurant-support' => ['table' => 'support_tickets', 'column' => 'id', 'restro_col' => 'restaurant_id'],
        ];

        // Map specific parameter keys directly to database tables
        $parameterNameMapping = [
            'order_id' => ['table' => 'orders', 'column' => 'id', 'restro_col' => 'restaurant_id'],
            'order' => ['table' => 'orders', 'column' => 'id', 'restro_col' => 'restaurant_id'],
            'ticket' => ['table' => 'support_tickets', 'column' => 'id', 'restro_col' => 'restaurant_id'],
        ];

        $path = $request->path();

        foreach ($parameters as $key => $value) {
            // Get the ID from the route parameter
            $id = is_object($value) && method_exists($value, 'getKey') ? $value->getKey() : $value;

            if (!$id || !is_numeric($id)) {
                continue;
            }

            $target = null;

            // 1. Try matching based on parameter name
            if (isset($parameterNameMapping[$key])) {
                $target = $parameterNameMapping[$key];
            } 
            // 2. Try matching based on URL segments
            else {
                foreach ($modelMapping as $segment => $config) {
                    if (str_contains($path, $segment)) {
                        $target = $config;
                        break;
                    }
                }
            }

            // Fallback for general 'id' parameter if no segment matched
            if (!$target && $key === 'id') {
                if (str_contains($path, 'order')) {
                    $target = ['table' => 'orders', 'column' => 'id', 'restro_col' => 'restaurant_id'];
                } elseif (str_contains($path, 'staff')) {
                    $target = ['table' => 'users', 'column' => 'id', 'restro_col' => 'restaurant_id'];
                } elseif (str_contains($path, 'category')) {
                    $target = ['table' => 'category', 'column' => 'id', 'restro_col' => 'restaurant_id'];
                } elseif (str_contains($path, 'table')) {
                    $target = ['table' => 'table_management', 'column' => 'id', 'restro_col' => 'restaurant_id'];
                } elseif (str_contains($path, 'product')) {
                    $target = ['table' => 'products', 'column' => 'id', 'restro_col' => 'restaurant_id'];
                } elseif (str_contains($path, 'supplier')) {
                    $target = ['table' => 'suppliers', 'column' => 'id', 'restro_col' => 'restaurant_id'];
                } elseif (str_contains($path, 'purchase')) {
                    $target = ['table' => 'purchases', 'column' => 'id', 'restro_col' => 'restaurant_id'];
                } elseif (str_contains($path, 'stock-out') || str_contains($path, 'stockout')) {
                    $target = ['table' => 'stock_outs', 'column' => 'id', 'restro_col' => 'restaurant_id'];
                } elseif (str_contains($path, 'debit')) {
                    $target = ['table' => 'debit_notes', 'column' => 'id', 'restro_col' => 'restaurant_id'];
                }
            }

            // If we identified the table, query it to verify ownership
            if ($target) {
                $record = DB::table($target['table'])
                    ->where($target['column'], $id)
                    ->first();

                if ($record) {
                    $restroCol = $target['restro_col'];
                    if (property_exists($record, $restroCol) && $record->$restroCol != $user->restaurant_id) {
                        abort(403, 'Unauthorized access to this restaurant resource.');
                    }
                }
            }
        }

        return $next($request);
    }
}
