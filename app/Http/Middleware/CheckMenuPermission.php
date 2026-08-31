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

        // Default Super Admin ID 1 can do anything by default
        if ($user->role === 'SA' && $user->id == 1) {
            return $next($request);
        }

        // Restrict access for restaurant level users without an active subscription
        if ($user->role === 'RES') {
            $hasActiveSubscription = \DB::table('subscriptions')
                ->where('user_id', $user->restaurant_id)
                ->whereIn('status', ['active', 'completed'])
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
                    'admin.subscriptions.invoice',
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

                if (str_contains($path, 'subscribe') || str_contains($path, 'payment') || str_contains($path, 'support') || str_contains($path, 'logout') || str_contains($path, 'invoice')) {
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
            'manage.restaurant' => 'restaurant_master', 
            'manage-restaurant' => 'restaurant_master',
            'plans.' => 'plan_master', 
            'admin.plans' => 'plan_master', 
            'admin/plans' => 'plan_master',
            'admin.payment.history' => 'payment_history', 
            'payment-history' => 'payment_history',
            'admin.crm' => 'admin_crm', 
            'crm' => 'admin_crm',
            'admin.support.tickets' => 'customer_support', 
            'admin-support' => 'customer_support',
            'admin.users' => 'admin_user_management',
            'admin/users' => 'admin_user_management'
        ];

        foreach ($superAdminPatterns as $pattern => $permission) {
            // Exclude subscription paths/routes from plans restriction
            if ($pattern === 'admin/plans' && str_contains($path, 'subscribe')) {
                continue;
            }
            if (($routeName && str_starts_with($routeName, $pattern)) || str_contains($path, $pattern)) {
                if ($user->role !== 'SA') {
                    abort(403, 'Unauthorized. This page is accessible only by Super Administrators.');
                }
                if ($user->id != 1) {
                    $perms = $user->permissions ?? [];
                    if (!in_array($permission, $perms)) {
                        abort(403, 'Unauthorized. You do not have permission to access this module.');
                    }
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
                    $granularModules = ['menu_master', 'table_master', 'staff', 'inventory_setting'];
                    if (in_array($permission, $granularModules)) {
                        $action = $this->getRequiredAction($request, $routeName, $path);
                    } else {
                        $action = 'view';
                    }

                    if (!$user->hasPermission($permission, $action)) {
                        abort(403, 'Unauthorized access to this menu/module.');
                    }
                    return $next($request);
                }
            }
        }

        return $next($request);
    }

    /**
     * Resolve the required action (view, add, edit, delete) for granular modules.
     */
    private function getRequiredAction(Request $request, $routeName, $path)
    {
        $method = strtoupper($request->method());

        // 1. DELETE Action
        if ($method === 'DELETE' || 
            $this->strContainsAny($path, ['delete', 'destroy', 'remove']) || 
            ($routeName && $this->strContainsAny($routeName, ['delete', 'destroy', 'remove']))) {
            return 'delete';
        }

        // 2. EDIT Action
        if ($this->strContainsAny($path, ['edit', 'update', 'status', 'toggle', 'modify']) || 
            ($routeName && $this->strContainsAny($routeName, ['edit', 'update', 'status', 'toggle', 'modify']))) {
            return 'edit';
        }

        // 3. ADD Action
        if ($method === 'POST' || 
            $this->strContainsAny($path, ['create', 'add', 'insert', 'store', 'save', 'bulk', 'upload', 'import']) || 
            ($routeName && $this->strContainsAny($routeName, ['create', 'add', 'insert', 'store', 'save', 'bulk', 'upload', 'import']))) {
            return 'add';
        }

        // 4. Default to VIEW Action
        return 'view';
    }

    /**
     * Helper to check if string contains any needle in the array.
     */
    private function strContainsAny($haystack, array $needles)
    {
        if (!$haystack) {
            return false;
        }
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }
}
