<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Super Admin (User 1)
        DB::table('users')->insertOrIgnore([
            'id' => 1,
            'name' => 'Super Administrator',
            'email' => 'admin@billbite.com',
            'password' => Hash::make('password'),
            'role' => 'SA',
            'role_type' => 'ADMIN',
            'phone' => '9999999999',
            'status' => 'A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Default Restaurant (Restaurant 1)
        DB::table('restaurant_master')->insertOrIgnore([
            'id' => 1,
            'restaurant_id_unique' => 'BILL-BITE-001',
            'name' => 'Bill & Bite Flagship Restaurant',
            'address' => 'Main Road, Silicon City',
            'pincode' => '560001',
            'gstin' => '27ABCDE1234F1Z5',
            'gst_percentage' => 5.00,
            'owner_id' => 2,
            'status' => 'A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Default Restaurant Owner User (User 2)
        DB::table('users')->insertOrIgnore([
            'id' => 2,
            'name' => 'Restaurant Owner',
            'email' => 'owner@billbite.com',
            'password' => Hash::make('123456'),
            'role' => 'RES',
            'role_type' => 'ADMIN',
            'restaurant_id' => 1,
            'phone' => '9876543210',
            'status' => 'A',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Default Plan (Plan 1)
        DB::table('plans')->insertOrIgnore([
            'id' => 1,
            'name' => 'Enterprise Diamond Plan',
            'price' => 2999,
            'duration_days' => 365,
            'billing_cycle' => 'monthly',
            'inventory_checkbox' => 'Y',
            'plan_status' => 'A',
            'is_delete' => 'N',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Active Subscription for Restaurant 1
        DB::table('subscriptions')->insertOrIgnore([
            'id' => 1,
            'user_id' => 1, // restaurant_id
            'plan_id' => 1,
            'status' => 'active',
            'start_date' => Carbon::now()->subMonths(1)->format('Y-m-d H:i:s'),
            'end_date' => Carbon::now()->addYears(2)->format('Y-m-d H:i:s'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Default Role entries
        DB::table('role')->insertOrIgnore([
            ['id' => 1, 'name' => 'ADMIN', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'MANAGER', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'CHEF', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'WAITER', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'CASHIER', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 7. Restaurants list to seed tables and menu for
        $restaurantIds = [1, 7];
        foreach ($restaurantIds as $rid) {
            // Ensure restaurant master exists
            if (!DB::table('restaurant_master')->where('id', $rid)->exists()) {
                DB::table('restaurant_master')->insertOrIgnore([
                    'id' => $rid,
                    'restaurant_id_unique' => 'BILL-BITE-R' . $rid . '-' . rand(1000, 9999),
                    'name' => 'Bill & Bite Restaurant #' . $rid,
                    'address' => 'Main Road, Food Street',
                    'pincode' => '560001',
                    'gstin' => '27ABCDE1234F1Z5',
                    'gst_percentage' => 5.00,
                    'owner_id' => 2,
                    'status' => 'A',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Ensure active subscription
            if (!DB::table('subscriptions')->where('user_id', $rid)->where('status', 'active')->exists()) {
                DB::table('subscriptions')->insert([
                    'user_id' => $rid,
                    'plan_id' => 1,
                    'status' => 'active',
                    'start_date' => Carbon::now()->subMonths(1)->format('Y-m-d H:i:s'),
                    'end_date' => Carbon::now()->addYears(2)->format('Y-m-d H:i:s'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 8. Seeding Tables (Table 1 through Table 12, VIP 1-2, Rooftop 1-2)
            $tables = [
                ['name' => 'Table 1', 'description' => 'Ground Floor Window Side', 'table_status' => 'AVAILABLE'],
                ['name' => 'Table 2', 'description' => 'Ground Floor Central Booth', 'table_status' => 'AVAILABLE'],
                ['name' => 'Table 3', 'description' => 'Ground Floor Corner Table', 'table_status' => 'AVAILABLE'],
                ['name' => 'Table 4', 'description' => 'Ground Floor 4-Seater', 'table_status' => 'AVAILABLE'],
                ['name' => 'Table 5', 'description' => 'Ground Floor 6-Seater Family', 'table_status' => 'AVAILABLE'],
                ['name' => 'Table 6', 'description' => 'First Floor Quiet Zone', 'table_status' => 'AVAILABLE'],
                ['name' => 'Table 7', 'description' => 'First Floor Window View', 'table_status' => 'AVAILABLE'],
                ['name' => 'Table 8', 'description' => 'First Floor 4-Seater', 'table_status' => 'AVAILABLE'],
                ['name' => 'Table 9', 'description' => 'First Floor 2-Seater Couple', 'table_status' => 'AVAILABLE'],
                ['name' => 'Table 10', 'description' => 'First Floor 6-Seater Family', 'table_status' => 'AVAILABLE'],
                ['name' => 'VIP Lounge 1', 'description' => 'Air Conditioned Private Dining', 'table_status' => 'AVAILABLE'],
                ['name' => 'VIP Lounge 2', 'description' => 'Executive Meeting Dining', 'table_status' => 'AVAILABLE'],
                ['name' => 'Rooftop 1', 'description' => 'Open Air Garden View', 'table_status' => 'AVAILABLE'],
                ['name' => 'Rooftop 2', 'description' => 'Open Air Sunset View', 'table_status' => 'AVAILABLE'],
            ];

            foreach ($tables as $tbl) {
                if (!DB::table('table_management')->where('restaurant_id', $rid)->where('name', $tbl['name'])->exists()) {
                    DB::table('table_management')->insert([
                        'restaurant_id' => $rid,
                        'name' => $tbl['name'],
                        'description' => $tbl['description'],
                        'table_status' => $tbl['table_status'],
                        'status' => 'A',
                        'user_id' => 2,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 9. Seeding Categories & Dishes
            $menuCategories = [
                'Starters & Appetizers' => [
                    ['name' => 'Paneer Tikka', 'price' => 240, 'food_type' => 'veg', 'description' => 'Marinated paneer grilled in tandoor'],
                    ['name' => 'Crispy Corn Salt & Pepper', 'price' => 190, 'food_type' => 'veg', 'description' => 'Crunchy sweet corn tossed with bell peppers'],
                    ['name' => 'Chicken Tikka', 'price' => 290, 'food_type' => 'non-veg', 'description' => 'Juicy boneless chicken marinated in spicy yogurt'],
                    ['name' => 'Fish Amritsari', 'price' => 340, 'food_type' => 'non-veg', 'description' => 'Crisp batter fried fish with ajwain flavor'],
                    ['name' => 'Veg Spring Rolls', 'price' => 180, 'food_type' => 'veg', 'description' => 'Crispy rolls filled with shredded vegetables'],
                ],
                'Main Course' => [
                    ['name' => 'Paneer Butter Masala', 'price' => 280, 'food_type' => 'veg', 'description' => 'Rich creamy tomato gravy with cottage cheese cubes'],
                    ['name' => 'Dal Makhani', 'price' => 240, 'food_type' => 'veg', 'description' => 'Slow cooked black lentils with fresh cream and butter'],
                    ['name' => 'Butter Chicken', 'price' => 360, 'food_type' => 'non-veg', 'description' => 'Tender chicken cooked in rich buttery tomato puree'],
                    ['name' => 'Mutton Rogan Josh', 'price' => 420, 'food_type' => 'non-veg', 'description' => 'Authentic Kashmiri style slow-braised lamb'],
                    ['name' => 'Kadhai Paneer', 'price' => 270, 'food_type' => 'veg', 'description' => 'Paneer tossed with capsicum and freshly ground spices'],
                ],
                'Rice & Biryani' => [
                    ['name' => 'Hyderabadi Chicken Biryani', 'price' => 320, 'food_type' => 'non-veg', 'description' => 'Dum-cooked aromatic basmati rice layered with spiced chicken'],
                    ['name' => 'Veg Dum Biryani', 'price' => 240, 'food_type' => 'veg', 'description' => 'Fragrant rice cooked with seasonal vegetables & herbs'],
                    ['name' => 'Mutton Dum Biryani', 'price' => 450, 'food_type' => 'non-veg', 'description' => 'Layered fragrant basmati rice with tender succulent mutton'],
                    ['name' => 'Jeera Rice', 'price' => 140, 'food_type' => 'veg', 'description' => 'Fluffy basmati rice tempered with roasted cumin seeds'],
                    ['name' => 'Steamed Basmati Rice', 'price' => 110, 'food_type' => 'veg', 'description' => 'Freshly steamed aromatic long grain rice'],
                ],
                'Breads & Tandoor' => [
                    ['name' => 'Butter Naan', 'price' => 50, 'food_type' => 'veg', 'description' => 'Clay oven baked flatbread brushed with butter'],
                    ['name' => 'Garlic Naan', 'price' => 65, 'food_type' => 'veg', 'description' => 'Tandoori naan topped with roasted minced garlic'],
                    ['name' => 'Tandoori Roti', 'price' => 25, 'food_type' => 'veg', 'description' => 'Whole wheat flatbread cooked in clay oven'],
                    ['name' => 'Butter Roti', 'price' => 30, 'food_type' => 'veg', 'description' => 'Whole wheat tandoori roti with fresh butter'],
                    ['name' => 'Cheese Garlic Naan', 'price' => 90, 'food_type' => 'veg', 'description' => 'Stuffed with mozzarella cheese and topped with garlic'],
                ],
                'Beverages & Mocktails' => [
                    ['name' => 'Fresh Lime Soda', 'price' => 70, 'food_type' => 'veg', 'description' => 'Refreshing lime drink sweet or salted'],
                    ['name' => 'Virgin Mojito', 'price' => 130, 'food_type' => 'veg', 'description' => 'Fresh mint leaves muddled with lime and soda'],
                    ['name' => 'Sweet Lassi', 'price' => 90, 'food_type' => 'veg', 'description' => 'Traditional chilled yogurt drink with cardamom'],
                    ['name' => 'Cold Coffee with Ice Cream', 'price' => 120, 'food_type' => 'veg', 'description' => 'Blended coffee topped with vanilla scoop'],
                    ['name' => 'Mineral Water Bottle', 'price' => 30, 'food_type' => 'veg', 'description' => '1L packaged drinking water'],
                ],
                'Desserts' => [
                    ['name' => 'Gulab Jamun (2 pcs)', 'price' => 80, 'food_type' => 'veg', 'description' => 'Warm milk solid dumplings soaked in rose sugar syrup'],
                    ['name' => 'Rasmalai (2 pcs)', 'price' => 100, 'food_type' => 'veg', 'description' => 'Soft cottage cheese patties in thickened saffron milk'],
                    ['name' => 'Sizzling Brownie with Ice Cream', 'price' => 160, 'food_type' => 'veg', 'description' => 'Hot chocolate fudge brownie served on sizzling platter'],
                ]
            ];

            foreach ($menuCategories as $catName => $dishes) {
                $category = DB::table('category')->where('restaurant_id', $rid)->where('name', $catName)->first();
                if (!$category) {
                    $catId = DB::table('category')->insertGetId([
                        'restaurant_id' => $rid,
                        'name' => $catName,
                        'description' => $catName . ' Collection',
                        'status' => 'A',
                        'user_id' => 2,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $catId = $category->id;
                }

                foreach ($dishes as $dish) {
                    if (!DB::table('sub_category')->where('restaurant_id', $rid)->where('name', $dish['name'])->exists()) {
                        DB::table('sub_category')->insert([
                            'restaurant_id' => $rid,
                            'category_id' => $catId,
                            'name' => $dish['name'],
                            'description' => $dish['description'],
                            'price' => $dish['price'],
                            'gst_rate' => 5.00,
                            'food_type' => $dish['food_type'],
                            'discount_percentage' => 0.00,
                            'is_available' => 1,
                            'status' => 'A',
                            'user_id' => 2,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            // 10. Suppliers & Units
            if (!DB::table('units')->where('restaurant_id', $rid)->where('name', 'Kg')->exists()) {
                DB::table('units')->insert([
                    ['name' => 'Kg', 'short_name' => 'kg', 'restaurant_id' => $rid, 'status' => 'A', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Litre', 'short_name' => 'L', 'restaurant_id' => $rid, 'status' => 'A', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Packet', 'short_name' => 'pkt', 'restaurant_id' => $rid, 'status' => 'A', 'created_at' => now(), 'updated_at' => now()],
                    ['name' => 'Piece', 'short_name' => 'pcs', 'restaurant_id' => $rid, 'status' => 'A', 'created_at' => now(), 'updated_at' => now()],
                ]);
            }

            if (!DB::table('suppliers')->where('restaurant_id', $rid)->where('name', 'Metro Fresh Supplies')->exists()) {
                DB::table('suppliers')->insert([
                    'name' => 'Metro Fresh Supplies',
                    'contact_person' => 'Rajesh Sharma',
                    'phone' => '9811223344',
                    'email' => 'metro_supplies@example.com',
                    'address' => 'Wholesale Market Block B',
                    'restaurant_id' => $rid,
                    'status' => 'A',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 11. Products & Inventory
            $products = [
                ['name' => 'Basmati Biryani Rice (50kg Bag)', 'unit' => 'Kg', 'cost_price' => 85, 'selling_price' => 110, 'stock' => 150],
                ['name' => 'Fresh Dairy Paneer (Block)', 'unit' => 'Kg', 'cost_price' => 220, 'selling_price' => 280, 'stock' => 35],
                ['name' => 'Pure Desi Ghee (15L Tin)', 'unit' => 'Litre', 'cost_price' => 550, 'selling_price' => 650, 'stock' => 20],
                ['name' => 'Farm Fresh Broiler Chicken', 'unit' => 'Kg', 'cost_price' => 160, 'selling_price' => 220, 'stock' => 45],
            ];

            foreach ($products as $p) {
                if (!DB::table('products')->where('restaurant_id', $rid)->where('name', $p['name'])->exists()) {
                    $pid = DB::table('products')->insertGetId([
                        'restaurant_id' => $rid,
                        'name' => $p['name'],
                        'unit' => $p['unit'],
                        'cost_price' => $p['cost_price'],
                        'selling_price' => $p['selling_price'],
                        'status' => 'A',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    DB::table('inventories')->insert([
                        'restaurant_id' => $rid,
                        'product_id' => $pid,
                        'opening_stock' => $p['stock'],
                        'current_stock' => $p['stock'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 12. Sample Expenses
            $expenses = [
                ['title' => 'Daily Fresh Dairy & Milk', 'category' => 'Raw Materials', 'amount' => 1250, 'payment_mode' => 'UPI', 'expense_date' => date('Y-m-d')],
                ['title' => 'Vegetable & Fruit Market', 'category' => 'Groceries', 'amount' => 2100, 'payment_mode' => 'Cash', 'expense_date' => date('Y-m-d')],
                ['title' => 'Electricity & Power Utility', 'category' => 'Utilities', 'amount' => 4500, 'payment_mode' => 'Online', 'expense_date' => date('Y-m-d', strtotime('-2 days'))],
            ];

            foreach ($expenses as $exp) {
                if (!DB::table('expenses')->where('restaurant_id', $rid)->where('title', $exp['title'])->exists()) {
                    DB::table('expenses')->insert([
                        'restaurant_id' => $rid,
                        'title' => $exp['title'],
                        'category' => $exp['category'],
                        'amount' => $exp['amount'],
                        'payment_mode' => $exp['payment_mode'],
                        'expense_date' => $exp['expense_date'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 13. Sample Completed Orders for Today and this week
            $customers = [
                ['name' => 'Vikram Malhotra', 'phone' => '9820011223', 'table' => 'Table 1', 'total' => 960, 'collected' => 960, 'due' => 0, 'mode' => 'UPI'],
                ['name' => 'Ananya Sen', 'phone' => '9830022334', 'table' => 'Table 3', 'total' => 1450, 'collected' => 1450, 'due' => 0, 'mode' => 'Card'],
                ['name' => 'Rohit Verma', 'phone' => '9840033445', 'table' => 'Table 5', 'total' => 620, 'collected' => 620, 'due' => 0, 'mode' => 'Cash'],
                ['name' => 'Deepak Gupta', 'phone' => '9810044556', 'table' => 'VIP Lounge 1', 'total' => 2350, 'collected' => 2000, 'due' => 350, 'mode' => 'UPI'],
            ];

            foreach ($customers as $idx => $c) {
                $orderNo = 'ORD-' . date('Ymd') . '-' . str_pad($rid . ($idx + 1), 4, '0', STR_PAD_LEFT);
                if (!DB::table('orders')->where('order_number', $orderNo)->exists()) {
                    $orderId = DB::table('orders')->insertGetId([
                        'restaurant_id' => $rid,
                        'order_number' => $orderNo,
                        'customer_name' => $c['name'],
                        'customer_phone' => $c['phone'],
                        'table_name' => $c['table'],
                        'total_amount' => $c['total'],
                        'order_status' => 'COMPLETED',
                        'payment_status' => $c['due'] > 0 ? 'PARTIALLY_PAID' : 'PAID',
                        'payment_method' => $c['mode'],
                        'paid_amount' => $c['collected'],
                        'due_amount' => $c['due'],
                        'order_date' => date('Y-m-d'),
                        'created_at' => now()->subHours(rand(1, 6)),
                        'updated_at' => now(),
                    ]);

                    // Add items for this order
                    $subItems = DB::table('sub_category')->where('restaurant_id', $rid)->take(3)->get();
                    foreach ($subItems as $sItem) {
                        DB::table('order_items')->insert([
                            'order_id' => $orderId,
                            'sub_category_id' => $sItem->id,
                            'item_name' => $sItem->name,
                            'item_price' => $sItem->price,
                            'quantity' => rand(1, 2),
                            'total_price' => $sItem->price * rand(1, 2),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
