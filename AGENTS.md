# Bill&Bite - Restaurant POS System

## Stack
Laravel 9.x (PHP 8.0+) + MySQL + Bootstrap 5. Composer + npm/Vite. Razorpay payments.

## Scripts
- `npm run dev` - Vite dev server
- `npm run build` - Vite production build
- `php artisan serve` - Laravel dev server

## Structure
- `app/Http/Controllers/` - 38 controllers (Admin/, Api/, Auth/, Category/, Dashboard/, Order/, etc.)
- `app/Models/` - 37 models (User, Admin, RestaurantMaster, Category, SubCategory, OrderManage, OrderItems, TableManage, Product, Inventory, Purchase, Subscription, Plan, etc.)
- `app/Services/` - FirebasePushService, FirebaseNotificationService, FirebaseHttpService, WebNotificationService
- `resources/views/` - 36 Blade dirs/files (welcome, order, kitchen, restaurant, admin/crm, auth, emails, reports, etc.)
- `routes/` - web.php (442 lines), api.php (133 lines), admin.php
- `database/migrations/` - 10 migrations

## What It Does
Full restaurant management SaaS: menu management, table QR ordering, KOT/kitchen display, order management, inventory/purchases/suppliers, staff management, subscription plans (Razorpay), Firebase push notifications, CRM (demo leads), support tickets, expenses, reports, analytics dashboard.

## Env
APP_KEY, DB_*, RAZORPAY_KEY_ID/SECRET/WEBHOOK_SECRET, JWT_SECRET, MAIL_*, FIREBASE_CREDENTIALS_PATH/SERVER_KEY/VAPID_KEY
