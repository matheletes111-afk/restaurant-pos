# Bill&Bite - Restaurant POS System: Feature List & Specifications

This document outlines the core and advanced features available within the Restaurant Panel and associated Admin operations of the Bill&Bite system.

---

## 1. Authentication & Onboarding
*Secure entry points and setup for restaurant managers and staff.*

### 1.1 Multi-Factor Custom Login with OTP
- **Description:** Enhanced security for system entry using a custom login flow integrated with one-time passwords (OTP) sent to registered credentials.
- **Workflow:** Validates email/password credentials, triggers OTP verification, and allows OTP resending if necessary.

### 1.2 Self-Service Password Recovery
- **Description:** Streamlined password recovery system to securely email password reset links to authorized personnel.
- **Workflow:** Verifies the user's email, sends a secure validation link, and permits the creation of a new password.

---

## 2. Dashboard & Performance Analytics
*Real-time business insights and key metrics.*

### 2.1 Central Sales Dashboard
- **Description:** High-level dashboard showing key performance indicators (KPIs) such as daily/monthly sales volume, active orders, and tables occupied.
- **Workflow:** Provides visual graphs of monthly dish performance and revenue trends.

### 2.2 Advanced Restaurant Analytics
- **Description:** Deep-dive reporting module offering dynamic date filtering to evaluate daily revenues, transaction volumes, and average ticket sizes.
- **Workflow:** Feeds API endpoints containing top-selling menu items and cumulative sales graphs.

---

## 3. Table & QR-Code Ordering System
*Dynamic table management and contact-free ordering.*

### 3.1 Digital Table Layout & Management
- **Description:** Configures physical restaurant layouts virtually, detailing tables by names/numbers and seating capacities.
- **Workflow:** Enables managers to insert, update, check status, or delete tables in the database.

### 3.2 Instant QR Code Generation
- **Description:** Generates unique QR codes for each physical table linking to a dedicated customer ordering web page.
- **Workflow:** Customers scan the table-specific QR code to access the digital menu, place their orders without waiting for staff, and receive order success statuses.

---

## 4. Point-of-Sale (POS) & Order Management
*Comprehensive processing of in-house, takeaway, and digital orders.*

### 4.1 POS Order Creation & Customization
- **Description:** A fast-paced screen for taking tableside or walk-in orders, editing existing orders, and dynamically modifying cart items.
- **Workflow:** Admins or servers select items, assign tables, and add custom notes or discounts to the order before sending it to the kitchen.

### 4.2 Flexible Payments & Multi-Billing
- **Description:** Manages multi-part, split, or partial payments per order, using cash, cards, or digital gateways (e.g. Razorpay).
- **Workflow:** Supports recording payment history, deleting incorrect payments, and keeping a live ledger of balances.

### 4.3 Invoice & KOT PDF Generation
- **Description:** Produces print-ready files for receipts and Kitchen Order Tickets (KOT).
- **Workflow:** Generates PDF receipts in customer-friendly formats and streamlined KOTs for quick kitchen coordination.

---

## 5. Kitchen Display System (KDS)
*Real-time updates and coordination for kitchen staff.*

### 5.1 Interactive Kitchen Panel
- **Description:** A dedicated screen designed for kitchen staff to view active dish preparation lists.
- **Workflow:** Displays pending items, groups items by tables, and allows status updates (e.g., in progress, ready, served).

### 5.2 Auto-Refresh & Synchronization
- **Description:** Ensures real-time responsiveness by constantly polling/refreshing incoming orders without requiring manual browser reloads.
- **Workflow:** Updates the kitchen interface instantly when a new customer QR order or POS order is submitted.

---

## 6. Menu & Category Management
*Cataloging food offerings and organizing price rules.*

### 6.1 Hierarchical Food Categorization
- **Description:** Organizes the menu into Categories (e.g., Appetizers, Main Course, Drinks) and Sub-Categories/Food Items.
- **Workflow:** Provides complete CRUD controls for menu structures, including image uploads and description fields.

### 6.2 Bulk Upload & Template Downloader
- **Description:** Allows batch imports of large food menus using Excel spreadsheets to quick-start menu configurations.
- **Workflow:** Users download a pre-formatted Excel template, input their food data, and upload it to import hundreds of items at once.

### 6.3 Menu Availability & Discounts
- **Description:** Offers toggle switches to set items as "Out of Stock" or "Available" instantly, along with percentage or flat discount rules per item.
- **Workflow:** Updates public-facing customer menus in real-time to avoid order cancellation issues.

---

## 7. Inventory & Unit Management
*Stock control and raw ingredient tracking.*

### 7.1 Material & Ingredient Inventory
- **Description:** Manages stock levels of core raw ingredients, packaging, and items.
- **Workflow:** Keeps track of current stock, minimum alert thresholds, and allows editing or removal of stock items.

### 7.2 Custom Measurement Units (Unit Master)
- **Description:** Standardizes measurements for inventory ingredients (e.g., Kgs, Liters, Grams, Pieces, Boxes).
- **Workflow:** Allows staff to define conversion rules and select appropriate units during purchase inputs.

### 7.3 Live Stock Checker
- **Description:** Real-time visibility into quantities on hand, alerting managers about items running low.
- **Workflow:** Dynamically queries current stock counts before placing purchase orders or recording wastage.

---

## 8. Procurement, Suppliers & Stock Operations
*B2B supply tracking and accounts payable.*

### 8.1 Supplier Directory & Profiles
- **Description:** Manages contact info, addresses, and tax numbers for raw material suppliers.
- **Workflow:** Integrates with procurement modules to associate purchases with specific suppliers.

### 8.2 Supplier Ledgers & Deposits
- **Description:** Tracks complete financial statements for suppliers, including payments made, credit limits, and balances.
- **Workflow:** Records deposits, payments, exportable ledgers, and balances to keep accurate account histories.

### 8.3 Purchase Order Management
- **Description:** Records incoming stock shipments, item cost prices, quantities, and reference invoices.
- **Workflow:** Increases inventory counts automatically upon order completion and updates supplier balances.

### 8.4 Stock Out & Wastage Records
- **Description:** Tracks ingredients consumed for daily prep or marked as waste due to expiration/spoilage.
- **Workflow:** Lowers inventory counts and tracks wastage reports to prevent leakage.

### 8.5 Debit Notes (Supplier Returns)
- **Description:** Handles returns of damaged or incorrect inventory materials back to suppliers.
- **Workflow:** Reduces supplier debt, updates inventory counts, and maintains records of returns.

---

## 9. Staff & Role-Based Access Control
*Security settings and employee management.*

### 9.1 Operations Role Manager
- **Description:** Defines specific system permissions for roles (e.g., Cashier, Waiter, Kitchen Staff, Manager).
- **Workflow:** Restricts access to financial metrics, settings, or inventory based on the active role.

### 9.2 Employee Directory & Active Status
- **Description:** Adds staff members, generates login credentials, and monitors active/inactive status.
- **Workflow:** Enables locking out former employees or pausing access during leave.

---

## 10. SaaS Subscription Plans & Payment Gateways
*Monetization and licensing rules for the platform.*

### 10.1 Multi-Tier Pricing Plans
- **Description:** Offers flexible subscription options (e.g., Trial, Gold, Platinum) with varied limitations.
- **Workflow:** Controls parameters like maximum allowed tables, menu items, or order volumes per plan.

### 10.2 Razorpay Integration & Auto-Renewal
- **Description:** Integrates online payment processing for subscriptions.
- **Workflow:** Handles credit cards, Razorpay webhooks for real-time validation, automatic renewals, and cancellations.

### 10.3 Manual Admin Plan Assignment
- **Description:** Allows SaaS super-admins to customize or assign specific plans directly to restaurants.
- **Workflow:** Overrides standard subscription flows for custom client deals.

---

## 11. Expense Management
*Tracking day-to-day operational expenditures.*

### 11.1 Expense Categorization & Logging
- **Description:** Records non-inventory bills like rent, electricity, maintenance, and marketing.
- **Workflow:** Supports receipt uploads, category creation, expense updates, and spreadsheet exports for tax planning.

---

## 12. Reporting & Fraud Prevention
*Securing data and checking tax compliance.*

### 12.1 Detailed Order and GST Reports
- **Description:** Generates exportable records detailing taxes (GST), gross margins, net profits, and cancellation summaries.
- **Workflow:** Provides filters for date ranges, payment types, and staff performance.

### 12.2 Staff OTP Verification for Order Deletion
- **Description:** Preventative control requiring authorized supervisor OTP verification before an order can be deleted.
- **Workflow:** Sends an OTP to the manager's registered contact to validate and log the deletion reasons.

---

## 13. Customer Support Tickets
*Customer service and resolution tracking.*

### 13.1 Support Ticket Management
- **Description:** Internal system allowing restaurant operators to submit bug reports or assistance tickets.
- **Workflow:** Users create tickets and add attachments. Staff reply, assign tickets, and toggle statuses to "Resolved".

