<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\Dashboard;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Notification\NotificationController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\TableManageController;
use App\Http\Controllers\RestaurantStaffController;
use App\Http\Controllers\AIChatController;
use App\Http\Controllers\OrderManagementController; 
use App\Http\Controllers\FcmController;
use App\Http\Controllers\RestaurantAnalyticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PurchaseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('config:clear');
    $exitCode = Artisan::call('cache:clear');
    echo "clear-cache".$exitCode;
    // return what you want
});


Route::get('/', [App\Http\Controllers\FrontendController::class, 'index'])->name('home');
// Registration routes
Route::get('/register-restaurant', [App\Http\Controllers\FrontendController::class, 'create'])->name('restaurant.register.form');
Route::post('/register-restaurant', [App\Http\Controllers\FrontendController::class, 'store'])->name('restaurant.register');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/custom-login',[LoginController::class,'customLogin'])->name('custom.login');
Route::get('logout',[LoginController::class, 'logout'])->name('logout.user');



// Scan QR link: /customer/order/{table_id}/{restaurant_id}
    Route::get('order-customer/{table_id}/{restaurant_id}', [App\Http\Controllers\TempOrderController::class, 'create'])->name('temp.order.create');
    Route::post('order/store', [App\Http\Controllers\TempOrderController::class, 'store'])->name('temp.order.store');
    Route::get('/order-success/{id}', [App\Http\Controllers\TempOrderController::class, 'success'])->name('order.success');
Route::group(['middleware' => 'auth'], function () {

 Route::get('/select-plans', [App\Http\Controllers\PlanController::class, 'selectPlan'])->name('select.plan.page');




Route::get('dashboard',[Dashboard::class,'index'])->name('dashboard');

// Endpoint used by the dish dropdown. Implement it in the same controller:
Route::get('/dashboard/dish-monthly/{id}', [Dashboard::class, 'dishMonthly'])
    ->middleware('auth')
    ->name('dashboard.dish.monthly');

Route::post('/save-fcm-token', function (Request $request) {
    $request->validate([
        'fcm_token' => 'required|string',
    ]);

    $user = Auth::user();
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
    }

    $user->fcm_token = $request->fcm_token;
    $user->save();

    return response()->json(['success' => true]);
})->middleware('auth')->name('save.fcm.token');

// category-management


Route::prefix('admin')->group(function () {

    


    Route::post('/fcm/register', [FcmController::class, 'registerToken']);
    Route::post('/fcm/unregister', [FcmController::class, 'unregisterToken']);

Route::get('manage-restaurant', [RestaurantController::class, 'index'])->name('manage.restaurant');
Route::post('manage-restaurant/insert', [RestaurantController::class, 'store'])->name('manage.restaurant.insert');
Route::post('manage-restaurant/update', [RestaurantController::class, 'update'])->name('manage.restaurant.update');
Route::get('manage-restaurant/status/{id}', [RestaurantController::class, 'status'])->name('manage.restaurant.status');
Route::get('manage-restaurant/delete/{id}', [RestaurantController::class, 'delete'])->name('manage.restaurant.delete');

// manage-menu-category
Route::get('manage-menu-category',[App\Http\Controllers\Category\CategoryController::class,'index'])->name('manage.category');
Route::post('manage-menu-category/insert-category',[App\Http\Controllers\Category\CategoryController::class,'insert'])->name('manage.category.insert');
Route::post('manage-menu-category/update-category',[App\Http\Controllers\Category\CategoryController::class,'update'])->name('manage.category.update');
Route::get('manage-menu-category/delete-category/{id}',[App\Http\Controllers\Category\CategoryController::class,'delete'])->name('manage.category.delete');

Route::get('manage-category/manage-food-items/{id}',[App\Http\Controllers\Category\CategoryController::class,'subCategory'])->name('manage.subcategory.category');
Route::post('manage-category/manage-food-items/insert-sub-category',[App\Http\Controllers\Category\CategoryController::class,'subCategoryinsert'])->name('manage.subcategory.category.insert');
Route::post('manage-category/manage-food-items/update-sub-category',[App\Http\Controllers\Category\CategoryController::class,'subCategoryupdate'])->name('manage.subcategory.category.update');
Route::get('manage-category/manage-food-items/delete-sub-category/{id}',[App\Http\Controllers\Category\CategoryController::class,'subCategorydelete'])->name('manage.subcategory.category.delete');
Route::get('manage-category/manage-food-items/status-sub-category/{id}',[App\Http\Controllers\Category\CategoryController::class,'subCategorystatus'])->name('manage.subcategory.category.status');

Route::post('manage-category/manage-food-items/bulk-upload', [App\Http\Controllers\Category\CategoryController::class, 'bulkUpload'])->name('manage.subcategory.category.bulk.upload');
Route::get('manage-category/bulk-upload-template/{id}', [App\Http\Controllers\Category\CategoryController::class, 'downloadTemplate'])->name('manage.subcategory.category.template');

// manage-table
Route::get('table-manage', [TableManageController::class, 'index'])->name('table.manage');
Route::post('table-manage/insert', [TableManageController::class, 'store'])->name('table.manage.insert');
Route::post('table-manage/update', [TableManageController::class, 'update'])->name('table.manage.update');
Route::get('table-manage/status/{id}', [TableManageController::class, 'status'])->name('table.manage.status');
Route::get('table-manage/delete/{id}', [TableManageController::class, 'delete'])->name('table.manage.delete');
Route::get('/restaurant/table/{table_id}/{restaurant_id}', function($table_id,$restaurant_id){
    return "Table ID: $table_id <br> Restaurant ID: $restaurant_id";
});



    // Restaurant Analytics Dashboard
    Route::get('/restaurant/analytics/{id}', [App\Http\Controllers\RestaurantAnalyticsController::class, 'dashboard'])->name('restaurant.analytics');
    Route::post('/restaurant/analytics/filter/{id}', [App\Http\Controllers\RestaurantAnalyticsController::class, 'filter'])->name('restaurant.analytics.filter');
    
    // Restaurant API endpoints
    Route::get('/api/restaurant/daily-revenue/{id}', [App\Http\Controllers\RestaurantAnalyticsController::class, 'dailyRevenue'])->name('restaurant.daily.revenue');
    Route::get('/api/restaurant/top-items/{id}', [App\Http\Controllers\RestaurantAnalyticsController::class, 'topItems'])->name('restaurant.top.items');
    


// order-management
Route::get('/order/print/{order_id}', [App\Http\Controllers\OrderManagementController::class, 'pdfReceipt'])->name('order.print');
Route::get('order-management-dashboard', [App\Http\Controllers\OrderManagementController::class, 'index'])
    ->name('order.management.dashboard');

Route::get('order-create/{table_id?}', [App\Http\Controllers\OrderManagementController::class, 'create'])
    ->name('order.create');

Route::get('order-edit/{order_id}', [App\Http\Controllers\OrderManagementController::class, 'edit'])
    ->name('order.edit');

Route::post('order-save', [App\Http\Controllers\OrderManagementController::class, 'store'])
    ->name('order.save');

    // Invoice Page (iframe)
Route::get('order/{id}/invoice', [OrderManagementController::class, 'invoicePage'])
    ->name('order.invoice');

// PDF Receipt
Route::get('order/{id}/receipt-pdf', [OrderManagementController::class, 'pdfReceipt'])
    ->name('order.receipt.pdf');

Route::post('order-update/{order_id}', [App\Http\Controllers\OrderManagementController::class, 'update'])
    ->name('order.update');
Route::get('order/{order}/payment', [App\Http\Controllers\OrderManagementController::class, 'paymentPage'])->name('order.payment');
Route::post('order/{order}/payment', [App\Http\Controllers\OrderManagementController::class, 'submitPayment'])->name('order.payment.submit');
Route::post('order-item-delete/{id}', [App\Http\Controllers\OrderManagementController::class, 'deleteOrderItem'])
    ->name('order.item.delete');


// order-report
Route::get('order-report',[App\Http\Controllers\OrderFilterController::class,'index'])->name('order.report');    
Route::get('order-report/details/{order_id}',[App\Http\Controllers\OrderFilterController::class,'orderDetails'])->name('order.report.order.details'); 

// Product Management Routes
Route::get('products/manage', [App\Http\Controllers\ProductController::class, 'index'])->name('products.manage');
Route::post('products/store', [App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
Route::post('products/update', [App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
Route::get('products/delete/{id}', [App\Http\Controllers\ProductController::class, 'delete'])->name('products.delete');

// Excel Import Routes
Route::get('products/import', [App\Http\Controllers\ProductController::class, 'importView'])->name('products.import.view');
Route::post('products/import', [App\Http\Controllers\ProductController::class, 'import'])->name('products.import');

// Export Route (optional)
Route::get('products/export', [App\Http\Controllers\ProductController::class, 'export'])->name('products.export');

// Sample Excel Download Route
Route::get('products/download-sample', [App\Http\Controllers\ProductController::class, 'downloadSample'])->name('products.download-sample');





Route::get('inventory/manage', [App\Http\Controllers\InventoryController::class, 'index'])->name('inventory.manage');
Route::post('inventory/store', [App\Http\Controllers\InventoryController::class, 'store'])->name('inventory.store');
Route::post('inventory/update', [App\Http\Controllers\InventoryController::class, 'update'])->name('inventory.update');
Route::get('inventory/delete/{id}', [App\Http\Controllers\InventoryController::class, 'delete'])->name('inventory.delete');


// manage-role
Route::get('manage-role',[App\Http\Controllers\Role\RoleController::class,'index'])->name('manage.operations.role.management');
Route::post('manage-role/insert-role',[App\Http\Controllers\Role\RoleController::class,'insert'])->name('manage.operations.role.management.insert');
Route::post('manage-role/update-role',[App\Http\Controllers\Role\RoleController::class,'update'])->name('manage.operations.role.management.update');
Route::get('manage-role/delete-role/{id}',[App\Http\Controllers\Role\RoleController::class,'delete'])->name('manage.operations.role.management.delete');

// manage-staff
Route::get('manage-staff',[App\Http\Controllers\Staff\StaffController::class,'index'])->name('manage.operations.staff.management');
Route::post('manage-staff/insert',[App\Http\Controllers\Staff\StaffController::class,'insert'])->name('manage.operations.staff.management.insert');
Route::post('manage-staff/update',[App\Http\Controllers\Staff\StaffController::class,'update'])->name('manage.operations.staff.management.update');
Route::get('manage-staff/delete/{id}',[App\Http\Controllers\Staff\StaffController::class,'delete'])->name('manage.operations.staff.management.delete');

// kitchen-panel
Route::get('kitchen-panel', [App\Http\Controllers\OrderManagementController::class, 'kitchen'])->name('manage.kitchen-panel');
Route::post('update-kitchen-status', [App\Http\Controllers\OrderManagementController::class, 'updateKitchenStatus'])->name('update.kitchen.status');
// Add this route with your other kitchen routes
Route::get('kitchen/orders/refresh', [App\Http\Controllers\OrderManagementController::class, 'refreshOrders'])->name('kitchen.orders.refresh');

// restaurant-staff
Route::prefix('restaurant-staff')->group(function () {

    Route::get('/', [RestaurantStaffController::class, 'index'])
        ->name('restaurant.staff.index');

    Route::post('/insert', [RestaurantStaffController::class, 'insert'])
        ->name('restaurant.staff.insert');

    Route::post('/update', [RestaurantStaffController::class, 'update'])
        ->name('restaurant.staff.update');

    Route::get('/delete/{id}', [RestaurantStaffController::class, 'delete'])
        ->name('restaurant.staff.delete');
    Route::get('/status/{id}', [RestaurantStaffController::class, 'status'])
        ->name('restaurant.staff.status');
});
Route::get('/ask-ai', [AIChatController::class, 'index'])->name('ask-ai');
Route::post('/ask-ai/send', [AIChatController::class, 'send'])->name('ask-ai.send');


// temporary-order
// Temporary Orders - Admin
Route::get('/pending-temp-orders', [App\Http\Controllers\TempOrderAdminController::class, 'index'])->name('temp.orders');
Route::get('/pending-temp-orders/{id}', [App\Http\Controllers\TempOrderAdminController::class, 'view'])->name('temp.orders.view');
Route::get('/pending-temp-orders/delete-item/{id}', [App\Http\Controllers\TempOrderAdminController::class, 'deleteItem'])->name('temp.orders.view.delete.item');
Route::get('admin/temp-order/approve/{id}', [App\Http\Controllers\TempOrderAdminController::class, 'approveOrder'])
    ->name('admin.temporder.approve');

// Admin Plan Routes

    Route::resource('plans', \App\Http\Controllers\PlanController::class);
    Route::get('plans/{id}/history', [\App\Http\Controllers\PlanController::class, 'history'])->name('admin.plans.history');


    // Subscription routes
    Route::get('subscriptions', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('admin.subscriptions.index');
    Route::get('plans/{plan}/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'create'])->name('admin.subscriptions.create');
    Route::post('plans/{plan}/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'store'])->name('admin.subscriptions.store');
    Route::get('subscriptions/payment', [\App\Http\Controllers\SubscriptionController::class, 'payment'])->name('admin.subscriptions.payment');
    Route::post('subscriptions/payment-success', [\App\Http\Controllers\SubscriptionController::class, 'paymentSuccess'])->name('admin.subscriptions.payment.success');
    Route::get('subscriptions/payment-success', [\App\Http\Controllers\SubscriptionController::class, 'paymentSuccess'])->name('admin.subscriptions.payment.success.get');
    Route::post('subscriptions/payment-failed', [\App\Http\Controllers\SubscriptionController::class, 'paymentFailed'])->name('admin.subscriptions.payment.failed');
    Route::get('subscriptions/payment-failed', [\App\Http\Controllers\SubscriptionController::class, 'paymentFailed'])->name('admin.subscriptions.payment.failed.get');
    Route::delete('subscriptions/{id}/cancel', [\App\Http\Controllers\SubscriptionController::class, 'cancel'])->name('admin.subscriptions.cancel');
    Route::post('razorpay/webhook', [\App\Http\Controllers\Admin\WebhookController::class, 'handle']);
    
    
    // unit master
    Route::get('manage-units', [App\Http\Controllers\UnitMasterController::class, 'index'])->name('manage.units');
    Route::post('manage-units/insert', [App\Http\Controllers\UnitMasterController::class, 'insert'])->name('manage.units.insert');
    Route::post('manage-units/update', [App\Http\Controllers\UnitMasterController::class, 'update'])->name('manage.units.update');
    Route::get('manage-units/delete/{id}', [App\Http\Controllers\UnitMasterController::class, 'delete'])->name('manage.units.delete');




    Route::get('suppliers', [App\Http\Controllers\SupplierController::class, 'index'])->name('suppliers.index');
    Route::post('suppliers/store', [App\Http\Controllers\SupplierController::class, 'store'])->name('suppliers.store');
    Route::post('suppliers/update', [App\Http\Controllers\SupplierController::class, 'update'])->name('suppliers.update');
    Route::get('suppliers/delete/{id}', [App\Http\Controllers\SupplierController::class, 'delete'])->name('suppliers.delete');

    // Supplier Ledger Routes
    Route::get('suppliers/{id}/ledger', [App\Http\Controllers\SupplierLedgerController::class, 'ledger'])->name('suppliers.ledger');
    Route::post('suppliers/deposit/store', [App\Http\Controllers\SupplierLedgerController::class, 'storeDeposit'])->name('suppliers.deposit.store');
    Route::get('suppliers/deposit/delete/{id}', [App\Http\Controllers\SupplierLedgerController::class, 'deleteDeposit'])->name('suppliers.deposit.delete');

    // Export routes
    Route::get('suppliers/{id}/ledger/export', [App\Http\Controllers\SupplierLedgerController::class, 'exportLedger'])->name('suppliers.ledger.export');


    // Purchase Management Routes
    Route::get('purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::get('purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
    Route::post('purchases/store', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::get('purchases/{id}/edit', [PurchaseController::class, 'edit'])->name('purchases.edit');
    Route::post('purchases/update', [PurchaseController::class, 'update'])->name('purchases.update');
    Route::get('purchases/{id}', [PurchaseController::class, 'show'])->name('purchases.show');
    Route::get('purchases/delete/{id}', [PurchaseController::class, 'delete'])->name('purchases.delete');

    // AJAX Routes
    Route::get('purchases/get-product/{id}', [PurchaseController::class, 'getProduct'])->name('purchases.get-product');
    Route::get('purchases/check-stock/{productId}', [PurchaseController::class, 'checkStock'])->name('purchases.check-stock');

    // Inventory Report
    Route::get('inventory/stock-report', [PurchaseController::class, 'stockReport'])->name('inventory.stock-report');




    // Stock Out Management Routes
    Route::get('stock-outs', [App\Http\Controllers\StockOutController::class, 'index'])->name('stock-outs.index');
    Route::get('stock-outs/create', [App\Http\Controllers\StockOutController::class, 'create'])->name('stock-outs.create');
    Route::post('stock-outs/store', [App\Http\Controllers\StockOutController::class, 'store'])->name('stock-outs.store');
    Route::get('stock-outs/{id}/edit', [App\Http\Controllers\StockOutController::class, 'edit'])->name('stock-outs.edit');
    Route::post('stock-outs/update', [App\Http\Controllers\StockOutController::class, 'update'])->name('stock-outs.update');
    Route::get('stock-outs/{id}', [App\Http\Controllers\StockOutController::class, 'show'])->name('stock-outs.show');
    Route::get('stock-outs/delete/{id}', [App\Http\Controllers\StockOutController::class, 'delete'])->name('stock-outs.delete');

    // AJAX Routes
    Route::get('stock-outs/get-product/{id}', [App\Http\Controllers\StockOutController::class, 'getProduct'])->name('stock-outs.get-product');
    Route::get('stock-outs/check-stock/{productId}', [App\Http\Controllers\StockOutController::class, 'checkStock'])->name('stock-outs.check-stock');

    // Live Inventory Route
    Route::get('inventory/live', [App\Http\Controllers\InventoryController::class, 'live'])->name('inventory.live');



    // Reports Routes
    Route::get('report-top-analysis', [App\Http\Controllers\ReportController::class, 'topAnalysisReport'])->name('order.report.top.analysis');
    Route::get('report-order-analysis', [App\Http\Controllers\ReportController::class, 'orderAnalysisReport'])->name('order.report.analysis');
    Route::get('report-order-management', [App\Http\Controllers\ReportController::class, 'orderManagementReport'])->name('order.report.management');

    // Expense Management Routes
    Route::prefix('expense')->group(function () {
        Route::get('/', [App\Http\Controllers\ExpenseController::class, 'index'])->name('expense.index');
        Route::post('/', [App\Http\Controllers\ExpenseController::class, 'store'])->name('expense.store');
        Route::put('/{id}', [App\Http\Controllers\ExpenseController::class, 'update'])->name('expense.update');
        Route::delete('/{id}', [App\Http\Controllers\ExpenseController::class, 'destroy'])->name('expense.destroy');
        Route::get('/{id}', [App\Http\Controllers\ExpenseController::class, 'show'])->name('expense.show');
        Route::get('/export', [App\Http\Controllers\ExpenseController::class, 'export'])->name('expense.export');
    });


    Route::prefix('debit-notes')->group(function () {
    Route::get('/', [App\Http\Controllers\DebitNoteController::class, 'index'])->name('debit-notes.index');
    Route::get('/create', [App\Http\Controllers\DebitNoteController::class, 'create'])->name('debit-notes.create');
    Route::post('/store', [App\Http\Controllers\DebitNoteController::class, 'store'])->name('debit-notes.store');
    Route::get('/{id}', [App\Http\Controllers\DebitNoteController::class, 'show'])->name('debit-notes.show');
    Route::delete('/{id}', [App\Http\Controllers\DebitNoteController::class, 'destroy'])->name('debit-notes.destroy');
    
    // AJAX routes
    Route::get('/check-stock/{productId}', [App\Http\Controllers\DebitNoteController::class, 'checkStock'])->name('debit-notes.check-stock');
    Route::get('/get-product/{id}', [App\Http\Controllers\DebitNoteController::class, 'getProduct'])->name('debit-notes.get-product');
});
    

});

});


