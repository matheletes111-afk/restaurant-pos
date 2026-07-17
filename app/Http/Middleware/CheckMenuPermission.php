<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckMenuPermission
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

        // Restrict access for restaurant level users without an active subscription
        if ($user->role === 'RES') {
            $hasActiveSubscription = \DB::table('subscriptions')
                ->where('user_id', $user->restaurant_id)
                ->where('status', 'active')
                ->exists();

            if (!$hasActiveSubscription) {
                $allowedRoutes = [
                    'select.plan.page',
                    'admin.subscriptions.index',
                    'admin.subscriptions.create',
                    'admin.subscriptions.store',
                    'admin.subscriptions.payment',
                    'admin.subscriptions.payment.success',
                    'admin.subscriptions.payment.success.get',
                    'admin.subscriptions.payment.failed',
                    'admin.subscriptions.payment.failed.get',
                    'restaurant.support.tickets',
                    'restaurant-support',
                    'logout',
                    'logout.user'
                ];

                $routeName = $request->route()->getName();
                $path = $request->path();

                $isAllowed = false;
                foreach ($allowedRoutes as $allowed) {
                    if ($routeName === $allowed || str_contains($path, $allowed) || ($routeName && str_contains($routeName, $allowed))) {
                        $isAllowed = true;
                        break;
                    }
                }

                if (str_contains($path, 'subscribe') || str_contains($path, 'payment') || str_contains($path, 'support') || str_contains($path, 'logout')) {
                    $isAllowed = true;
                }

                if (!$isAllowed) {
                    return redirect()->route('select.plan.page')
                        ->with('error', 'Please subscribe to a plan to access this feature.');
                }
            }
        }

        // Get the route name or path
        $routeName = $request->route()->getName();
        $path = $request->path();

        // Restrict Super Admin routes to ONLY role == 'SA'
        $superAdminPatterns = [
            'manage.restaurant', 'manage-restaurant',
            'plans.', 'admin.plans', 'admin/plans',
            'admin.payment.history', 'payment-history',
            'admin.crm', 'crm',
            'admin.support.tickets', 'admin-support'
        ];

        foreach ($superAdminPatterns as $pattern) {
            // Exclude subscription paths/routes from plans restriction
            if ($pattern === 'admin/plans' && str_contains($path, 'subscribe')) {
                continue;
            }
            if (($routeName && str_starts_with($routeName, $pattern)) || str_contains($path, $pattern)) {
                if ($user->role !== 'SA') {
                    abort(403, 'Unauthorized. This page is accessible only by Super Administrators.');
                }
                return $next($request);
            }
        }

        // Restaurant ADMIN and Super Admin have all access by default
        if ($user->role !== 'RES' || $user->role_type === 'ADMIN') {
            return $next($request);
        }

        // Map route/path patterns to permission keys
        $mappings = [
            'menu_master' => ['manage-menu-category', 'manage.category'],
            'menu_availability' => ['menu-availability', 'menu.availability'],
            'table_master' => ['table-manage', 'table.manage'],
            'order_master' => ['order-management-dashboard', 'order.management.dashboard', 'order-create', 'order.create', 'order-edit', 'order.edit', 'order-save', 'order.save', 'order-update', 'order.update', 'order/payment', 'order.payment', 'order/print', 'order.print', 'order/receipt', 'order.receipt.pdf', 'order-item-delete', 'order.item.delete', 'add-payment', 'order.add.payment', 'delete-payment', 'order.delete.payment', 'get-payments', 'order.get.payments', 'invoice', 'order.invoice'],
            'kitchen_order' => ['kitchen-panel', 'manage.kitchen-panel', 'update-kitchen-status', 'update.kitchen.status', 'kitchen/orders/refresh', 'kitchen.orders.refresh'],
            'pending_order' => ['pending-temp-orders', 'temp.orders'],
            'restro_ai' => ['ask-ai'],
            'billing_subscription' => ['subscriptions', 'admin.subscriptions.index', 'plans/subscribe', 'admin.subscriptions.create', 'subscriptions/payment', 'admin.subscriptions.payment', 'razorpay/webhook'],
            'customer_support' => ['restaurant-support', 'restaurant.support.tickets'],
            'staff' => ['restaurant-staff', 'restaurant.staff.index'],
            'inventory_setting' => ['manage-units', 'manage.units', 'products/manage', 'products.manage', 'suppliers', 'suppliers.index', 'purchases', 'purchases.index', 'stock-outs', 'stock-outs.index', 'debit-notes', 'debit-notes.index', 'inventory/manage', 'inventory.manage', 'inventory/stock-report', 'inventory.stock-report', 'inventory.delete', 'products/store', 'products.store', 'products/update', 'products.update', 'products/delete', 'products.delete', 'products/import', 'products.import', 'products/export', 'products.export', 'products/download-sample', 'products.download-sample'],
            'reports' => ['report-top-analysis', 'order.report.top.analysis', 'report-order-analysis', 'order.report.analysis', 'report-order-management', 'order.report.management', 'item-gst-summary', 'report.item.gst.summary', 'inventory/live', 'inventory.live', 'order-report', 'order.report']
        ];

        // Check if current route/path matches any of the mapped permissions
        foreach ($mappings as $permission => $patterns) {
            foreach ($patterns as $pattern) {
                if (($routeName && str_starts_with($routeName, $pattern)) || str_contains($path, $pattern)) {
                    if (!$user->hasPermission($permission)) {
                        abort(403, 'Unauthorized access to this menu/module.');
                    }
                    return $next($request);
                }
            }
        }

        return $next($request);
    }
}
