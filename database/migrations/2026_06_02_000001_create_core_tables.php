<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. role
        Schema::create('role', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // 2. units
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status', 1)->default('A');
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });

        // 3. restaurant_master
        Schema::create('restaurant_master', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('pincode')->nullable();
            $table->string('gstin')->nullable();
            $table->decimal('gst_percentage', 8, 2)->default(0.00);
            $table->foreignId('owner_id')->nullable()->constrained('users');
            $table->string('status', 1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        // 4. category
        Schema::create('category', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('image')->nullable();
            $table->string('status', 1)->default('A');
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        // 5. table_management
        Schema::create('table_management', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description', 500)->nullable();
            $table->string('qr_code')->nullable();
            $table->string('table_status', 20)->default('AVAILABLE');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('status', 1)->default('A');
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        // 6. plans
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_parent_id')->nullable();
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('gst_percentage', 5, 2)->default(18.00);
            $table->decimal('taxable_amount', 10, 2)->default(0.00);
            $table->decimal('gst_amount', 10, 2)->default(0.00);
            $table->integer('country_id')->nullable();
            $table->string('currency', 10)->default('INR');
            $table->string('billing_cycle', 20);
            $table->integer('duration_days')->default(30);
            $table->text('description')->nullable();
            $table->integer('category_number')->default(0);
            $table->integer('total_number_of_dishes')->default(0);
            $table->integer('total_number_of_table')->default(0);
            $table->string('inventory_checkbox', 1)->default('N');
            $table->string('is_default_plan', 1)->default('N');
            $table->string('is_default_free', 1)->default('N');
            $table->string('is_default_paid', 1)->default('N');
            $table->string('razorpay_plan_id')->nullable();
            $table->string('is_delete', 1)->default('N');
            $table->dateTime('end_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('plan_parent_id')->references('id')->on('plans');
        });

        // 7. products
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('opening_qty', 12, 2)->default(0.00);
            $table->string('status', 1)->default('A');
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('unit_id')->references('id')->on('units');
        });

        // 8. suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->string('shop_name')->nullable();
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->decimal('opening_outstanding', 12, 2)->default(0.00);
            $table->decimal('current_outstanding', 12, 2)->default(0.00);
            $table->decimal('total_deposits', 12, 2)->default(0.00);
            $table->date('last_deposit_date')->nullable();
            $table->date('last_purchase_date')->nullable();
            $table->string('status', 1)->default('A');
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // 9. sub_category
        Schema::create('sub_category', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('gst_rate', 5, 2)->default(0.00);
            $table->string('food_type', 20)->default('VEG');
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->string('image')->nullable();
            $table->string('status', 1)->default('A');
            $table->foreignId('category_id')->constrained('category');
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        // 10. inventories
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('total_qty', 12, 2)->default(0.00);
            $table->decimal('opening_qty', 12, 2)->default(0.00);
            $table->string('created_by')->nullable();
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['product_id', 'restaurant_id']);
        });

        // 11. purchases
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 100);
            $table->date('purchase_date');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->integer('total_items')->default(0);
            $table->decimal('bill_amount', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->string('bill_attachment')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default('COMPLETED');
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // 12. purchase_items
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('purchases');
            $table->foreignId('product_id')->constrained('products');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('quantity', 12, 2)->default(0.00);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->timestamps();

            $table->foreign('unit_id')->references('id')->on('units');
        });

        // 13. stock_outs
        Schema::create('stock_outs', function (Blueprint $table) {
            $table->id();
            $table->string('stockout_no', 100);
            $table->date('stockout_date');
            $table->integer('total_items')->default(0);
            $table->text('remarks')->nullable();
            $table->string('status', 20)->default('COMPLETED');
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // 14. stockout_items
        Schema::create('stockout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stockout_id')->constrained('stock_outs');
            $table->foreignId('product_id')->constrained('products');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('quantity', 12, 2)->default(0.00);
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->timestamps();

            $table->foreign('unit_id')->references('id')->on('units');
        });

        // 15. debit_notes
        Schema::create('debit_notes', function (Blueprint $table) {
            $table->id();
            $table->string('debit_note_no', 100);
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->date('debit_date');
            $table->text('remarks')->nullable();
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        // 16. debit_note_items
        Schema::create('debit_note_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('debit_note_id')->constrained('debit_notes');
            $table->foreignId('product_id')->constrained('products');
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->decimal('quantity', 12, 3)->default(0.000);
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->timestamps();

            $table->foreign('unit_id')->references('id')->on('units');
        });

        // 17. orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_phone', 20)->nullable();
            $table->unsignedBigInteger('table_id')->nullable();
            $table->string('order_type', 20)->default('DINE_IN');
            $table->decimal('discount', 10, 2)->default(0.00);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->decimal('taxable_amount', 12, 2)->default(0.00);
            $table->decimal('gst_amount', 12, 2)->default(0.00);
            $table->decimal('cgst_amount', 12, 2)->default(0.00);
            $table->decimal('sgst_amount', 12, 2)->default(0.00);
            $table->decimal('igst_amount', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);
            $table->decimal('round_off', 8, 2)->default(0.00);
            $table->decimal('amount_paid', 12, 2)->default(0.00);
            $table->string('payment_status', 20)->default('PENDING');
            $table->string('payment_method', 50)->nullable();
            $table->text('remarks')->nullable();
            $table->string('order_status', 20)->default('PENDING');
            $table->string('order_complete', 20)->nullable();
            $table->string('is_gst_bill', 5)->default('NO');
            $table->decimal('restaurant_gst_percentage', 5, 2)->default(0.00);
            $table->string('restaurant_gstin', 50)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->timestamps();

            $table->foreign('table_id')->references('id')->on('table_management');
        });

        // 18. order_items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->unsignedBigInteger('subcategory_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('discounted_price', 10, 2)->default(0.00);
            $table->decimal('item_discount_percentage', 5, 2)->default(0.00);
            $table->decimal('taxable_amount', 12, 2)->default(0.00);
            $table->decimal('gst_rate', 5, 2)->default(0.00);
            $table->decimal('gst_amount', 12, 2)->default(0.00);
            $table->decimal('cgst_amount', 12, 2)->default(0.00);
            $table->decimal('sgst_amount', 12, 2)->default(0.00);
            $table->decimal('igst_amount', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->string('order_status', 20)->default('PENDING');
            $table->boolean('is_new')->default(true);
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('subcategory_id')->references('id')->on('sub_category');
        });

        // 19. plan_histories
        Schema::create('plan_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->constrained('plans');
            $table->string('name');
            $table->string('razorpay_plan_id')->nullable();
            $table->string('status', 5);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('gst_percentage', 5, 2)->default(18.00);
            $table->decimal('taxable_amount', 10, 2)->default(0.00);
            $table->decimal('gst_amount', 10, 2)->default(0.00);
            $table->integer('country_id')->nullable();
            $table->string('currency', 10)->default('INR');
            $table->string('billing_cycle', 20);
            $table->integer('duration_days')->default(30);
            $table->text('description')->nullable();
            $table->integer('category_number')->default(0);
            $table->integer('total_number_of_dishes')->default(0);
            $table->integer('total_number_of_table')->default(0);
            $table->string('inventory_checkbox', 1)->default('N');
            $table->string('is_default_free', 1)->default('N');
            $table->string('is_default_paid', 1)->default('N');
            $table->timestamps();
        });

        // 20. subscriptions
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreignId('plan_id')->constrained('plans');
            $table->string('razorpay_subscription_id')->nullable();
            $table->string('razorpay_plan_id')->nullable();
            $table->string('status', 20);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->dateTime('renewal_date')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->decimal('refund_amount', 10, 2)->default(0.00);
            $table->timestamps();
        });

        // 21. payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_order_id')->nullable();
            $table->string('razorpay_signature')->nullable();
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->decimal('refund_amount', 10, 2)->default(0.00);
            $table->string('currency', 10)->default('INR');
            $table->string('status', 20);
            $table->string('gst_percentage', 10)->default('18');
            $table->text('description')->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->text('razorpay_response')->nullable();
            $table->timestamps();

            $table->foreign('subscription_id')->references('id')->on('subscriptions');
            $table->foreign('plan_id')->references('id')->on('plans');
        });

        // 22. expenses
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->date('expense_date');
            $table->string('payment_method', 100)->nullable();
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users');
        });

        // 23. temp_orders
        Schema::create('temp_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('table_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_phone', 20)->nullable();
            $table->string('order_type', 20);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->decimal('taxable_amount', 12, 2)->default(0.00);
            $table->decimal('gst_amount', 12, 2)->default(0.00);
            $table->decimal('cgst_amount', 12, 2)->default(0.00);
            $table->decimal('sgst_amount', 12, 2)->default(0.00);
            $table->decimal('igst_amount', 12, 2)->default(0.00);
            $table->decimal('discount', 12, 2)->default(0.00);
            $table->decimal('discount_percentage', 5, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);
            $table->decimal('round_off', 8, 2)->default(0.00);
            $table->string('is_gst_bill', 5)->default('NO');
            $table->decimal('restaurant_gst_percentage', 5, 2)->default(0.00);
            $table->string('restaurant_gstin', 50)->nullable();
            $table->text('remarks')->nullable();
            $table->string('order_status', 20)->default('PENDING');
            $table->string('payment_status', 20)->default('PENDING');
            $table->string('payment_method', 50)->nullable();
            $table->decimal('amount_paid', 12, 2)->default(0.00);
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('table_id')->references('id')->on('table_management');
            $table->foreign('order_id')->references('id')->on('orders');
        });

        // 24. temp_order_items
        Schema::create('temp_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('temp_order_id')->constrained('temp_orders');
            $table->unsignedBigInteger('subcategory_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('discounted_price', 10, 2)->default(0.00);
            $table->decimal('item_discount_percentage', 5, 2)->default(0.00);
            $table->decimal('taxable_amount', 12, 2)->default(0.00);
            $table->decimal('gst_rate', 5, 2)->default(0.00);
            $table->decimal('gst_amount', 12, 2)->default(0.00);
            $table->decimal('cgst_amount', 12, 2)->default(0.00);
            $table->decimal('sgst_amount', 12, 2)->default(0.00);
            $table->decimal('igst_amount', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->string('order_status', 20)->default('PENDING');
            $table->boolean('is_new')->default(true);
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->timestamps();

            $table->foreign('subcategory_id')->references('id')->on('sub_category');
        });

        // 25. fcm_tokens
        Schema::create('fcm_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->text('token');
            $table->string('device_type', 50)->nullable();
            $table->timestamps();
        });

        // 26. razorpay_customers
        Schema::create('razorpay_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('rzpay_customer_id');
            $table->timestamps();
        });

        // 27. restaurant_to_custom_plan
        Schema::create('restaurant_to_custom_plan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->foreignId('plan_id')->constrained('plans');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
        });

        // 28. support_tickets
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no', 50);
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->string('subject');
            $table->text('message');
            $table->string('status', 20)->default('NEW');
            $table->string('priority', 20)->default('MEDIUM');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('assigned_to')->references('id')->on('users');
        });

        // 29. support_ticket_comments
        Schema::create('support_ticket_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('support_tickets');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type', 50)->nullable();
            $table->text('comment');
            $table->string('attachment')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });

        // 30. enquiry_management
        Schema::create('enquiry_management', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->text('query');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('status', 5)->default('NEW');
            $table->text('query_reply')->nullable();
            $table->unsignedBigInteger('replier_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('replier_by')->references('id')->on('users');
        });

        // 31. order_to_payments
        Schema::create('order_to_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('restaurant_id')->constrained('restaurant_master');
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->string('payment_method', 20);
            $table->string('transaction_no', 100)->nullable();
            $table->text('remarks')->nullable();
            $table->dateTime('payment_date');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_to_payments');
        Schema::dropIfExists('enquiry_management');
        Schema::dropIfExists('support_ticket_comments');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('restaurant_to_custom_plan');
        Schema::dropIfExists('razorpay_customers');
        Schema::dropIfExists('fcm_tokens');
        Schema::dropIfExists('temp_order_items');
        Schema::dropIfExists('temp_orders');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plan_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('debit_note_items');
        Schema::dropIfExists('debit_notes');
        Schema::dropIfExists('stockout_items');
        Schema::dropIfExists('stock_outs');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('inventories');
        Schema::dropIfExists('sub_category');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('products');
        Schema::dropIfExists('plans');
        Schema::dropIfExists('table_management');
        Schema::dropIfExists('category');
        Schema::dropIfExists('restaurant_master');
        Schema::dropIfExists('units');
        Schema::dropIfExists('role');
    }
};
