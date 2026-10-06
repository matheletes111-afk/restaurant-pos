<!DOCTYPE html>
<html lang="en">
<head>
  <title>⚡ Rapid Bill POS • {{ $restaurant->name ?? 'Restaurant' }}</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('includes.style')

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">
  
  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    :root {
      --rp-primary: #ff5e14;
      --rp-primary-dark: #e04a08;
      --rp-primary-light: #fff3ed;
      --rp-primary-gradient: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);
      --rp-dark: #0f172a;
      --rp-slate: #1e293b;
      --rp-muted: #64748b;
      --rp-border: #e2e8f0;
      --rp-border-light: #f1f5f9;
      --rp-bg: #f4f6f9;
      --rp-card: #ffffff;
      --rp-success: #10b981;
      --rp-success-dark: #059669;
      --rp-success-bg: #ecfdf5;
      --rp-danger: #ef4444;
      --rp-danger-bg: #fef2f2;
      --rp-upi: #6366f1;
      --rp-upi-gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
      --rp-cash: #059669;
      --rp-cash-gradient: linear-gradient(135deg, #059669 0%, #10b981 100%);
      --rp-radius-lg: 16px;
      --rp-radius-md: 12px;
      --rp-radius-sm: 8px;
    }

    *, *::before, *::after {
      box-sizing: border-box;
    }

    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      background-color: var(--rp-bg);
      color: var(--rp-slate);
      -webkit-font-smoothing: antialiased;
      overflow-x: hidden;
    }

    /* Top Navigation Bar */
    .rapid-topbar {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      color: #ffffff;
      padding: 10px 18px;
      border-radius: var(--rp-radius-lg);
      margin-bottom: 14px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 8px 20px rgba(15, 23, 42, 0.12);
      border-bottom: 3px solid var(--rp-primary);
    }

    .rapid-brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .btn-rapid-hamburger {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.12);
      border: 1px solid rgba(255, 255, 255, 0.25);
      color: #ffffff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.15rem;
      cursor: pointer;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      flex-shrink: 0;
    }

    .btn-rapid-hamburger:hover,
    .btn-rapid-hamburger:active {
      background: var(--rp-primary);
      border-color: var(--rp-primary);
      color: #ffffff;
      transform: scale(1.05);
      box-shadow: 0 4px 14px rgba(255, 94, 20, 0.4);
    }

    /* Sidebar Drawer & Toggle CSS Overrides */
    .pc-sidebar {
      z-index: 1030 !important;
    }

    @media (min-width: 1025px) {
      .pc-sidebar {
        transition: width 0.25s ease, margin 0.25s ease, transform 0.25s ease !important;
      }
      .pc-sidebar.pc-sidebar-hide {
        width: 0 !important;
        min-width: 0 !important;
        overflow: hidden !important;
        visibility: hidden !important;
        border: none !important;
        transform: translateX(-100%) !important;
      }
      .pc-sidebar.pc-sidebar-hide ~ .pc-container {
        margin-left: 0 !important;
      }
    }

    @media (max-width: 1024px) {
      .pc-sidebar {
        position: fixed !important;
        top: 0 !important;
        bottom: 0 !important;
        left: -280px !important;
        width: 280px !important;
        max-width: 85vw !important;
        transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: none !important;
      }
      .pc-sidebar.mob-sidebar-active {
        left: 0 !important;
        box-shadow: 10px 0 40px rgba(0, 0, 0, 0.5) !important;
      }
      .pc-menu-overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        bottom: 0 !important;
        right: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: rgba(15, 23, 42, 0.6) !important;
        backdrop-filter: blur(4px) !important;
        -webkit-backdrop-filter: blur(4px) !important;
        z-index: 1020 !important;
      }
    }

    .rapid-brand-icon {
      width: 40px;
      height: 40px;
      background: var(--rp-primary-gradient);
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 1.2rem;
      box-shadow: 0 4px 10px rgba(255, 94, 20, 0.35);
      flex-shrink: 0;
    }

    .rapid-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.25rem;
      font-weight: 800;
      color: #ffffff;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 6px;
      flex-wrap: wrap;
    }

    .rapid-badge-speed {
      background: rgba(255, 94, 20, 0.2);
      border: 1px solid rgba(255, 94, 20, 0.4);
      color: #ff9d66;
      font-size: 0.68rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      padding: 2px 8px;
      border-radius: 16px;
    }

    .rapid-top-actions {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-shrink: 0;
    }

    .rapid-btn-secondary {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #ffffff !important;
      padding: 6px 14px;
      border-radius: 24px;
      font-size: 0.82rem;
      font-weight: 700;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s ease;
      white-space: nowrap;
    }

    .rapid-btn-secondary:hover {
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff !important;
      transform: translateY(-1px);
    }

    /* Main Grid Layout */
    .rapid-workspace {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 420px;
      gap: 16px;
      align-items: start;
      width: 100%;
      max-width: 100%;
      box-sizing: border-box;
    }

    .rapid-workspace > * {
      min-width: 0;
    }

    /* Mobile Segmented Switcher */
    .mobile-pos-switcher {
      display: none;
      background: #e2e8f0;
      padding: 4px;
      border-radius: 30px;
      gap: 4px;
      margin-bottom: 12px;
      width: 100%;
    }

    .mobile-pos-tab {
      flex: 1;
      border: none;
      background: transparent;
      padding: 9px 12px;
      border-radius: 24px;
      font-size: 0.88rem;
      font-weight: 700;
      color: var(--rp-muted);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.2s ease;
    }

    .mobile-pos-tab.active {
      background: #ffffff;
      color: var(--rp-dark);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .mobile-pos-tab.active.tab-bill {
      background: var(--rp-primary);
      color: #ffffff;
    }

    /* Mobile Floating Cart Bar */
    .mobile-floating-cart-bar {
      display: none;
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      color: #ffffff;
      padding: 10px 16px;
      z-index: 1040;
      box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.25);
      border-top: 2px solid var(--rp-primary);
      animation: slideUpFloat 0.25s ease-out;
    }

    @keyframes slideUpFloat {
      from { transform: translateY(100%); }
      to { transform: translateY(0); }
    }

    /* Menu Section Styles */
    .menu-panel {
      background: var(--rp-card);
      border-radius: var(--rp-radius-lg);
      border: 1.5px solid var(--rp-border);
      padding: 16px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
      min-width: 0;
    }

    .menu-filters-bar {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-bottom: 14px;
    }

    .search-veg-row {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .search-input-wrap {
      position: relative;
      flex: 1;
      min-width: 180px;
    }

    .search-input-wrap i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--rp-muted);
      font-size: 0.9rem;
    }

    .search-input {
      width: 100%;
      border: 1.5px solid var(--rp-border);
      border-radius: 30px;
      padding: 8px 16px 8px 36px;
      font-size: 0.88rem;
      font-weight: 600;
      background: #f8fafc;
      outline: none;
      transition: all 0.2s ease;
      box-sizing: border-box;
    }

    .search-input:focus {
      background: #ffffff;
      border-color: var(--rp-primary);
      box-shadow: 0 0 0 3px rgba(255, 94, 20, 0.12);
    }

    .search-clear-btn {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: transparent;
      border: none;
      color: var(--rp-muted);
      cursor: pointer;
      display: none;
      padding: 2px;
    }

    /* Food Type Switcher */
    .food-type-toggles {
      display: flex;
      align-items: center;
      background: #f1f5f9;
      border-radius: 30px;
      padding: 3px;
      gap: 2px;
      flex-shrink: 0;
    }

    .type-toggle-btn {
      border: none;
      background: transparent;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.78rem;
      font-weight: 700;
      color: var(--rp-muted);
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 5px;
      transition: all 0.2s ease;
    }

    .type-toggle-btn.active {
      background: #ffffff;
      color: var(--rp-dark);
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .type-toggle-btn.active.veg { color: var(--rp-success-dark); }
    .type-toggle-btn.active.nonveg { color: var(--rp-danger); }

    /* Category Filter Tabs */
    .category-scroll-tabs {
      display: flex;
      align-items: center;
      gap: 6px;
      overflow-x: auto;
      padding-bottom: 4px;
      scrollbar-width: thin;
      width: 100%;
    }

    .category-scroll-tabs::-webkit-scrollbar { height: 4px; }
    .category-scroll-tabs::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .cat-tab-btn {
      white-space: nowrap;
      border: 1.5px solid var(--rp-border);
      background: #f8fafc;
      color: var(--rp-slate);
      padding: 6px 12px;
      border-radius: 24px;
      font-size: 0.78rem;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 5px;
      transition: all 0.2s ease;
      flex-shrink: 0;
    }

    .cat-tab-btn:hover {
      background: #f1f5f9;
      border-color: #cbd5e1;
    }

    .cat-tab-btn.active {
      background: var(--rp-primary);
      border-color: var(--rp-primary);
      color: #ffffff;
      box-shadow: 0 3px 10px rgba(255, 94, 20, 0.3);
    }

    .cat-tab-badge {
      background: rgba(0, 0, 0, 0.08);
      padding: 1px 6px;
      border-radius: 10px;
      font-size: 0.7rem;
    }

    .cat-tab-btn.active .cat-tab-badge {
      background: rgba(255, 255, 255, 0.25);
      color: #ffffff;
    }

    /* Dish Cards Grid */
    .dishes-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
      gap: 10px;
      max-height: calc(100vh - 240px);
      overflow-y: auto;
      overflow-x: hidden;
      padding-right: 4px;
    }

    .dishes-grid::-webkit-scrollbar { width: 5px; }
    .dishes-grid::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .dish-card {
      background: #ffffff;
      border: 1.5px solid var(--rp-border);
      border-radius: var(--rp-radius-md);
      padding: 10px;
      cursor: pointer;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      user-select: none;
      min-width: 0;
      box-sizing: border-box;
    }

    .dish-card:hover {
      border-color: var(--rp-primary);
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(255, 94, 20, 0.12);
    }

    .dish-card.in-cart {
      border-color: var(--rp-primary);
      background: #fffcf8;
      box-shadow: 0 0 0 1px var(--rp-primary);
    }

    .dish-top-meta {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 5px;
      gap: 4px;
    }

    .food-symbol {
      width: 15px;
      height: 15px;
      border: 1.5px solid #ccc;
      border-radius: 3px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 1px;
      flex-shrink: 0;
    }

    .food-symbol.veg { border-color: #10b981; }
    .food-symbol.veg::after {
      content: '';
      width: 7px;
      height: 7px;
      background: #10b981;
      border-radius: 50%;
    }

    .food-symbol.nonveg { border-color: #ef4444; }
    .food-symbol.nonveg::after {
      content: '';
      width: 7px;
      height: 7px;
      background: #ef4444;
      border-radius: 50%;
    }

    .dish-cart-badge {
      background: var(--rp-primary);
      color: #ffffff;
      font-size: 0.72rem;
      font-weight: 800;
      padding: 1px 6px;
      border-radius: 10px;
      display: none;
    }

    .dish-card.in-cart .dish-cart-badge {
      display: inline-block;
    }

    .dish-title {
      font-family: 'Outfit', sans-serif;
      font-size: 0.88rem;
      font-weight: 700;
      color: var(--rp-dark);
      margin: 0 0 3px 0;
      line-height: 1.25;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      min-height: 2.2em;
    }

    .dish-category-label {
      font-size: 0.7rem;
      color: var(--rp-muted);
      font-weight: 600;
      margin-bottom: 6px;
    }

    .dish-bottom-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: auto;
      padding-top: 5px;
      border-top: 1px dashed var(--rp-border-light);
    }

    .dish-price {
      font-family: 'Outfit', sans-serif;
      font-size: 0.98rem;
      font-weight: 800;
      color: var(--rp-dark);
    }

    .dish-add-btn {
      width: 26px;
      height: 26px;
      background: var(--rp-primary-light);
      border: 1px solid rgba(255, 94, 20, 0.3);
      color: var(--rp-primary);
      border-radius: 7px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.8rem;
      transition: all 0.15s ease;
      flex-shrink: 0;
    }

    .dish-card:hover .dish-add-btn {
      background: var(--rp-primary);
      color: #ffffff;
    }

    /* Right Checkout Pane */
    .bill-checkout-panel {
      background: #ffffff;
      border-radius: var(--rp-radius-lg);
      border: 1.5px solid var(--rp-border);
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
      display: flex;
      flex-direction: column;
      position: sticky;
      top: 16px;
      max-height: calc(100vh - 32px);
      min-width: 0;
      width: 100%;
      box-sizing: border-box;
      overflow: hidden;
    }

    .checkout-header {
      background: #f8fafc;
      padding: 10px 14px;
      border-bottom: 1.5px solid var(--rp-border);
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-shrink: 0;
    }

    .checkout-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.05rem;
      font-weight: 800;
      color: var(--rp-dark);
      margin: 0;
      display: flex;
      align-items: center;
      gap: 6px;
    }

    .btn-clear-all {
      background: transparent;
      border: none;
      color: var(--rp-danger);
      font-size: 0.78rem;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 4px;
      padding: 3px 6px;
      border-radius: 6px;
      flex-shrink: 0;
    }

    .btn-clear-all:hover {
      background: var(--rp-danger-bg);
    }

    /* Customer & Dining Meta Row */
    .customer-meta-box {
      padding: 10px 14px 8px;
      background: #ffffff;
      border-bottom: 1px solid var(--rp-border-light);
      flex-shrink: 0;
    }

    .customer-input-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 6px;
      margin-bottom: 6px;
    }

    .c-input {
      width: 100%;
      min-width: 0;
      border: 1px solid var(--rp-border);
      border-radius: 6px;
      padding: 6px 8px;
      font-size: 0.8rem;
      font-weight: 600;
      background: #f8fafc;
      outline: none;
      box-sizing: border-box;
    }

    .c-input:focus {
      background: #ffffff;
      border-color: var(--rp-primary);
    }

    .dining-type-selector {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 6px;
    }

    .dining-btn {
      border: 1.5px solid var(--rp-border);
      background: #f8fafc;
      border-radius: 6px;
      padding: 5px 8px;
      font-size: 0.76rem;
      font-weight: 700;
      color: var(--rp-slate);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 4px;
      transition: all 0.15s ease;
      white-space: nowrap;
    }

    .dining-btn.active {
      background: #0f172a;
      border-color: #0f172a;
      color: #ffffff;
    }

    .table-select-wrap {
      margin-top: 6px;
      display: none;
    }

    /* Cart Items Container */
    .cart-items-wrapper {
      flex: 1 1 auto;
      min-height: 90px;
      max-height: 240px;
      overflow-y: auto;
      overflow-x: hidden;
      padding: 6px 12px;
      background: #ffffff;
      border-bottom: 1.5px solid var(--rp-border);
    }

    .cart-items-wrapper::-webkit-scrollbar { width: 4px; }
    .cart-items-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .cart-empty-state {
      padding: 24px 12px;
      text-align: center;
      color: var(--rp-muted);
    }

    .cart-empty-icon {
      font-size: 1.8rem;
      color: #cbd5e1;
      margin-bottom: 4px;
    }

    /* Cart Item Card & Main Row */
    .cart-item-card {
      padding: 7px 0 5px;
      border-bottom: 1px dashed var(--rp-border);
      transition: background 0.15s ease;
      min-width: 0;
      width: 100%;
    }

    .cart-item-card:last-child {
      border-bottom: none;
    }

    .cart-item-main-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 6px;
      min-width: 0;
      width: 100%;
    }

    .cart-item-info {
      flex: 1 1 auto;
      min-width: 0;
      overflow: hidden;
      padding-right: 2px;
    }

    .cart-item-title {
      font-size: 0.84rem;
      font-weight: 700;
      color: var(--rp-dark);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      display: block;
      min-width: 0;
    }

    .cart-item-price-meta {
      font-size: 0.7rem;
      color: var(--rp-muted);
      font-weight: 600;
      margin-top: 1px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .cart-item-actions-wrap {
      display: flex;
      align-items: center;
      gap: 4px;
      flex-shrink: 0;
    }

    /* Associated Addons Nested Panel & Table */
    .cart-dish-addons-panel {
      margin-top: 5px;
      margin-left: 6px;
      background: #fffcf8;
      border: 1px solid #ffedd5;
      border-left: 3px solid #ff5e14;
      border-radius: 6px;
      padding: 4px 6px;
      box-shadow: 0 1px 3px rgba(255, 94, 20, 0.05);
      animation: fadeIn 0.15s ease-out;
    }

    .addons-panel-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 3px;
      padding-bottom: 2px;
      border-bottom: 1px dashed #fed7aa;
    }

    .addons-panel-title {
      font-size: 0.7rem;
      font-weight: 800;
      color: #c2410c;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .btn-clear-all-addons {
      background: transparent;
      border: none;
      color: #ef4444;
      font-size: 0.65rem;
      font-weight: 700;
      cursor: pointer;
      padding: 0 3px;
    }

    .btn-clear-all-addons:hover {
      text-decoration: underline;
    }

    .addons-nested-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.72rem;
    }

    .nested-addon-row td {
      padding: 2px 2px;
      vertical-align: middle;
    }

    .addon-td-name {
      display: flex;
      align-items: center;
      gap: 4px;
      font-weight: 600;
      color: #1e293b;
      max-width: 110px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .addon-name-txt {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .addon-td-rate {
      font-size: 0.68rem;
      color: var(--rp-muted);
      font-weight: 600;
      text-align: right;
      padding-right: 3px !important;
      white-space: nowrap;
    }

    .addon-td-qty {
      text-align: center;
      width: 50px;
    }

    .nested-addon-qty-ctrl {
      display: inline-flex;
      align-items: center;
      background: #ffffff;
      border: 1px solid #cbd5e1;
      border-radius: 4px;
      padding: 1px;
    }

    .nested-addon-qty-ctrl .btn-addon-chip-dec,
    .nested-addon-qty-ctrl .btn-addon-chip-inc {
      width: 15px;
      height: 15px;
      background: #f1f5f9;
      border: none;
      border-radius: 2px;
      font-size: 0.65rem;
      font-weight: 800;
      color: #334155;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      padding: 0;
      transition: all 0.1s ease;
    }

    .nested-addon-qty-ctrl .btn-addon-chip-dec:hover,
    .nested-addon-qty-ctrl .btn-addon-chip-inc:hover {
      background: var(--rp-primary);
      color: #ffffff;
    }

    .nested-addon-qty-num {
      font-size: 0.72rem;
      font-weight: 800;
      min-width: 14px;
      text-align: center;
      color: #0f172a;
    }

    .addon-td-total {
      font-family: 'Outfit', sans-serif;
      font-weight: 800;
      font-size: 0.75rem;
      color: #0f172a;
      text-align: right;
      white-space: nowrap;
    }

    .addon-td-del {
      text-align: right;
      width: 16px;
    }

    .addon-td-del .btn-remove-addon {
      background: transparent;
      border: none;
      color: #94a3b8;
      cursor: pointer;
      padding: 1px 2px;
      font-size: 0.72rem;
      transition: color 0.15s ease;
    }

    .addon-td-del .btn-remove-addon:hover {
      color: #ef4444;
    }

    /* Addon Badges & Customization in Rapid Bill */
    .dish-addon-pill {
      background: #fff3ed;
      color: #ff5e14;
      border: 1px solid #ffdecb;
      font-size: 0.62rem;
      font-weight: 700;
      border-radius: 8px;
      padding: 1px 5px;
      display: inline-flex;
      align-items: center;
      gap: 3px;
    }

    .btn-customize-addons {
      background: #f0f9ff;
      border: 1px solid #bae6fd;
      color: #0284c7;
      border-radius: 5px;
      font-size: 0.65rem;
      font-weight: 700;
      padding: 1px 5px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 2px;
      transition: all 0.15s ease;
      text-decoration: none;
      white-space: nowrap;
      flex-shrink: 0;
    }

    .btn-customize-addons:hover {
      background: #0284c7;
      color: #ffffff;
      border-color: #0284c7;
    }

    .btn-customize-addons.active {
      background: #fff3ed;
      border-color: #ffdecb;
      color: #ff5e14;
    }

    .btn-customize-addons.active:hover {
      background: #ff5e14;
      color: #ffffff;
      border-color: #ff5e14;
    }

    .food-symbol-micro {
      width: 7px;
      height: 7px;
      border-radius: 50%;
      display: inline-block;
      flex-shrink: 0;
    }

    .food-symbol-micro.veg {
      background: #10b981;
    }

    .food-symbol-micro.nonveg {
      background: #ef4444;
    }

    .addon-selection-row {
      display: flex !important;
      align-items: center;
      justify-content: space-between;
      padding: 7px 10px;
      border-radius: 8px;
      background: #f8fafc;
      border: 1.5px solid #e2e8f0;
      cursor: pointer;
      transition: all 0.15s ease;
      user-select: none;
      margin-bottom: 5px;
    }

    .addon-selection-row:hover {
      background: #fff8f5;
      border-color: #ff5e14;
    }

    .addon-selection-row.is-selected {
      background: #fff9f6;
      border-color: #ff5e14;
      box-shadow: 0 2px 6px rgba(255, 94, 20, 0.12);
    }

    .addon-selection-row.is-hidden-addon {
      display: none !important;
    }

    .addon-modal-qty-control {
      display: inline-flex;
      align-items: center;
      background: #ffffff;
      border: 1.5px solid var(--rp-primary);
      border-radius: 5px;
      padding: 1px 2px;
      gap: 3px;
    }

    .btn-addon-modal-qty {
      width: 20px;
      height: 20px;
      background: #fff3ed;
      border: none;
      border-radius: 3px;
      color: var(--rp-primary);
      font-size: 0.8rem;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.1s ease;
      line-height: 1;
      padding: 0;
    }

    .btn-addon-modal-qty:hover {
      background: var(--rp-primary);
      color: #ffffff;
    }

    .addon-modal-qty-val {
      min-width: 16px;
      text-align: center;
      font-weight: 800;
      font-size: 0.8rem;
      color: var(--rp-dark);
    }

    /* Qty Control */
    .qty-control {
      display: inline-flex;
      align-items: center;
      background: #f1f5f9;
      border-radius: 5px;
      padding: 1px;
      flex-shrink: 0;
    }

    .btn-qty {
      width: 19px;
      height: 19px;
      border: none;
      background: #ffffff;
      border-radius: 3px;
      font-size: 0.72rem;
      font-weight: 800;
      color: var(--rp-slate);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.1s ease;
      padding: 0;
    }

    .btn-qty:hover { background: var(--rp-primary); color: #fff; }

    .qty-display {
      min-width: 18px;
      text-align: center;
      font-size: 0.76rem;
      font-weight: 800;
      color: var(--rp-dark);
      padding: 0 1px;
    }

    /* Item-wise Discount Box */
    .item-disc-wrap {
      display: inline-flex;
      align-items: center;
      background: #f8fafc;
      border: 1px solid var(--rp-border);
      border-radius: 5px;
      padding: 1px 3px;
      gap: 1px;
      flex-shrink: 0;
    }

    .item-disc-label {
      font-size: 0.65rem;
      font-weight: 700;
      color: var(--rp-muted);
    }

    .item-disc-input {
      width: 24px;
      border: none;
      background: transparent;
      font-size: 0.72rem;
      font-weight: 800;
      color: #059669;
      text-align: right;
      outline: none;
      padding: 0;
    }

    .item-disc-unit {
      font-size: 0.65rem;
      font-weight: 800;
      color: var(--rp-muted);
    }

    .cart-item-total {
      font-family: 'Outfit', sans-serif;
      min-width: 48px;
      text-align: right;
      flex-shrink: 0;
    }

    .cart-item-net-val {
      font-size: 0.84rem;
      font-weight: 800;
      color: var(--rp-dark);
      line-height: 1.1;
    }

    .item-disc-tag {
      font-size: 0.6rem;
      color: #059669;
      font-weight: 700;
      line-height: 1;
    }

    .btn-remove-item {
      background: transparent;
      border: none;
      color: #94a3b8;
      cursor: pointer;
      padding: 2px 3px;
      font-size: 0.78rem;
      transition: color 0.15s ease;
      flex-shrink: 0;
    }

    .btn-remove-item:hover { color: var(--rp-danger); }

    /* Summary Calculation Deck */
    .summary-deck {
      background: #f8fafc;
      padding: 8px 14px;
      border-bottom: 1.5px solid var(--rp-border);
      flex-shrink: 0;
    }

    .summary-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 0.78rem;
      font-weight: 600;
      color: var(--rp-muted);
      margin-bottom: 2px;
    }

    .summary-row.grand-total-row {
      margin-top: 5px;
      padding-top: 5px;
      border-top: 1.5px dashed #cbd5e1;
      font-size: 1.12rem;
      font-weight: 900;
      color: var(--rp-dark);
    }

    .grand-total-val {
      font-family: 'Outfit', sans-serif;
      color: var(--rp-primary);
      font-size: 1.32rem;
    }

    /* Split Payment Box (Cash & UPI) */
    .split-payment-box {
      padding: 8px 14px;
      background: #ffffff;
      border-bottom: 1.5px solid var(--rp-border);
      flex-shrink: 0;
    }

    .split-header {
      font-size: 0.76rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      color: var(--rp-dark);
      margin-bottom: 6px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      gap: 4px;
    }

    .split-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 6px;
      margin-bottom: 6px;
    }

    .payment-field-card {
      border: 1.5px solid var(--rp-border);
      border-radius: 8px;
      padding: 5px 8px;
      background: #f8fafc;
      min-width: 0;
      box-sizing: border-box;
      transition: all 0.2s ease;
    }

    .payment-field-card.active-cash {
      border-color: #10b981;
      background: #f0fdf4;
    }

    .payment-field-card.active-upi {
      border-color: #6366f1;
      background: #f5f3ff;
    }

    .p-label {
      font-size: 0.68rem;
      font-weight: 800;
      text-transform: uppercase;
      margin-bottom: 2px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .p-label.cash-label { color: #059669; }
    .p-label.upi-label { color: #4f46e5; }

    .p-input-wrap {
      position: relative;
      display: flex;
      align-items: center;
      width: 100%;
    }

    .p-input-wrap span {
      position: absolute;
      left: 6px;
      font-weight: 800;
      color: var(--rp-slate);
      font-size: 0.8rem;
    }

    .p-input {
      width: 100%;
      border: 1px solid #cbd5e1;
      border-radius: 5px;
      padding: 4px 6px 4px 18px;
      font-family: 'Outfit', monospace;
      font-size: 0.95rem;
      font-weight: 800;
      color: var(--rp-dark);
      background: #ffffff;
      outline: none;
      box-sizing: border-box;
    }

    .p-input:focus {
      border-color: var(--rp-primary);
    }

    /* Quick Split Action Chips */
    .quick-split-bar {
      display: flex;
      align-items: center;
      gap: 4px;
      flex-wrap: wrap;
    }

    .btn-split-chip {
      background: #f1f5f9;
      border: 1px solid var(--rp-border);
      border-radius: 12px;
      padding: 2px 7px;
      font-size: 0.68rem;
      font-weight: 700;
      color: var(--rp-slate);
      cursor: pointer;
      transition: all 0.15s ease;
    }

    .btn-split-chip:hover {
      background: #e2e8f0;
    }

    .btn-split-chip.chip-cash {
      color: #047857;
      background: #ecfdf5;
      border-color: #a7f3d0;
    }

    .btn-split-chip.chip-upi {
      color: #4338ca;
      background: #eef2ff;
      border-color: #c7d2fe;
    }

    /* Payment Balance Badge */
    .payment-status-badge {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 5px;
      padding: 4px 8px;
      border-radius: 6px;
      font-size: 0.74rem;
      font-weight: 700;
    }

    .payment-status-badge.exact {
      background: #ecfdf5;
      color: #065f46;
      border: 1px solid #a7f3d0;
    }

    .payment-status-badge.due {
      background: #fffbeb;
      color: #92400e;
      border: 1px solid #fde68a;
    }

    .payment-status-badge.excess {
      background: #eff6ff;
      color: #1e40af;
      border: 1px solid #bfdbfe;
    }

    /* Action Footer */
    .checkout-footer {
      padding: 10px 14px;
      background: #ffffff;
      flex-shrink: 0;
    }

    .print-toggle-wrap {
      display: flex;
      align-items: center;
      gap: 6px;
      margin-bottom: 8px;
      font-size: 0.8rem;
      font-weight: 700;
      color: var(--rp-dark);
      cursor: pointer;
    }

    .print-toggle-checkbox {
      width: 16px;
      height: 16px;
      accent-color: var(--rp-primary);
      cursor: pointer;
    }

    .btn-submit-rapid {
      width: 100%;
      background: var(--rp-primary-gradient);
      color: #ffffff;
      border: none;
      border-radius: 10px;
      padding: 11px;
      font-family: 'Outfit', sans-serif;
      font-size: 1.02rem;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      box-shadow: 0 4px 16px rgba(255, 94, 20, 0.3);
      cursor: pointer;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-submit-rapid:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 20px rgba(255, 94, 20, 0.4);
      color: #ffffff;
    }

    .btn-submit-rapid:disabled {
      background: #cbd5e1;
      box-shadow: none;
      cursor: not-allowed;
      transform: none;
    }

    /* Print Preview Modal */
    .modal-backdrop-custom {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(15, 23, 42, 0.7);
      backdrop-filter: blur(4px);
      z-index: 1050;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 16px;
    }

    .modal-box-custom {
      background: #ffffff;
      width: 100%;
      max-width: 460px;
      border-radius: var(--rp-radius-lg);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      overflow: hidden;
      animation: modalSlideIn 0.25s ease-out;
    }

    @keyframes modalSlideIn {
      from { transform: translateY(20px) scale(0.96); opacity: 0; }
      to { transform: translateY(0) scale(1); opacity: 1; }
    }

    .modal-header-custom {
      background: #0f172a;
      color: #ffffff;
      padding: 14px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .receipt-paper {
      background: #fdfdfd;
      border: 1px solid #e2e8f0;
      padding: 14px;
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.78rem;
      line-height: 1.4;
      color: #1e293b;
      max-height: 360px;
      overflow-y: auto;
    }

    .receipt-center { text-align: center; }
    .receipt-divider { border-top: 1px dashed #94a3b8; margin: 6px 0; }

    /* ===================================================
       RESPONSIVE BREAKPOINTS
       =================================================== */
    @media (max-width: 1300px) {
      .rapid-workspace {
        grid-template-columns: minmax(0, 1fr) 380px;
        gap: 12px;
      }
      .dishes-grid {
        grid-template-columns: repeat(auto-fill, minmax(145px, 1fr));
      }
    }

    @media (max-width: 1100px) {
      .rapid-workspace {
        grid-template-columns: minmax(0, 1fr) 350px;
        gap: 10px;
      }
      .dishes-grid {
        grid-template-columns: repeat(auto-fill, minmax(135px, 1fr));
      }
      .dish-title {
        font-size: 0.82rem;
      }
      .dish-price {
        font-size: 0.92rem;
      }
    }

    @media (max-width: 991.98px) {
      .pc-content {
        padding: 10px 8px 90px 8px !important;
      }
      .rapid-workspace {
        display: block !important;
      }
      .mobile-pos-switcher {
        display: flex !important;
      }
      .menu-panel {
        display: none;
        padding: 12px;
      }
      .menu-panel.mobile-active {
        display: block !important;
      }
      .bill-checkout-panel {
        display: none;
        max-height: none !important;
      }
      .bill-checkout-panel.mobile-active {
        display: flex !important;
        position: static !important;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
        border-radius: var(--rp-radius-md);
      }
      .cart-items-wrapper {
        max-height: 320px;
      }
      .dishes-grid {
        max-height: calc(100vh - 260px);
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
      }
    }

    @media (max-width: 768px) {
      .search-veg-row {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
      }
      .food-type-toggles {
        width: 100%;
        justify-content: space-between;
      }
      .type-toggle-btn {
        flex: 1;
        justify-content: center;
        padding: 7px 10px;
      }
    }

    @media (max-width: 576px) {
      .rapid-topbar {
        padding: 8px 12px;
        margin-bottom: 10px;
        border-radius: 10px;
      }
      .rapid-brand {
        gap: 8px;
      }
      .rapid-brand-icon {
        width: 34px;
        height: 34px;
        font-size: 1rem;
        border-radius: 8px;
      }
      .rapid-title {
        font-size: 1rem;
      }
      .rapid-badge-speed {
        font-size: 0.62rem;
        padding: 1px 5px;
      }
      .rapid-btn-secondary {
        padding: 4px 8px;
        font-size: 0.72rem;
      }
      .dishes-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 8px !important;
        max-height: calc(100vh - 280px);
      }
      .dish-card {
        padding: 8px !important;
      }
      .dish-title {
        font-size: 0.8rem;
        min-height: 2em;
        margin-bottom: 2px;
      }
      .dish-category-label {
        font-size: 0.65rem;
        margin-bottom: 3px;
      }
      .dish-price {
        font-size: 0.9rem;
      }
      .dish-add-btn {
        width: 24px;
        height: 24px;
        font-size: 0.72rem;
      }
      .customer-input-grid {
        grid-template-columns: 1fr;
        gap: 5px;
      }
      .split-row {
        grid-template-columns: 1fr;
        gap: 6px;
      }
      .quick-split-bar {
        gap: 3px;
      }
      .btn-split-chip {
        padding: 2px 6px;
        font-size: 0.66rem;
      }
      .cart-item-main-row {
        flex-wrap: wrap;
      }
      .cart-item-info {
        width: 100%;
        margin-bottom: 3px;
      }
      .cart-item-actions-wrap {
        width: 100%;
        justify-content: space-between;
      }
      .cart-dish-addons-panel {
        margin-left: 2px;
      }
      .modal-box-custom {
        max-width: 95% !important;
        margin: 10px auto;
        border-radius: 12px;
      }
    }
  </style>
</head>
<body>

@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">

    <!-- Top Header Bar -->
    <div class="rapid-topbar">
      <div class="rapid-brand">
        <button type="button" class="btn-rapid-hamburger" id="rapidSidebarToggle" title="Toggle Navigation Menu">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div class="rapid-brand-icon">
          <i class="fa-solid fa-bolt"></i>
        </div>
        <div>
          <h1 class="rapid-title">
            Rapid Bill Terminal
            <span class="rapid-badge-speed">⚡ Fast POS</span>
          </h1>
          <div style="font-size: 0.78rem; color: #94a3b8;">
            {{ $restaurant->name ?? 'Restaurant' }} @if(!empty($restaurant->gstin)) • GSTIN: {{ $restaurant->gstin }} @endif
          </div>
        </div>
      </div>

      <div class="rapid-top-actions">
        <span class="text-white small fw-bold d-none d-md-inline" id="liveClock">
          <i class="fa-regular fa-clock me-1"></i> --:--:--
        </span>
        <a href="{{ route('order.management.dashboard') }}" class="rapid-btn-secondary">
          <i class="fa-solid fa-table-cells"></i> Floor Orders
        </a>
      </div>
    </div>

    <!-- Mobile View Segmented Switcher (Visible on <= 991px) -->
    <div class="mobile-pos-switcher" id="mobilePosSwitcher">
      <button type="button" class="mobile-pos-tab active" data-tab="menu" id="tabMobileMenu">
        <i class="fa-solid fa-utensils"></i> Menu Items
      </button>
      <button type="button" class="mobile-pos-tab tab-bill" data-tab="bill" id="tabMobileBill">
        <i class="fa-solid fa-receipt"></i> Current Bill
        <span class="badge bg-white text-dark ms-1" id="mobileBillBadge" style="display: none;">0</span>
      </button>
    </div>

    <!-- Main Workspace Grid -->
    <div class="rapid-workspace">

      <!-- LEFT: Fast Menu & Dish Explorer -->
      <div class="menu-panel mobile-active" id="menuPanelSection">
        
        <div class="menu-filters-bar">
          <!-- Search & Veg/Non-veg Row -->
          <div class="search-veg-row">
            <div class="search-input-wrap">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input type="text" id="menuSearchInput" class="search-input" placeholder="Search dish name, code or keyword... (Press / to focus)" autocomplete="off">
              <button type="button" id="clearSearchBtn" class="search-clear-btn">
                <i class="fa-solid fa-circle-xmark"></i>
              </button>
            </div>

            <div class="food-type-toggles">
              <button type="button" class="type-toggle-btn active" data-type="all">All</button>
              <button type="button" class="type-toggle-btn" data-type="veg">
                <span class="food-symbol veg"></span> Veg
              </button>
              <button type="button" class="type-toggle-btn" data-type="nonveg">
                <span class="food-symbol nonveg"></span> Non-Veg
              </button>
            </div>
          </div>

          <!-- Category Scroll Pills -->
          <div class="category-scroll-tabs" id="categoryScrollTabs">
            <button type="button" class="cat-tab-btn active" data-category="all">
              <i class="fa-solid fa-border-all"></i> All Items
              <span class="cat-tab-badge">{{ $categories->sum(function($c) { return $c->subcategories->count(); }) }}</span>
            </button>
            @foreach($categories as $cat)
              <button type="button" class="cat-tab-btn" data-category="{{ $cat->id }}">
                {{ $cat->name }}
                <span class="cat-tab-badge">{{ $cat->subcategories->count() }}</span>
              </button>
            @endforeach
            @if(isset($restaurant_addons) && $restaurant_addons->count() > 0)
              <button type="button" class="cat-tab-btn cat-tab-addons" data-category="addons" style="background: #fff7ed; border-color: #fed7aa; color: #c2410c; font-weight: 800;">
                <i class="fa-solid fa-puzzle-piece text-warning"></i> Add-ons
                <span class="cat-tab-badge" style="background: #ea580c; color: #fff;">{{ $restaurant_addons->count() }}</span>
              </button>
            @endif
          </div>
        </div>

        <!-- Dishes Grid -->
        <div class="dishes-grid" id="dishesGridContainer">
          @forelse($categories as $category)
            @foreach($category->subcategories as $dish)
              @php
                $dishPrice = floatval($dish->price ?? 0);
                $dishFoodType = strtolower($dish->food_type ?? 'veg');
                $dishDiscount = floatval($dish->discount ?? 0);
                $dishAddons = $dish->addons ?? collect([]);
              @endphp
              <div class="dish-card" 
                   data-id="{{ $dish->id }}"
                   data-name="{{ $dish->name }}"
                   data-price="{{ $dishPrice }}"
                   data-category-id="{{ $category->id }}"
                   data-food-type="{{ $dishFoodType }}"
                   data-discount="{{ $dishDiscount }}"
                   data-addons='@json($dishAddons)'
                   data-search-terms="{{ strtolower($dish->name . ' ' . $category->name . ' ' . ($dish->code ?? '')) }}">
                
                <div>
                  <div class="dish-top-meta">
                    <span class="food-symbol {{ $dishFoodType === 'non-veg' || $dishFoodType === 'nonveg' ? 'nonveg' : 'veg' }}"></span>
                    <div class="d-flex align-items-center gap-1">
                      @if($dishAddons->count() > 0)
                      <span class="dish-addon-pill" title="{{ $dishAddons->count() }} Addon(s) Available">
                        <i class="fa-solid fa-puzzle-piece"></i> {{ $dishAddons->count() }} Addon{{ $dishAddons->count() > 1 ? 's' : '' }}
                      </span>
                      @endif
                      <span class="dish-cart-badge" id="dishBadge-{{ $dish->id }}">0</span>
                    </div>
                  </div>

                  <h3 class="dish-title" title="{{ $dish->name }}">{{ $dish->name }}</h3>
                </div>

                <div class="dish-bottom-bar">
                  <div class="dish-price">₹{{ number_format($dishPrice, 2) }}</div>
                  <div class="dish-add-btn">
                    <i class="fa-solid fa-plus"></i>
                  </div>
                </div>
              </div>
            @endforeach
          @empty
            <div class="col-12 text-center py-5 text-muted">
              <i class="fa-solid fa-utensils fa-3x mb-3 text-secondary opacity-50"></i>
              <p class="fw-bold">No active dishes found in the menu.</p>
            </div>
          @endforelse

          @if(isset($restaurant_addons) && $restaurant_addons->count() > 0)
            @foreach($restaurant_addons as $addon)
              @php
                $addonPrice = floatval($addon->price ?? 0);
                $addonFoodType = strtolower($addon->food_type ?? 'veg');
                $isNonVeg = ($addonFoodType === 'non-veg' || $addonFoodType === 'nonveg');
              @endphp
              <div class="dish-card addon-menu-card"
                   data-id="addon-{{ $addon->id }}"
                   data-addon-id="{{ $addon->id }}"
                   data-name="{{ $addon->name }}"
                   data-price="{{ $addonPrice }}"
                   data-category-id="addons"
                   data-food-type="{{ $addonFoodType }}"
                   data-is-addon="1"
                   data-search-terms="{{ strtolower($addon->name . ' addon add-on extra topping portion') }}"
                   style="border-color: #fed7aa; background: linear-gradient(180deg, #ffffff 0%, #fffbf8 100%);">
                <div>
                  <div class="dish-top-meta">
                    <span class="food-symbol {{ $isNonVeg ? 'nonveg' : 'veg' }}"></span>
                    <span class="badge bg-warning-subtle text-warning border px-2 py-1" style="font-size: 0.65rem; font-weight: 800; border-radius: 6px;">
                      <i class="fa-solid fa-puzzle-piece"></i> ADD-ON
                    </span>
                  </div>
                  <h3 class="dish-title" title="{{ $addon->name }}">{{ $addon->name }}</h3>
                </div>

                <div class="dish-bottom-bar">
                  <div class="dish-price">₹{{ number_format($addonPrice, 2) }}</div>
                  <div class="dish-add-btn" style="background: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%); color: #fff;">
                    <i class="fa-solid fa-plus"></i>
                  </div>
                </div>
              </div>
            @endforeach
          @endif
        </div>

      </div>

      <!-- RIGHT: Rapid Checkout & Split Payment Pane -->
      <div class="bill-checkout-panel" id="billCheckoutSection">
        
        <div class="checkout-header">
          <button type="button" class="btn btn-sm btn-outline-primary fw-bold d-lg-none me-2" id="btnBackToMenuMobile" style="border-radius: 20px; font-size: 0.78rem; padding: 4px 10px;">
            <i class="fa-solid fa-arrow-left me-1"></i> Menu
          </button>
          <h2 class="checkout-title me-auto">
            <i class="fa-solid fa-receipt text-warning"></i> Current Bill
          </h2>
          <button type="button" class="btn-clear-all" id="clearCartBtn">
            <i class="fa-solid fa-trash-can"></i> Clear
          </button>
        </div>

        <!-- Customer & Order Type Box (Optional Info) -->
        <div class="customer-meta-box">
          <div class="customer-input-grid">
            <input type="text" id="custName" class="c-input" placeholder="Customer Name (Optional)">
            <input type="text" id="custPhone" class="c-input" placeholder="Phone No. (Optional)" maxlength="15">
          </div>

          <div class="dining-type-selector">
            <button type="button" class="dining-btn active" data-type="takeaway" id="btnTakeaway">
              <i class="fa-solid fa-bag-shopping"></i> Takeaway / Express
            </button>
            <button type="button" class="dining-btn" data-type="dine_in" id="btnDineIn">
              <i class="fa-solid fa-chair"></i> Dine-In Table
            </button>
          </div>

          <div class="table-select-wrap" id="tableSelectWrap">
            <select class="c-input" id="tableIdSelect" style="margin-top: 6px;">
              <option value="">-- Select Dining Table (Optional) --</option>
              @foreach($tables as $t)
                <option value="{{ $t->id }}">Table {{ $t->name }} ({{ $t->seating_capacity ?? '4' }} seats)</option>
              @endforeach
            </select>
          </div>
        </div>

        <!-- Cart Items List -->
        <div class="cart-items-wrapper" id="cartItemsList">
          <div class="cart-empty-state" id="cartEmptyMessage">
            <div class="cart-empty-icon"><i class="fa-solid fa-cart-shopping"></i></div>
            <div class="fw-bold">Bill is empty</div>
            <div class="small">Click any dish on the left to add</div>
          </div>
        </div>

        <!-- Summary & Bill Calculation -->
        <div class="summary-deck">
          <div class="summary-row">
            <span>Items Subtotal:</span>
            <span class="fw-bold" id="lblSubtotal">₹0.00</span>
          </div>

          <div class="summary-row" id="rowItemDiscounts" style="display: none; color: #059669;">
            <span>Total Item Discount:</span>
            <span class="fw-bold" id="lblItemDiscounts">-₹0.00</span>
          </div>

          @if($isGstEnabled)
            <div class="summary-row">
              <span>Taxable Subtotal:</span>
              <span id="lblTaxable">₹0.00</span>
            </div>
            <div class="summary-row">
              <span>GST ({{ $gstPercentage }}%):</span>
              <span id="lblGst">₹0.00</span>
            </div>
          @endif

          <div class="summary-row">
            <span>Round Off:</span>
            <span id="lblRoundOff">₹0.00</span>
          </div>

          <div class="summary-row grand-total-row">
            <span>Grand Total:</span>
            <span class="grand-total-val" id="lblGrandTotal">₹0.00</span>
          </div>
        </div>

        <!-- Split Payment (Cash & UPI) -->
        <div class="split-payment-box">
          <div class="split-header">
            <span>Payment Mode</span>
            <div class="quick-split-bar">
              <button type="button" class="btn-split-chip chip-cash" id="btnFullCash">Full Cash</button>
              <button type="button" class="btn-split-chip chip-upi" id="btnFullUpi">Full UPI</button>
              <button type="button" class="btn-split-chip" id="btnSplit5050">50 / 50</button>
            </div>
          </div>

          <div class="split-row">
            <!-- Cash Input Card -->
            <div class="payment-field-card active-cash" id="cashCard">
              <div class="p-label cash-label">
                <span><i class="fa-solid fa-money-bill-wave"></i> Cash</span>
              </div>
              <div class="p-input-wrap">
                <span>₹</span>
                <input type="number" id="inputCashAmount" class="p-input" min="0" step="1" value="0">
              </div>
            </div>

            <!-- UPI Input Card -->
            <div class="payment-field-card active-upi" id="upiCard">
              <div class="p-label upi-label">
                <span><i class="fa-solid fa-qrcode"></i> UPI / QR</span>
              </div>
              <div class="p-input-wrap">
                <span>₹</span>
                <input type="number" id="inputUpiAmount" class="p-input" min="0" step="1" value="0">
              </div>
            </div>
          </div>

          <!-- Payment Status / Balance Indicator -->
          <div class="payment-status-badge exact" id="paymentStatusBadge">
            <span id="paymentStatusText"><i class="fa-solid fa-check-circle"></i> Payment Balanced</span>
            <span id="paymentDiffText">₹0.00</span>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="checkout-footer">
          <label class="print-toggle-wrap">
            <input type="checkbox" id="chkPrintBill" class="print-toggle-checkbox" checked>
            <span><i class="fa-solid fa-print me-1 text-primary"></i> Print Bill & Receipt</span>
          </label>

          <button type="button" class="btn-submit-rapid" id="btnSubmitBill">
            <i class="fa-solid fa-bolt"></i> Place & Generate Bill
          </button>
        </div>

      </div>

    </div>

  </div>
</div>

<!-- ===================================================
     MODAL: DISH ADDONS CUSTOMIZATION
     =================================================== -->
<div class="modal-backdrop-custom" id="rapidAddonModal">
  <div class="modal-box-custom" style="max-width: 480px;">
    <div class="modal-header-custom" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
      <div class="d-flex align-items-center gap-2">
        <span class="food-symbol veg" id="addonModalFoodSymbol"></span>
        <div>
          <h5 class="m-0 text-white fw-bold" id="addonModalDishTitle" style="font-size: 1.05rem;">Customize Add-ons</h5>
          <small class="text-white-50" id="addonModalBasePrice" style="font-size: 0.78rem;">Base Price: ₹0.00</small>
        </div>
      </div>
      <button type="button" class="btn-close btn-close-white" id="btnCloseAddonModal"></button>
    </div>
    
    <div class="p-3" style="background: #ffffff;">
      <!-- Search filter for addons inside modal -->
      <div class="input-group input-group-sm mb-2" id="addonModalSearchWrap">
        <span class="input-group-text bg-light border-end-0" style="border-radius: 8px 0 0 8px;">
          <i class="fa-solid fa-magnifying-glass text-muted" style="font-size: 0.8rem;"></i>
        </span>
        <input type="text" id="addonModalSearchInput" class="form-control form-control-sm border-start-0" placeholder="Search toppings, cheese, dips..." style="border-radius: 0 8px 8px 0; font-size: 0.82rem;">
      </div>

      <!-- Quick select all / clear -->
      <div class="d-flex justify-content-between align-items-center mb-2 px-1">
        <span class="text-muted" style="font-size: 0.75rem;">Select toppings or sides for this dish:</span>
        <div>
          <a href="javascript:void(0)" class="text-primary small fw-bold me-2" id="btnSelectAllAddons" style="font-size: 0.75rem; text-decoration: none;">Select All</a>
          <a href="javascript:void(0)" class="text-muted small fw-bold" id="btnClearAllAddons" style="font-size: 0.75rem; text-decoration: none;">Clear</a>
        </div>
      </div>

      <!-- Scrollable list of addons -->
      <div class="addon-selection-list" id="addonModalItemsList" style="max-height: 250px; overflow-y: auto;">
        <!-- Rendered dynamically -->
      </div>
      
      <!-- Summary Bar in Modal -->
      <div class="mt-3 p-2 rounded d-flex align-items-center justify-content-between" style="background: #f8fafc; border: 1.5px solid #e2e8f0;">
        <div>
          <div class="small text-muted" style="font-size: 0.72rem;">Selected Add-ons</div>
          <div class="fw-bold text-dark" id="addonModalSelectedSummary" style="font-size: 0.86rem;">0 Add-ons (+₹0.00)</div>
        </div>
        <div class="text-end">
          <div class="small text-muted" style="font-size: 0.72rem;">New Unit Price</div>
          <div class="fw-bold text-primary" id="addonModalNewUnitPrice" style="font-size: 1.05rem; font-family: 'Outfit', sans-serif;">₹0.00</div>
        </div>
      </div>

      <div class="d-flex gap-2 mt-3">
        <button type="button" class="btn btn-light flex-grow-1 fw-bold border" id="btnCancelAddonModal" style="border-radius: 8px;">
          Cancel
        </button>
        <button type="button" class="btn btn-primary flex-grow-1 fw-bold" id="btnApplyAddons" style="background: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%); border: none; border-radius: 8px; box-shadow: 0 4px 12px rgba(255, 94, 20, 0.3);">
          <i class="fa-solid fa-check me-1"></i> Apply Add-ons
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Print Preview & Receipt Modal -->
<div class="modal-backdrop-custom" id="rapidReceiptModal">
  <div class="modal-box-custom">
    <div class="modal-header-custom">
      <div class="d-flex align-items-center gap-2">
        <i class="fa-solid fa-receipt text-warning"></i>
        <h5 class="m-0 text-white fw-bold">Bill Generated Successfully</h5>
      </div>
      <button type="button" class="btn-close btn-close-white" id="btnCloseModal"></button>
    </div>
    
    <div class="p-3">
      <div class="receipt-paper" id="receiptPaperBody">
        <!-- Rendered dynamically -->
      </div>
      
      <div class="d-flex gap-2 mt-3">
        <button type="button" class="btn btn-dark flex-grow-1 fw-bold" id="btnDirectPrint">
          <i class="fa-solid fa-print me-1"></i> Print Bill
        </button>
        <button type="button" class="btn btn-outline-secondary flex-grow-1 fw-bold" id="btnNextBill">
          <i class="fa-solid fa-plus me-1"></i> Next Bill
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Mobile Floating Cart Bar (Appears when cart has items on mobile) -->
<div class="mobile-floating-cart-bar" id="mobileFloatingCartBar">
  <div class="d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center gap-2">
      <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 0.95rem;">
        <i class="fa-solid fa-cart-shopping"></i>
      </div>
      <div>
        <div class="fw-bold text-white" style="font-size: 0.95rem;" id="floatCartItemCount">0 Items</div>
        <div class="text-warning fw-bold" style="font-size: 0.85rem;" id="floatCartTotal">₹0.00</div>
      </div>
    </div>
    <button type="button" class="btn btn-sm btn-light fw-bold px-3 py-2 text-dark shadow-sm" id="btnFloatViewBill" style="border-radius: 20px; font-size: 0.84rem;">
      View Bill & Pay <i class="fa-solid fa-arrow-right ms-1 text-primary"></i>
    </button>
  </div>
<!-- Map Addon To Dish Modal -->
<div class="modal-backdrop-custom" id="rapidMapAddonModal" style="display: none; z-index: 100000;">
  <div class="modal-box-custom" style="max-width: 440px; border-radius: 18px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.3);">
    <div class="modal-header-custom" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 16px 20px;">
      <div class="d-flex align-items-center gap-2">
        <span class="food-symbol veg" id="mapAddonFoodSymbol"></span>
        <div>
          <h5 class="m-0 text-white fw-bold" id="mapAddonTitle" style="font-size: 1.02rem;">Add Add-on</h5>
          <small class="text-warning fw-bold" id="mapAddonPrice" style="font-size: 0.8rem;">+₹0.00 / portion</small>
        </div>
      </div>
      <button type="button" class="btn-close btn-close-white" id="btnCloseMapAddonModal"></button>
    </div>

    <div class="p-3" style="background: #ffffff;">
      <div class="mb-3 text-center">
        <span class="badge bg-light text-dark border px-3 py-1 fw-semibold" style="font-size: 0.8rem;">
          <i class="fa-solid fa-layer-group text-primary me-1"></i> Choose which dish in bill to attach this add-on to:
        </span>
      </div>

      <!-- Dishes in Cart Options -->
      <div class="d-flex flex-column gap-2 mb-3" id="mapAddonDishesList" style="max-height: 240px; overflow-y: auto;">
        <!-- Injected dynamically based on cart -->
      </div>

      <div class="pt-2 border-top">
        <button type="button" class="btn btn-light w-100 fw-bold border text-muted" id="btnCancelMapAddon">
          Cancel
        </button>
      </div>
    </div>
  </div>
</div>

@include('includes.footer')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
window.POS_RESTAURANT_ADDONS = @json($restaurant_addons ?? []);
$(document).ready(function() {
  // Global POS State
  const RESTAURANT_GST_PERCENT = parseFloat("{{ $gstPercentage ?? 0 }}");
  const IS_GST_ENABLED = {{ $isGstEnabled ? 'true' : 'false' }};
  
  let cart = {}; // { dishId: { id, name, price, qty, discount_percent } }
  let orderType = 'takeaway';
  let activeCategoryId = 'all';
  let activeFoodType = 'all';
  let lastOrderInvoiceUrl = null;
  let currentMobileTab = 'menu';

  // Mobile POS Tab Switcher
  function switchMobileTab(tab) {
    currentMobileTab = tab;
    $('.mobile-pos-tab').removeClass('active');
    $(`.mobile-pos-tab[data-tab="${tab}"]`).addClass('active');

    if (tab === 'menu') {
      $('#menuPanelSection').addClass('mobile-active');
      $('#billCheckoutSection').removeClass('mobile-active');
      let itemCount = Object.values(cart).reduce(function(sum, it) { return sum + it.qty; }, 0);
      if (itemCount > 0 && window.innerWidth <= 991.98) {
        $('#mobileFloatingCartBar').fadeIn(150);
      } else {
        $('#mobileFloatingCartBar').hide();
      }
    } else {
      $('#billCheckoutSection').addClass('mobile-active');
      $('#menuPanelSection').removeClass('mobile-active');
      $('#mobileFloatingCartBar').hide();
      if ($('#billCheckoutSection').length && window.innerWidth <= 991.98) {
        $('html, body').animate({ scrollTop: $('#billCheckoutSection').offset().top - 20 }, 200);
      }
    }
  }

  $('.mobile-pos-tab').on('click', function() {
    let tab = $(this).data('tab');
    switchMobileTab(tab);
  });

  $('#btnFloatViewBill').on('click', function() {
    switchMobileTab('bill');
  });

  $('#btnBackToMenuMobile').on('click', function() {
    switchMobileTab('menu');
  });

  $(window).on('resize', function() {
    if (window.innerWidth > 991.98) {
      $('#mobileFloatingCartBar').hide();
    } else {
      switchMobileTab(currentMobileTab);
    }
  });

  // Live Clock
  function updateLiveClock() {
    const now = new Date();
    $('#liveClock').html('<i class="fa-regular fa-clock me-1"></i> ' + now.toLocaleTimeString());
  }
  setInterval(updateLiveClock, 1000);
  updateLiveClock();

  // Search & Filter Events
  $('#menuSearchInput').on('input', function() {
    let val = $(this).val().trim();
    if (val.length > 0) {
      $('#clearSearchBtn').show();
    } else {
      $('#clearSearchBtn').hide();
    }
    filterMenuDishes();
  });

  $('#clearSearchBtn').on('click', function() {
    $('#menuSearchInput').val('').focus();
    $(this).hide();
    filterMenuDishes();
  });

  $('.cat-tab-btn').on('click', function() {
    $('.cat-tab-btn').removeClass('active');
    $(this).addClass('active');
    activeCategoryId = $(this).data('category');
    filterMenuDishes();
  });

  $('.type-toggle-btn').on('click', function() {
    $('.type-toggle-btn').removeClass('active veg nonveg');
    let type = $(this).data('type');
    activeFoodType = type;
    $(this).addClass('active');
    if (type === 'veg') $(this).addClass('veg');
    if (type === 'nonveg') $(this).addClass('nonveg');
    filterMenuDishes();
  });

  function filterMenuDishes() {
    let search = $('#menuSearchInput').val().toLowerCase().trim();

    $('.dish-card').each(function() {
      let card = $(this);
      let catId = card.data('category-id');
      let foodType = (card.data('food-type') || 'veg').toString().toLowerCase();
      let searchTerms = (card.data('search-terms') || '').toString();

      let matchesCategory = (activeCategoryId === 'all' || activeCategoryId == catId);
      let matchesFood = (activeFoodType === 'all' || 
                        (activeFoodType === 'veg' && (foodType === 'veg' || foodType === 'vegetarian')) ||
                        (activeFoodType === 'nonveg' && (foodType === 'non-veg' || foodType === 'nonveg')));
      let matchesSearch = (!search || searchTerms.includes(search));

      if (matchesCategory && matchesFood && matchesSearch) {
        card.show();
      } else {
        card.hide();
      }
    });
  }

  // Dining Type Switcher
  $('.dining-btn').on('click', function() {
    $('.dining-btn').removeClass('active');
    $(this).addClass('active');
    orderType = $(this).data('type');
    if (orderType === 'dine_in') {
      $('#tableSelectWrap').slideDown(150);
    } else {
      $('#tableSelectWrap').slideUp(150);
      $('#tableIdSelect').val('');
    }
  });

  let pendingMappingAddon = null;

  // Add Dish or Map Add-on to Cart
  $(document).on('click', '.dish-card', function(e) {
    let isAddon = $(this).data('is-addon');

    if (isAddon) {
      let addonId = $(this).data('addon-id') || String($(this).data('id')).replace('addon-', '');
      let addonName = $(this).data('name');
      let addonPrice = parseFloat($(this).data('price')) || 0;
      let foodType = $(this).data('food-type') || 'veg';
      let cartKey = 'addon_' + addonId;

      if (cart[cartKey]) {
        cart[cartKey].qty += 1;
      } else {
        cart[cartKey] = {
          id: cartKey,
          name: addonName,
          base_price: addonPrice,
          price: addonPrice,
          qty: 1,
          discount_percent: 0,
          food_type: foodType,
          is_addon: true,
          addon_id: addonId,
          available_addons: [],
          addons: [{
            id: addonId,
            name: addonName,
            price: addonPrice,
            qty: 1,
            quantity: 1,
            food_type: foodType
          }]
        };
      }
      renderCart();
      return;
    }

    let dishId = $(this).data('id');
    let dishName = $(this).data('name');
    let dishPrice = parseFloat($(this).data('price')) || 0;
    let dishDiscount = parseFloat($(this).data('discount')) || 0;
    let foodType = $(this).data('food-type') || 'veg';
    
    let rawAddons = $(this).attr('data-addons') || $(this).data('addons');
    let availableAddons = [];
    if (typeof rawAddons === 'string') {
      try { availableAddons = JSON.parse(rawAddons); } catch (err) { availableAddons = []; }
    } else if (Array.isArray(rawAddons)) {
      availableAddons = rawAddons;
    }

    if ((!availableAddons || availableAddons.length === 0) && window.POS_RESTAURANT_ADDONS && window.POS_RESTAURANT_ADDONS.length > 0) {
      availableAddons = window.POS_RESTAURANT_ADDONS;
    }

    if (cart[dishId]) {
      cart[dishId].qty += 1;
    } else {
      cart[dishId] = {
        id: dishId,
        name: dishName,
        base_price: dishPrice,
        price: dishPrice,
        qty: 1,
        discount_percent: dishDiscount,
        food_type: foodType,
        available_addons: availableAddons,
        addons: []
      };
    }
    renderCart();
  });

  function attachAddonToDish(dishId, addonData) {
    if (!cart[dishId]) return;
    if (!cart[dishId].addons) cart[dishId].addons = [];

    let existing = cart[dishId].addons.find(a => String(a.id) === String(addonData.id));
    if (existing) {
      existing.qty = (existing.qty || existing.quantity || 1) + 1;
      existing.quantity = existing.qty;
    } else {
      cart[dishId].addons.push({
        id: addonData.id,
        name: addonData.name,
        price: addonData.price,
        qty: 1,
        quantity: 1,
        food_type: addonData.food_type || 'VEG'
      });
    }

    let totalAddonsCost = cart[dishId].addons.reduce((sum, a) => sum + ((parseFloat(a.price) || 0) * (a.qty || 1)), 0);
    let base = cart[dishId].base_price !== undefined ? cart[dishId].base_price : cart[dishId].price;
    cart[dishId].price = base + totalAddonsCost;

    $('#rapidMapAddonModal').fadeOut(150);
    renderCart();
  }

  $(document).on('click', '.btn-attach-addon-target', function(e) {
    e.preventDefault();
    let dishId = $(this).data('dish-id');
    if (pendingMappingAddon && dishId) {
      attachAddonToDish(dishId, pendingMappingAddon);
    }
  });

  $('#btnCloseMapAddonModal, #btnCancelMapAddon').on('click', function() {
    $('#rapidMapAddonModal').fadeOut(150);
    pendingMappingAddon = null;
  });

  // Quantity and Remove Handlers in Cart
  $(document).on('click', '.btn-inc-qty', function(e) {
    e.stopPropagation();
    let id = $(this).data('id');
    if (cart[id]) {
      cart[id].qty += 1;
      renderCart();
    }
  });

  $(document).on('click', '.btn-dec-qty', function(e) {
    e.stopPropagation();
    let id = $(this).data('id');
    if (cart[id]) {
      cart[id].qty -= 1;
      if (cart[id].qty <= 0) {
        delete cart[id];
      }
      renderCart();
    }
  });

  $(document).on('click', '.btn-remove-item', function(e) {
    e.stopPropagation();
    let id = $(this).data('id');
    delete cart[id];
    renderCart();
  });

  $('#clearCartBtn').on('click', function() {
    cart = {};
    renderCart();
  });

  // Item-wise discount change
  $(document).on('input', '.item-disc-input', function() {
    let id = $(this).data('id');
    let val = parseFloat($(this).val()) || 0;
    val = Math.min(100, Math.max(0, val));
    if (cart[id]) {
      cart[id].discount_percent = val;
      recalculateTotals();

      // Update current line net total immediately
      let item = cart[id];
      let origTotal = item.price * item.qty;
      let discAmt = (origTotal * (item.discount_percent || 0)) / 100;
      let netTotal = origTotal - discAmt;
      let row = $(this).closest('.cart-item-card');
      row.find('.cart-item-net-val').text('₹' + netTotal.toFixed(2));
      if (discAmt > 0) {
        row.find('.item-disc-tag').text(`-₹${discAmt.toFixed(2)}`).show();
      } else {
        row.find('.item-disc-tag').hide();
      }
    }
  });

  // Payment amount change
  $('#inputCashAmount, #inputUpiAmount').on('input', function() {
    updatePaymentIndicator();
  });

  // Quick Payment Chips
  $('#btnFullCash').on('click', function() {
    let grandTotal = getComputedGrandTotal();
    $('#inputCashAmount').val(grandTotal);
    $('#inputUpiAmount').val(0);
    updatePaymentIndicator();
  });

  $('#btnFullUpi').on('click', function() {
    let grandTotal = getComputedGrandTotal();
    $('#inputCashAmount').val(0);
    $('#inputUpiAmount').val(grandTotal);
    updatePaymentIndicator();
  });

  $('#btnSplit5050').on('click', function() {
    let grandTotal = getComputedGrandTotal();
    let half = Math.round(grandTotal / 2);
    $('#inputCashAmount').val(half);
    $('#inputUpiAmount').val(grandTotal - half);
    updatePaymentIndicator();
  });

  // ===================================================
  // DISH ADDON CUSTOMIZATION MODAL LOGIC WITH QUANTITY
  // ===================================================
  let currentEditingDishId = null;

  function openAddonModal(dishId) {
    currentEditingDishId = dishId;
    let item = cart[dishId];
    if (!item) return;

    let availableAddons = item.available_addons || [];
    if (availableAddons.length === 0 && window.POS_RESTAURANT_ADDONS && window.POS_RESTAURANT_ADDONS.length > 0) {
      availableAddons = window.POS_RESTAURANT_ADDONS;
    }
    if (availableAddons.length === 0) return;

    let selectedAddonMap = {};
    (item.addons || []).forEach(function(a) {
      selectedAddonMap[String(a.id)] = a.qty || a.quantity || 1;
    });

    $('#addonModalDishTitle').text(item.name);
    $('#addonModalBasePrice').text('Base Price: ₹' + (item.base_price || item.price).toFixed(2));
    
    let foodSymbol = $('#addonModalFoodSymbol');
    foodSymbol.removeClass('veg nonveg');
    let foodType = item.food_type || 'veg';
    foodSymbol.addClass(foodType === 'non-veg' || foodType === 'nonveg' ? 'nonveg' : 'veg');

    let listHtml = '';
    availableAddons.forEach(function(addon) {
      let isChecked = selectedAddonMap.hasOwnProperty(String(addon.id));
      let initialQty = isChecked ? (selectedAddonMap[String(addon.id)] || 1) : 1;
      let isNonVeg = (addon.food_type === 'NON-VEG' || addon.food_type === 'non-veg');

      listHtml += `
        <div class="addon-selection-row ${isChecked ? 'is-selected' : ''}" 
             data-id="${addon.id}"
             data-name="${(addon.name || '').toLowerCase()}"
             data-price="${addon.price}">
          <div class="d-flex align-items-center gap-2">
            <input type="checkbox" class="form-check-input addon-modal-checkbox mt-0" 
                   value="${addon.id}" 
                   data-id="${addon.id}"
                   data-name="${addon.name}"
                   data-price="${addon.price}"
                   data-food-type="${addon.food_type || 'VEG'}"
                   ${isChecked ? 'checked' : ''}
                   style="width: 18px; height: 18px; cursor: pointer;">
            
            <span class="badge ${isNonVeg ? 'bg-danger' : 'bg-success'} text-white" style="font-size: 0.65rem; padding: 2px 5px;">
              ${isNonVeg ? '🔴 Non-Veg' : '🟢 Veg'}
            </span>
            
            <span class="fw-bold text-dark" style="font-size: 0.86rem;">${addon.name}</span>
          </div>

          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark font-monospace fw-bold border" style="font-size: 0.82rem;">
              +₹${parseFloat(addon.price).toFixed(2)}
            </span>
            <div class="addon-modal-qty-control ${isChecked ? '' : 'd-none'}" data-id="${addon.id}">
              <button type="button" class="btn-addon-modal-qty btn-addon-modal-dec" data-id="${addon.id}" title="Decrease quantity">-</button>
              <span class="addon-modal-qty-val" data-id="${addon.id}">${initialQty}</span>
              <button type="button" class="btn-addon-modal-qty btn-addon-modal-inc" data-id="${addon.id}" title="Increase quantity">+</button>
            </div>
          </div>
        </div>
      `;
    });

    $('#addonModalItemsList').html(listHtml);
    $('#addonModalSearchInput').val('');
    updateAddonModalSummary();
    $('#rapidAddonModal').css('display', 'flex').fadeIn(150);
  }

  function updateAddonModalSummary() {
    if (!currentEditingDishId || !cart[currentEditingDishId]) return;
    let item = cart[currentEditingDishId];

    let selectedAddons = [];
    let totalAddonsCost = 0;
    let totalAddonQty = 0;

    $('#addonModalItemsList .addon-selection-row').each(function() {
      let chk = $(this).find('.addon-modal-checkbox');
      if (chk.is(':checked')) {
        let price = parseFloat(chk.data('price')) || 0;
        let name = chk.data('name');
        let id = chk.data('id');
        let foodType = chk.data('food-type');
        let qty = parseInt($(this).find('.addon-modal-qty-val').text()) || 1;

        let lineCost = price * qty;
        totalAddonsCost += lineCost;
        totalAddonQty += qty;

        selectedAddons.push({
          id: id,
          name: name,
          price: price,
          qty: qty,
          quantity: qty,
          food_type: foodType
        });
      }
    });

    let countTypes = selectedAddons.length;
    $('#addonModalSelectedSummary').text(countTypes + ' Add-on' + (countTypes === 1 ? '' : 's') + ' (' + totalAddonQty + ' item' + (totalAddonQty === 1 ? '' : 's') + ') (+₹' + totalAddonsCost.toFixed(2) + ')');
    
    let base = item.base_price !== undefined ? item.base_price : item.price;
    let newUnitPrice = base + totalAddonsCost;
    $('#addonModalNewUnitPrice').text('₹' + newUnitPrice.toFixed(2));
  }

  // Live Checkbox toggle inside Modal
  $(document).on('change', '.addon-modal-checkbox', function() {
    let row = $(this).closest('.addon-selection-row');
    let qtyCtrl = row.find('.addon-modal-qty-control');
    let qtyVal = row.find('.addon-modal-qty-val');

    if ($(this).is(':checked')) {
      row.addClass('is-selected');
      let currentVal = parseInt(qtyVal.text()) || 0;
      if (currentVal <= 0) {
        qtyVal.text(1);
      }
      qtyCtrl.removeClass('d-none');
    } else {
      row.removeClass('is-selected');
      qtyCtrl.addClass('d-none');
    }
    updateAddonModalSummary();
  });

  // Increment Addon Qty inside Modal
  $(document).on('click', '.btn-addon-modal-inc', function(e) {
    e.preventDefault();
    e.stopPropagation();
    let row = $(this).closest('.addon-selection-row');
    let chk = row.find('.addon-modal-checkbox');
    let qtySpan = row.find('.addon-modal-qty-val');
    let currentVal = parseInt(qtySpan.text()) || 1;
    let newVal = currentVal + 1;
    qtySpan.text(newVal);

    if (!chk.is(':checked')) {
      chk.prop('checked', true);
      row.addClass('is-selected');
      row.find('.addon-modal-qty-control').removeClass('d-none');
    }
    updateAddonModalSummary();
  });

  // Decrement Addon Qty inside Modal
  $(document).on('click', '.btn-addon-modal-dec', function(e) {
    e.preventDefault();
    e.stopPropagation();
    let row = $(this).closest('.addon-selection-row');
    let chk = row.find('.addon-modal-checkbox');
    let qtySpan = row.find('.addon-modal-qty-val');
    let currentVal = parseInt(qtySpan.text()) || 1;

    if (currentVal > 1) {
      qtySpan.text(currentVal - 1);
    } else {
      // Reached 0 -> uncheck
      qtySpan.text(1);
      chk.prop('checked', false);
      row.removeClass('is-selected');
      row.find('.addon-modal-qty-control').addClass('d-none');
    }
    updateAddonModalSummary();
  });

  // Search Addons inside Modal
  $(document).on('input keyup search change paste', '#addonModalSearchInput', function() {
    let query = ($(this).val() || '').toLowerCase().trim();
    $('#addonModalItemsList .addon-selection-row').each(function() {
      let name = String($(this).data('name') || '').toLowerCase();
      let price = String($(this).data('price') || '').toLowerCase();
      let text = $(this).text().toLowerCase();

      if (!query || name.includes(query) || price.includes(query) || text.includes(query)) {
        $(this).removeClass('is-hidden-addon');
      } else {
        $(this).addClass('is-hidden-addon');
      }
    });
  });

  // Select All / Clear inside Modal
  $('#btnSelectAllAddons').on('click', function() {
    $('#addonModalItemsList .addon-selection-row:not(.is-hidden-addon)').each(function() {
      let row = $(this);
      row.find('.addon-modal-checkbox').prop('checked', true);
      row.addClass('is-selected');
      let qtySpan = row.find('.addon-modal-qty-val');
      let currentVal = parseInt(qtySpan.text()) || 0;
      if (currentVal <= 0) qtySpan.text(1);
      row.find('.addon-modal-qty-control').removeClass('d-none');
    });
    updateAddonModalSummary();
  });

  $('#btnClearAllAddons').on('click', function() {
    $('#addonModalItemsList .addon-selection-row').each(function() {
      let row = $(this);
      row.find('.addon-modal-checkbox').prop('checked', false);
      row.removeClass('is-selected');
      row.find('.addon-modal-qty-control').addClass('d-none');
      row.find('.addon-modal-qty-val').text(1);
    });
    updateAddonModalSummary();
  });

  // Apply Addons
  $('#btnApplyAddons').on('click', function() {
    if (!currentEditingDishId || !cart[currentEditingDishId]) {
      $('#rapidAddonModal').fadeOut(150);
      return;
    }

    let selectedAddons = [];
    let totalAddonsCost = 0;

    $('#addonModalItemsList .addon-selection-row').each(function() {
      let chk = $(this).find('.addon-modal-checkbox');
      if (chk.is(':checked')) {
        let price = parseFloat(chk.data('price')) || 0;
        let name = chk.data('name');
        let id = chk.data('id');
        let foodType = chk.data('food-type');
        let qty = parseInt($(this).find('.addon-modal-qty-val').text()) || 1;

        let lineCost = price * qty;
        totalAddonsCost += lineCost;
        selectedAddons.push({
          id: id,
          name: name,
          price: price,
          qty: qty,
          quantity: qty,
          food_type: foodType
        });
      }
    });

    let base = cart[currentEditingDishId].base_price !== undefined ? cart[currentEditingDishId].base_price : cart[currentEditingDishId].price;
    cart[currentEditingDishId].addons = selectedAddons;
    cart[currentEditingDishId].price = base + totalAddonsCost;

    $('#rapidAddonModal').fadeOut(150);
    renderCart();
  });

  // Close / Cancel Addon Modal
  $('#btnCloseAddonModal, #btnCancelAddonModal').on('click', function() {
    $('#rapidAddonModal').fadeOut(150);
  });

  // Increment addon qty from cart chip
  $(document).on('click', '.btn-addon-chip-inc', function(e) {
    e.stopPropagation();
    let dishId = $(this).data('dish-id');
    let addonId = $(this).data('addon-id');

    if (cart[dishId] && cart[dishId].addons) {
      let addon = cart[dishId].addons.find(function(a) { return String(a.id) === String(addonId); });
      if (addon) {
        addon.qty = (addon.qty || addon.quantity || 1) + 1;
        addon.quantity = addon.qty;

        let totalAddonsCost = cart[dishId].addons.reduce(function(sum, a) {
          return sum + ((parseFloat(a.price) || 0) * (a.qty || 1));
        }, 0);

        let base = cart[dishId].base_price !== undefined ? cart[dishId].base_price : cart[dishId].price;
        cart[dishId].price = base + totalAddonsCost;

        renderCart();
      }
    }
  });

  // Decrement addon qty from cart chip
  $(document).on('click', '.btn-addon-chip-dec', function(e) {
    e.stopPropagation();
    let dishId = $(this).data('dish-id');
    let addonId = $(this).data('addon-id');

    if (cart[dishId] && cart[dishId].addons) {
      let addon = cart[dishId].addons.find(function(a) { return String(a.id) === String(addonId); });
      if (addon) {
        let currentQty = addon.qty || addon.quantity || 1;
        if (currentQty > 1) {
          addon.qty = currentQty - 1;
          addon.quantity = addon.qty;
        } else {
          cart[dishId].addons = cart[dishId].addons.filter(function(a) {
            return String(a.id) !== String(addonId);
          });
        }

        let totalAddonsCost = cart[dishId].addons.reduce(function(sum, a) {
          return sum + ((parseFloat(a.price) || 0) * (a.qty || 1));
        }, 0);

        let base = cart[dishId].base_price !== undefined ? cart[dishId].base_price : cart[dishId].price;
        cart[dishId].price = base + totalAddonsCost;

        renderCart();
      }
    }
  });

  // Remove individual addon directly from cart item
  $(document).on('click', '.btn-remove-addon', function(e) {
    e.stopPropagation();
    let dishId = $(this).data('dish-id');
    let addonId = $(this).data('addon-id');

    if (cart[dishId] && cart[dishId].addons) {
      cart[dishId].addons = cart[dishId].addons.filter(function(a) {
        return String(a.id) !== String(addonId);
      });

      let totalAddonsCost = cart[dishId].addons.reduce(function(sum, a) {
        return sum + ((parseFloat(a.price) || 0) * (a.qty || 1));
      }, 0);

      let base = cart[dishId].base_price !== undefined ? cart[dishId].base_price : cart[dishId].price;
      cart[dishId].price = base + totalAddonsCost;

      renderCart();
    }
  });

  // Clear all addons from a cart item directly
  $(document).on('click', '.btn-clear-all-addons', function(e) {
    e.stopPropagation();
    let dishId = $(this).data('dish-id');

    if (cart[dishId]) {
      cart[dishId].addons = [];
      let base = cart[dishId].base_price !== undefined ? cart[dishId].base_price : cart[dishId].price;
      cart[dishId].price = base;
      renderCart();
    }
  });

  // Click Customize button on cart row
  $(document).on('click', '.btn-customize-addons', function(e) {
    e.stopPropagation();
    let id = $(this).data('id') || $(this).closest('.cart-item-card').data('id');
    openAddonModal(id);
  });

  // Render Cart Function
  function renderCart() {
    let container = $('#cartItemsList');
    let items = Object.values(cart);

    // Update dish badges and highlighted cards
    $('.dish-card').removeClass('in-cart');
    $('.dish-cart-badge').hide().text('0');

    if (items.length === 0) {
      container.html(`
        <div class="cart-empty-state" id="cartEmptyMessage">
          <div class="cart-empty-icon"><i class="fa-solid fa-cart-shopping"></i></div>
          <div class="fw-bold">Bill is empty</div>
          <div class="small">Click any dish on the left to add</div>
        </div>
      `);
      recalculateTotals();
      return;
    }

    let html = '';
    items.forEach(function(item) {
      // Highlight card
      let card = $(`.dish-card[data-id="${item.id}"]`);
      if (card.length) {
        card.addClass('in-cart');
        card.find('.dish-cart-badge').text(item.qty).show();
      }

      let lineOriginal = item.price * item.qty;
      let discPercent = parseFloat(item.discount_percent) || 0;
      let discAmt = (lineOriginal * discPercent) / 100;
      let lineNet = lineOriginal - discAmt;

      let hasAvailableAddons = item.available_addons && item.available_addons.length > 0;
      let totalAddonsQtyCount = (item.addons || []).reduce(function(sum, a) { return sum + (a.qty || 1); }, 0);

      let customizeBtnHtml = '';
      if (hasAvailableAddons) {
        let btnClass = totalAddonsQtyCount > 0 ? 'btn-customize-addons active' : 'btn-customize-addons';
        let btnText = totalAddonsQtyCount > 0 ? `<i class="fa-solid fa-sliders"></i> ${totalAddonsQtyCount} Add-on${totalAddonsQtyCount > 1 ? 's' : ''}` : `<i class="fa-solid fa-plus"></i> Add-ons`;
        customizeBtnHtml = `<button type="button" class="${btnClass}" data-id="${item.id}" title="Customize Add-ons">${btnText}</button>`;
      }

      let addonsSubTableHtml = '';
      if (item.addons && item.addons.length > 0) {
        let rowsHtml = '';
        item.addons.forEach(function(a) {
          let dotClass = (a.food_type === 'NON-VEG' || a.food_type === 'non-veg') ? 'nonveg' : 'veg';
          let aQty = a.qty || a.quantity || 1;
          let aPrice = parseFloat(a.price) || 0;
          let aTotal = aPrice * aQty;

          rowsHtml += `
            <tr class="nested-addon-row">
              <td class="addon-td-name">
                <span class="food-symbol-micro ${dotClass}"></span>
                <span class="addon-name-txt" title="${a.name}">${a.name}</span>
              </td>
              <td class="addon-td-rate">₹${aPrice.toFixed(0)}</td>
              <td class="addon-td-qty">
                <div class="nested-addon-qty-ctrl">
                  <button type="button" class="btn-addon-chip-dec" data-dish-id="${item.id}" data-addon-id="${a.id}" title="Decrease quantity">-</button>
                  <span class="nested-addon-qty-num">${aQty}</span>
                  <button type="button" class="btn-addon-chip-inc" data-dish-id="${item.id}" data-addon-id="${a.id}" title="Increase quantity">+</button>
                </div>
              </td>
              <td class="addon-td-total">₹${aTotal.toFixed(2)}</td>
              <td class="addon-td-del">
                <button type="button" class="btn-remove-addon" data-dish-id="${item.id}" data-addon-id="${a.id}" title="Remove ${a.name}">
                  <i class="fa-solid fa-xmark"></i>
                </button>
              </td>
            </tr>
          `;
        });

        addonsSubTableHtml = `
          <div class="cart-dish-addons-panel">
            <div class="addons-panel-header">
              <span class="addons-panel-title">
                <i class="fa-solid fa-puzzle-piece"></i> Mapped Add-ons
              </span>
              <button type="button" class="btn-clear-all-addons" data-dish-id="${item.id}" title="Clear all add-ons from this dish">
                Clear all
              </button>
            </div>
            <table class="addons-nested-table">
              <tbody>
                ${rowsHtml}
              </tbody>
            </table>
          </div>
        `;
      }

      let basePrice = item.base_price !== undefined ? item.base_price : item.price;
      let foodTypeDot = (item.food_type === 'non-veg' || item.food_type === 'nonveg') ? 'nonveg' : 'veg';

      let itemHeaderHtml = '';
      if (item.is_addon) {
        itemHeaderHtml = `
          <div class="d-flex align-items-center gap-1 min-w-0" style="min-width: 0;">
            <span class="food-symbol-micro ${foodTypeDot}"></span>
            <span class="cart-item-title text-truncate" title="${item.name}">${item.name}</span>
            <span class="badge bg-warning text-dark ms-1" style="font-size: 0.6rem; font-weight: 800; padding: 1px 4px; border-radius: 4px; flex-shrink: 0;">Add-on</span>
          </div>
          <div class="cart-item-price-meta">
            Rate: ₹${basePrice.toFixed(2)} / portion
          </div>
        `;
      } else {
        itemHeaderHtml = `
          <div class="d-flex align-items-center gap-1 min-w-0" style="min-width: 0;">
            <span class="food-symbol-micro ${foodTypeDot}"></span>
            <span class="cart-item-title text-truncate" title="${item.name}">${item.name}</span>
            ${customizeBtnHtml}
          </div>
          <div class="cart-item-price-meta">
            Base: ₹${basePrice.toFixed(2)}${item.price > basePrice ? ` • <span class="text-primary fw-bold">Unit: ₹${item.price.toFixed(2)}</span>` : ''}
          </div>
        `;
      }

      html += `
        <div class="cart-item-card" data-id="${item.id}">
          <div class="cart-item-main-row">
            <div class="cart-item-info">
              ${itemHeaderHtml}
            </div>
            
            <div class="cart-item-actions-wrap">
              <div class="qty-control">
                <button type="button" class="btn-qty btn-dec-qty" data-id="${item.id}">-</button>
                <div class="qty-display">${item.qty}</div>
                <button type="button" class="btn-qty btn-inc-qty" data-id="${item.id}">+</button>
              </div>

              <div class="item-disc-wrap" title="Discount % for this item">
                <span class="item-disc-label">Disc</span>
                <input type="number" class="item-disc-input" data-id="${item.id}" min="0" max="100" step="1" value="${discPercent}" placeholder="0">
                <span class="item-disc-unit">%</span>
              </div>

              <div class="cart-item-total">
                <div class="cart-item-net-val">₹${lineNet.toFixed(2)}</div>
                <div class="item-disc-tag" style="${discAmt > 0 ? '' : 'display: none;'}">-₹${discAmt.toFixed(2)}</div>
              </div>

              <button type="button" class="btn-remove-item" data-id="${item.id}" title="Remove item">
                <i class="fa-solid fa-xmark"></i>
              </button>
            </div>
          </div>

          ${addonsSubTableHtml}
        </div>
      `;
    });

    container.html(html);
    recalculateTotals();
  }

  function getComputedGrandTotal() {
    let items = Object.values(cart);
    let netTaxable = 0;
    items.forEach(function(i) {
      let orig = i.price * i.qty;
      let discPercent = parseFloat(i.discount_percent) || 0;
      let discAmt = (orig * discPercent) / 100;
      netTaxable += (orig - discAmt);
    });

    let gstAmount = 0;
    if (IS_GST_ENABLED && RESTAURANT_GST_PERCENT > 0) {
      gstAmount = (netTaxable * RESTAURANT_GST_PERCENT) / 100;
    }

    let exactTotal = netTaxable + gstAmount;
    return Math.round(exactTotal);
  }

  function recalculateTotals() {
    let items = Object.values(cart);
    let grossSubtotal = 0;
    let totalDiscountAmount = 0;
    let netTaxable = 0;

    items.forEach(function(i) {
      let orig = i.price * i.qty;
      let discPercent = parseFloat(i.discount_percent) || 0;
      let discAmt = (orig * discPercent) / 100;
      grossSubtotal += orig;
      totalDiscountAmount += discAmt;
      netTaxable += (orig - discAmt);
    });

    let gstAmount = 0;
    if (IS_GST_ENABLED && RESTAURANT_GST_PERCENT > 0) {
      gstAmount = (netTaxable * RESTAURANT_GST_PERCENT) / 100;
    }

    let exactTotal = netTaxable + gstAmount;
    let grandTotal = Math.round(exactTotal);
    let roundOff = grandTotal - exactTotal;

    $('#lblSubtotal').text('₹' + grossSubtotal.toFixed(2));
    
    if (totalDiscountAmount > 0) {
      $('#rowItemDiscounts').show();
      $('#lblItemDiscounts').text('-₹' + totalDiscountAmount.toFixed(2));
    } else {
      $('#rowItemDiscounts').hide();
    }

    if (IS_GST_ENABLED) {
      $('#lblTaxable').text('₹' + netTaxable.toFixed(2));
      $('#lblGst').text('₹' + gstAmount.toFixed(2));
    }
    $('#lblRoundOff').text((roundOff >= 0 ? '+' : '') + '₹' + roundOff.toFixed(2));
    $('#lblGrandTotal').text('₹' + grandTotal.toFixed(2));

    // Automatically update cash/payment amounts when grand total changes (e.g. from discount or quantity change)
    let cash = parseFloat($('#inputCashAmount').val()) || 0;
    let upi = parseFloat($('#inputUpiAmount').val()) || 0;

    if (grandTotal <= 0) {
      $('#inputCashAmount').val(0);
      $('#inputUpiAmount').val(0);
    } else if (upi <= 0) {
      // Full Cash mode: auto-adjust cash amount to match new discounted total
      $('#inputCashAmount').val(grandTotal);
    } else if (cash > 0 && upi > 0) {
      // Split payment mode: keep UPI as set, auto-adjust cash to remaining balance
      let remainingCash = Math.max(0, grandTotal - upi);
      $('#inputCashAmount').val(remainingCash);
    } else if (cash <= 0 && upi > 0) {
      // Full UPI mode: auto-adjust UPI to match new discounted total
      $('#inputUpiAmount').val(grandTotal);
    }

    // Update Mobile POS Floating Bar & Badges
    let totalItemCount = items.reduce(function(sum, it) { return sum + it.qty; }, 0);
    $('#floatCartItemCount').text(totalItemCount + ' Item' + (totalItemCount === 1 ? '' : 's'));
    $('#floatCartTotal').text('₹' + grandTotal.toFixed(2));

    if (totalItemCount > 0) {
      $('#mobileBillBadge').text(totalItemCount).show();
      if (currentMobileTab === 'menu' && window.innerWidth <= 991.98) {
        $('#mobileFloatingCartBar').fadeIn(150);
      }
    } else {
      $('#mobileBillBadge').hide();
      $('#mobileFloatingCartBar').hide();
    }

    updatePaymentIndicator();
  }

  function updatePaymentIndicator() {
    let grandTotal = getComputedGrandTotal();
    let cash = parseFloat($('#inputCashAmount').val()) || 0;
    let upi = parseFloat($('#inputUpiAmount').val()) || 0;
    let totalPaid = cash + upi;
    let diff = totalPaid - grandTotal;

    let badge = $('#paymentStatusBadge');
    let text = $('#paymentStatusText');
    let diffLbl = $('#paymentDiffText');

    badge.removeClass('exact due excess');

    if (diff === 0 && grandTotal > 0) {
      badge.addClass('exact');
      text.html('<i class="fa-solid fa-circle-check"></i> Payment Balanced');
      diffLbl.text('₹' + totalPaid.toFixed(2));
    } else if (diff < 0) {
      badge.addClass('due');
      text.html('<i class="fa-solid fa-circle-exclamation"></i> Remaining Due:');
      diffLbl.text('₹' + Math.abs(diff).toFixed(2));
    } else {
      badge.addClass('excess');
      text.html('<i class="fa-solid fa-hand-holding-dollar"></i> Change to Return:');
      diffLbl.text('₹' + diff.toFixed(2));
    }
  }

  // Keyboard Shortcuts: Press '/' or 'F2' to search, 'Ctrl+Enter' to place order
  $(document).on('keydown', function(e) {
    if (e.key === '/' && !$(e.target).is('input, textarea, select')) {
      e.preventDefault();
      $('#menuSearchInput').focus();
    }
    if (e.ctrlKey && e.key === 'Enter') {
      e.preventDefault();
      $('#btnSubmitBill').trigger('click');
    }
    if (e.key === 'Escape') {
      $('#rapidAddonModal').fadeOut(150);
      $('#rapidReceiptModal').fadeOut(150);
    }
  });

  // Submit Rapid Bill
  $('#btnSubmitBill').on('click', function() {
    let items = Object.values(cart);
    if (items.length === 0) {
      alert('Please add at least one dish to generate bill.');
      return;
    }

    let grandTotal = getComputedGrandTotal();
    let cash = parseFloat($('#inputCashAmount').val()) || 0;
    let upi = parseFloat($('#inputUpiAmount').val()) || 0;

    if (cash <= 0 && upi <= 0) {
      alert('Please specify either Cash or UPI payment amount.');
      return;
    }

    let btn = $(this);
    btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Processing Bill...');

    let payload = {
      _token: $('meta[name="csrf-token"]').attr('content'),
      customer_name: $('#custName').val().trim(),
      customer_phone: $('#custPhone').val().trim(),
      order_type: orderType,
      table_id: $('#tableIdSelect').val() || null,
      cash_amount: cash,
      upi_amount: upi,
      print_bill: $('#chkPrintBill').is(':checked') ? 1 : 0,
      items: items.map(function(i) {
        return {
          dish_id: i.id,
          quantity: i.qty,
          price: i.base_price !== undefined ? i.base_price : i.price,
          discount_percentage: parseFloat(i.discount_percent) || 0,
          addons: (i.addons || []).map(function(a) {
            return {
              id: a.id,
              name: a.name,
              price: a.price,
              qty: a.qty || a.quantity || 1,
              quantity: a.qty || a.quantity || 1
            };
          })
        };
      })
    };

    $.ajax({
      url: "{{ route('rapid.bill.store') }}",
      type: "POST",
      contentType: "application/json",
      data: JSON.stringify(payload),
      dataType: "json",
      success: function(res) {
        if (res.success) {
          lastOrderInvoiceUrl = res.invoice_url;
          
          if ($('#chkPrintBill').is(':checked')) {
            // If print bill is checked, open modal preview with print option and option to open invoice
            renderReceiptPreview(res.receipt);
            $('#rapidReceiptModal').fadeIn(200);

            // Auto-trigger print preview
            setTimeout(function() {
              window.open(res.invoice_url + '?autoprint=1&return=rapid_bill', '_blank');
            }, 300);
          } else {
            alert('Order #' + res.order_id + ' generated successfully!');
          }

          // Reset cart & form for rapid next order
          cart = {};
          $('#custName').val('');
          $('#custPhone').val('');
          $('#inputCashAmount').val(0);
          $('#inputUpiAmount').val(0);
          renderCart();
        } else {
          alert('Error: ' + (res.message || 'Failed to create rapid bill'));
        }
      },
      error: function(xhr) {
        let msg = 'Failed to generate rapid bill.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }
        alert(msg);
      },
      complete: function() {
        btn.prop('disabled', false).html('<i class="fa-solid fa-bolt"></i> Place & Generate Bill');
      }
    });
  });

  function renderReceiptPreview(r) {
    if (!r) return;
    let itemsHtml = '';
    r.items.forEach(function(it) {
      let addonsLine = '';
      if (it.addons && it.addons.length > 0) {
        addonsLine = `<div style="font-size:0.72rem; color:#64748b; padding-left:6px;">${it.addons.map(function(a) { 
          let qtyStr = (a.qty && a.qty > 1) ? ` x${a.qty}` : '';
          return '+ ' + a.name + qtyStr + ' (₹' + (parseFloat(a.price) * (a.qty || 1)).toFixed(2) + ')'; 
        }).join(', ')}</div>`;
      }

      itemsHtml += `
        <div style="margin-bottom: 4px;">
          <div style="display:flex; justify-content:space-between;">
            <span>${it.dish_name || it.name} x${it.qty}</span>
            <span>₹${parseFloat(it.total).toFixed(2)}</span>
          </div>
          ${addonsLine}
        </div>
      `;
    });

    let paymentsHtml = '';
    r.payments.forEach(function(p) {
      paymentsHtml += `
        <div style="display:flex; justify-content:space-between;">
          <span>${p.method}:</span>
          <span>₹${parseFloat(p.amount).toFixed(2)}</span>
        </div>
      `;
    });

    let html = `
      <div class="receipt-center">
        <h4 style="margin:0 0 2px 0; font-weight:800;">${r.restaurant_name}</h4>
        <div style="font-size:0.75rem;">${r.address || ''}</div>
        ${r.gstin ? `<div style="font-size:0.75rem;">GSTIN: ${r.gstin}</div>` : ''}
        <div class="receipt-divider"></div>
        <div style="font-size:0.75rem; display:flex; justify-content:space-between;">
          <span>Bill #: <strong>${r.order_id}</strong></span>
          <span>KOT #: <strong>${r.kot_no || '1'}</strong></span>
        </div>
        <div style="font-size:0.75rem; text-align:left;">Customer: ${r.customer}</div>
        <div class="receipt-divider"></div>
      </div>
      <div>${itemsHtml}</div>
      <div class="receipt-divider"></div>
      <div style="display:flex; justify-content:space-between; font-weight:700;">
        <span>Grand Total:</span>
        <span>₹${parseFloat(r.grand_total).toFixed(2)}</span>
      </div>
      <div class="receipt-divider"></div>
      <div>${paymentsHtml}</div>
      <div class="receipt-center" style="margin-top:10px; font-size:0.75rem;">
        Thank you! Visit again.
      </div>
    `;
    $('#receiptPaperBody').html(html);
  }

  // Universal Sidebar Hamburger Toggle Handler
  $(document).on('click', '#rapidSidebarToggle, #mobile-collapse, #sidebar-hide', function(e) {
    e.preventDefault();
    e.stopPropagation();
    let sidebar = $('.pc-sidebar');
    let isMobile = (window.innerWidth <= 1024);
    
    if (isMobile) {
      if (sidebar.hasClass('mob-sidebar-active')) {
        sidebar.removeClass('mob-sidebar-active');
        $('.pc-menu-overlay').remove();
      } else {
        sidebar.addClass('mob-sidebar-active');
        if ($('.pc-menu-overlay').length === 0) {
          let overlay = $('<div class="pc-menu-overlay"></div>');
          $('body').append(overlay);
          overlay.on('click', function() {
            sidebar.removeClass('mob-sidebar-active');
            $(this).remove();
          });
        }
      }
    } else {
      sidebar.toggleClass('pc-sidebar-hide');
      if (sidebar.hasClass('pc-sidebar-hide')) {
        $('.pc-container').css('margin-left', '0px');
      } else {
        $('.pc-container').css('margin-left', '');
      }
    }
  });

  $(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
      $('.pc-sidebar').removeClass('mob-sidebar-active');
      $('.pc-menu-overlay').remove();
    }
  });

  $(document).on('click', '.pc-sidebar a', function() {
    if (window.innerWidth <= 1024 && !$(this).parent().hasClass('pc-hasmenu')) {
      $('.pc-sidebar').removeClass('mob-sidebar-active');
      $('.pc-menu-overlay').remove();
    }
  });

  $('#btnCloseModal, #btnNextBill').on('click', function() {
    $('#rapidReceiptModal').fadeOut(150);
  });

  $('#btnDirectPrint').on('click', function() {
    if (lastOrderInvoiceUrl) {
      window.open(lastOrderInvoiceUrl + '?autoprint=1&return=rapid_bill', '_blank');
    }
  });

});
</script>

@include('includes.script')

</body>
</html>
