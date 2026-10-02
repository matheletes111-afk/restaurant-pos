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

    * { box-sizing: border-box; }

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
      padding: 12px 24px;
      border-radius: var(--rp-radius-lg);
      margin-bottom: 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
      border-bottom: 3px solid var(--rp-primary);
    }

    .rapid-brand {
      display: flex;
      align-items: center;
      gap: 14px;
    }

    .rapid-brand-icon {
      width: 44px;
      height: 44px;
      background: var(--rp-primary-gradient);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 1.3rem;
      box-shadow: 0 4px 12px rgba(255, 94, 20, 0.4);
    }

    .rapid-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.35rem;
      font-weight: 800;
      color: #ffffff;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .rapid-badge-speed {
      background: rgba(255, 94, 20, 0.2);
      border: 1px solid rgba(255, 94, 20, 0.4);
      color: #ff9d66;
      font-size: 0.72rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      padding: 3px 10px;
      border-radius: 20px;
    }

    .rapid-top-actions {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .rapid-btn-secondary {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #ffffff !important;
      padding: 7px 16px;
      border-radius: 30px;
      font-size: 0.85rem;
      font-weight: 700;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s ease;
    }

    .rapid-btn-secondary:hover {
      background: rgba(255, 255, 255, 0.2);
      color: #ffffff !important;
      transform: translateY(-1px);
    }

    /* Main Grid Layout */
    .rapid-workspace {
      display: grid;
      grid-template-columns: 1fr 440px;
      gap: 20px;
      align-items: start;
    }

    @media (max-width: 1200px) {
      .rapid-workspace {
        grid-template-columns: 1fr 400px;
      }
    }

    @media (max-width: 992px) {
      .rapid-workspace {
        grid-template-columns: 1fr;
      }
    }

    /* Menu Section Styles */
    .menu-panel {
      background: var(--rp-card);
      border-radius: var(--rp-radius-lg);
      border: 1.5px solid var(--rp-border);
      padding: 18px;
      box-shadow: 0 4px 20px rgba(15, 23, 42, 0.04);
    }

    .menu-filters-bar {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-bottom: 16px;
    }

    .search-veg-row {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .search-input-wrap {
      position: relative;
      flex: 1;
    }

    .search-input-wrap i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--rp-muted);
      font-size: 0.95rem;
    }

    .search-input {
      width: 100%;
      border: 1.5px solid var(--rp-border);
      border-radius: 30px;
      padding: 10px 18px 10px 40px;
      font-size: 0.92rem;
      font-weight: 600;
      background: #f8fafc;
      outline: none;
      transition: all 0.2s ease;
    }

    .search-input:focus {
      background: #ffffff;
      border-color: var(--rp-primary);
      box-shadow: 0 0 0 4px rgba(255, 94, 20, 0.12);
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
    }

    /* Food Type Switcher */
    .food-type-toggles {
      display: flex;
      align-items: center;
      background: #f1f5f9;
      border-radius: 30px;
      padding: 3px;
      gap: 2px;
    }

    .type-toggle-btn {
      border: none;
      background: transparent;
      padding: 7px 14px;
      border-radius: 24px;
      font-size: 0.8rem;
      font-weight: 700;
      color: var(--rp-muted);
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s ease;
    }

    .type-toggle-btn.active {
      background: #ffffff;
      color: var(--rp-dark);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .type-toggle-btn.active.veg { color: var(--rp-success-dark); }
    .type-toggle-btn.active.nonveg { color: var(--rp-danger); }

    /* Category Filter Tabs */
    .category-scroll-tabs {
      display: flex;
      align-items: center;
      gap: 8px;
      overflow-x: auto;
      padding-bottom: 6px;
      scrollbar-width: thin;
    }

    .category-scroll-tabs::-webkit-scrollbar { height: 4px; }
    .category-scroll-tabs::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .cat-tab-btn {
      white-space: nowrap;
      border: 1.5px solid var(--rp-border);
      background: #f8fafc;
      color: var(--rp-slate);
      padding: 7px 14px;
      border-radius: 30px;
      font-size: 0.82rem;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s ease;
    }

    .cat-tab-btn:hover {
      background: #f1f5f9;
      border-color: #cbd5e1;
    }

    .cat-tab-btn.active {
      background: var(--rp-primary);
      border-color: var(--rp-primary);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(255, 94, 20, 0.3);
    }

    .cat-tab-badge {
      background: rgba(0, 0, 0, 0.08);
      padding: 1px 6px;
      border-radius: 10px;
      font-size: 0.72rem;
    }

    .cat-tab-btn.active .cat-tab-badge {
      background: rgba(255, 255, 255, 0.25);
      color: #ffffff;
    }

    /* Dish Cards Grid */
    .dishes-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
      gap: 12px;
      max-height: calc(100vh - 270px);
      overflow-y: auto;
      padding-right: 4px;
    }

    .dishes-grid::-webkit-scrollbar { width: 5px; }
    .dishes-grid::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .dish-card {
      background: #ffffff;
      border: 1.5px solid var(--rp-border);
      border-radius: var(--rp-radius-md);
      padding: 12px;
      cursor: pointer;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      position: relative;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      user-select: none;
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
      margin-bottom: 6px;
    }

    .food-symbol {
      width: 16px;
      height: 16px;
      border: 1.5px solid #ccc;
      border-radius: 3px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 1px;
    }

    .food-symbol.veg { border-color: #10b981; }
    .food-symbol.veg::after {
      content: '';
      width: 8px;
      height: 8px;
      background: #10b981;
      border-radius: 50%;
    }

    .food-symbol.nonveg { border-color: #ef4444; }
    .food-symbol.nonveg::after {
      content: '';
      width: 8px;
      height: 8px;
      background: #ef4444;
      border-radius: 50%;
    }

    .dish-cart-badge {
      background: var(--rp-primary);
      color: #ffffff;
      font-size: 0.75rem;
      font-weight: 800;
      padding: 2px 7px;
      border-radius: 12px;
      display: none;
    }

    .dish-card.in-cart .dish-cart-badge {
      display: inline-block;
    }

    .dish-title {
      font-family: 'Outfit', sans-serif;
      font-size: 0.92rem;
      font-weight: 700;
      color: var(--rp-dark);
      margin: 0 0 4px 0;
      line-height: 1.25;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
      min-height: 2.3em;
    }

    .dish-category-label {
      font-size: 0.72rem;
      color: var(--rp-muted);
      font-weight: 600;
      margin-bottom: 8px;
    }

    .dish-bottom-bar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: auto;
      padding-top: 6px;
      border-top: 1px dashed var(--rp-border-light);
    }

    .dish-price {
      font-family: 'Outfit', sans-serif;
      font-size: 1.05rem;
      font-weight: 800;
      color: var(--rp-dark);
    }

    .dish-add-btn {
      width: 28px;
      height: 28px;
      background: var(--rp-primary-light);
      border: 1px solid rgba(255, 94, 20, 0.3);
      color: var(--rp-primary);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.85rem;
      transition: all 0.15s ease;
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
      top: 20px;
      overflow: hidden;
    }

    .checkout-header {
      background: #f8fafc;
      padding: 14px 18px;
      border-bottom: 1.5px solid var(--rp-border);
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .checkout-title {
      font-family: 'Outfit', sans-serif;
      font-size: 1.1rem;
      font-weight: 800;
      color: var(--rp-dark);
      margin: 0;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .btn-clear-all {
      background: transparent;
      border: none;
      color: var(--rp-danger);
      font-size: 0.8rem;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 4px;
      padding: 4px 8px;
      border-radius: 6px;
    }

    .btn-clear-all:hover {
      background: var(--rp-danger-bg);
    }

    /* Customer & Dining Meta Row */
    .customer-meta-box {
      padding: 14px 18px 10px;
      background: #ffffff;
      border-bottom: 1px solid var(--rp-border-light);
    }

    .customer-input-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      margin-bottom: 8px;
    }

    .c-input {
      width: 100%;
      border: 1px solid var(--rp-border);
      border-radius: 8px;
      padding: 7px 10px;
      font-size: 0.82rem;
      font-weight: 600;
      background: #f8fafc;
      outline: none;
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
      border-radius: 8px;
      padding: 6px 10px;
      font-size: 0.78rem;
      font-weight: 700;
      color: var(--rp-slate);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.15s ease;
    }

    .dining-btn.active {
      background: #0f172a;
      border-color: #0f172a;
      color: #ffffff;
    }

    .table-select-wrap {
      margin-top: 8px;
      display: none;
    }

    /* Cart Items Container */
    .cart-items-wrapper {
      max-height: 230px;
      overflow-y: auto;
      padding: 8px 14px;
      background: #ffffff;
      border-bottom: 1.5px solid var(--rp-border);
    }

    .cart-items-wrapper::-webkit-scrollbar { width: 4px; }
    .cart-items-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }

    .cart-empty-state {
      padding: 36px 16px;
      text-align: center;
      color: var(--rp-muted);
    }

    .cart-empty-icon {
      font-size: 2.2rem;
      color: #cbd5e1;
      margin-bottom: 8px;
    }

    .cart-item-row {
      display: grid;
      grid-template-columns: 1fr auto auto auto auto;
      align-items: center;
      gap: 6px;
      padding: 8px 4px;
      border-bottom: 1px dashed var(--rp-border-light);
    }

    .cart-item-row:last-child { border-bottom: none; }

    .cart-item-title {
      font-size: 0.84rem;
      font-weight: 700;
      color: var(--rp-dark);
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 110px;
    }

    .cart-item-price {
      font-size: 0.74rem;
      color: var(--rp-muted);
      font-weight: 600;
    }

    /* Qty Control */
    .qty-control {
      display: flex;
      align-items: center;
      background: #f1f5f9;
      border-radius: 6px;
      padding: 2px;
    }

    .btn-qty {
      width: 20px;
      height: 20px;
      border: none;
      background: #ffffff;
      border-radius: 4px;
      font-size: 0.75rem;
      font-weight: 800;
      color: var(--rp-slate);
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.1s ease;
    }

    .btn-qty:hover { background: var(--rp-primary); color: #fff; }

    .qty-display {
      width: 22px;
      text-align: center;
      font-size: 0.8rem;
      font-weight: 800;
      color: var(--rp-dark);
    }

    /* Item-wise Discount Box */
    .item-disc-wrap {
      display: flex;
      align-items: center;
      background: #f8fafc;
      border: 1px solid var(--rp-border);
      border-radius: 6px;
      padding: 2px 4px;
      gap: 1px;
    }

    .item-disc-label {
      font-size: 0.68rem;
      font-weight: 700;
      color: var(--rp-muted);
    }

    .item-disc-input {
      width: 32px;
      border: none;
      background: transparent;
      font-size: 0.75rem;
      font-weight: 800;
      color: #059669;
      text-align: right;
      outline: none;
      padding: 0;
    }

    .item-disc-unit {
      font-size: 0.7rem;
      font-weight: 800;
      color: var(--rp-muted);
    }

    .cart-item-total {
      font-family: 'Outfit', sans-serif;
      min-width: 60px;
      text-align: right;
    }

    .cart-item-net-val {
      font-size: 0.9rem;
      font-weight: 800;
      color: var(--rp-dark);
      line-height: 1.1;
    }

    .item-disc-tag {
      font-size: 0.65rem;
      color: #059669;
      font-weight: 700;
      line-height: 1;
    }

    .btn-remove-item {
      background: transparent;
      border: none;
      color: #94a3b8;
      cursor: pointer;
      padding: 3px;
      font-size: 0.82rem;
      transition: color 0.15s ease;
    }

    .btn-remove-item:hover { color: var(--rp-danger); }

    /* Summary Calculation Deck */
    .summary-deck {
      background: #f8fafc;
      padding: 12px 18px;
      border-bottom: 1.5px solid var(--rp-border);
    }

    .summary-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 0.82rem;
      font-weight: 600;
      color: var(--rp-muted);
      margin-bottom: 5px;
    }

    .summary-row.grand-total-row {
      margin-top: 8px;
      padding-top: 8px;
      border-top: 1.5px dashed #cbd5e1;
      font-size: 1.25rem;
      font-weight: 900;
      color: var(--rp-dark);
    }

    .grand-total-val {
      font-family: 'Outfit', sans-serif;
      color: var(--rp-primary);
      font-size: 1.45rem;
    }

    /* Split Payment Box (Cash & UPI) */
    .split-payment-box {
      padding: 14px 18px;
      background: #ffffff;
      border-bottom: 1.5px solid var(--rp-border);
    }

    .split-header {
      font-size: 0.82rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--rp-dark);
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .split-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin-bottom: 8px;
    }

    .payment-field-card {
      border: 1.5px solid var(--rp-border);
      border-radius: 10px;
      padding: 8px 10px;
      background: #f8fafc;
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
      font-size: 0.72rem;
      font-weight: 800;
      text-transform: uppercase;
      margin-bottom: 4px;
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
    }

    .p-input-wrap span {
      position: absolute;
      left: 6px;
      font-weight: 800;
      color: var(--rp-slate);
      font-size: 0.85rem;
    }

    .p-input {
      width: 100%;
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      padding: 6px 6px 6px 20px;
      font-family: 'Outfit', monospace;
      font-size: 1.05rem;
      font-weight: 800;
      color: var(--rp-dark);
      background: #ffffff;
      outline: none;
    }

    .p-input:focus {
      border-color: var(--rp-primary);
    }

    /* Quick Split Action Chips */
    .quick-split-bar {
      display: flex;
      align-items: center;
      gap: 6px;
      flex-wrap: wrap;
    }

    .btn-split-chip {
      background: #f1f5f9;
      border: 1px solid var(--rp-border);
      border-radius: 14px;
      padding: 3px 9px;
      font-size: 0.72rem;
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
      margin-top: 8px;
      padding: 6px 10px;
      border-radius: 8px;
      font-size: 0.78rem;
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
      padding: 14px 18px;
      background: #ffffff;
    }

    .print-toggle-wrap {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 12px;
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--rp-dark);
      cursor: pointer;
    }

    .print-toggle-checkbox {
      width: 18px;
      height: 18px;
      accent-color: var(--rp-primary);
      cursor: pointer;
    }

    .btn-submit-rapid {
      width: 100%;
      background: var(--rp-primary-gradient);
      color: #ffffff;
      border: none;
      border-radius: 12px;
      padding: 13px;
      font-family: 'Outfit', sans-serif;
      font-size: 1.1rem;
      font-weight: 800;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      box-shadow: 0 6px 20px rgba(255, 94, 20, 0.35);
      cursor: pointer;
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-submit-rapid:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px rgba(255, 94, 20, 0.45);
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
      padding: 16px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .receipt-paper {
      background: #fdfdfd;
      border: 1px solid #e2e8f0;
      padding: 16px;
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.8rem;
      line-height: 1.4;
      color: #1e293b;
      max-height: 380px;
      overflow-y: auto;
    }

    .receipt-center { text-align: center; }
    .receipt-divider { border-top: 1px dashed #94a3b8; margin: 8px 0; }
  </style>
</head>
<body>

@include('includes.sidebar')

<div class="pc-container">
  <div class="pc-content">

    <!-- Top Header Bar -->
    <div class="rapid-topbar">
      <div class="rapid-brand">
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

    <!-- Main Workspace Grid -->
    <div class="rapid-workspace">

      <!-- LEFT: Fast Menu & Dish Explorer -->
      <div class="menu-panel">
        
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
              @endphp
              <div class="dish-card" 
                   data-id="{{ $dish->id }}"
                   data-name="{{ $dish->name }}"
                   data-price="{{ $dishPrice }}"
                   data-category-id="{{ $category->id }}"
                   data-food-type="{{ $dishFoodType }}"
                   data-discount="{{ $dishDiscount }}"
                   data-search-terms="{{ strtolower($dish->name . ' ' . $category->name . ' ' . ($dish->code ?? '')) }}">
                
                <div>
                  <div class="dish-top-meta">
                    <span class="food-symbol {{ $dishFoodType === 'non-veg' || $dishFoodType === 'nonveg' ? 'nonveg' : 'veg' }}"></span>
                    <span class="dish-cart-badge" id="dishBadge-{{ $dish->id }}">0</span>
                  </div>

                  <h3 class="dish-title" title="{{ $dish->name }}">{{ $dish->name }}</h3>
                  <div class="dish-category-label">{{ $category->name }}</div>
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
        </div>

      </div>

      <!-- RIGHT: Rapid Checkout & Split Payment Pane -->
      <div class="bill-checkout-panel">
        
        <div class="checkout-header">
          <h2 class="checkout-title">
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

@include('includes.footer')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
  // Global POS State
  const RESTAURANT_GST_PERCENT = parseFloat("{{ $gstPercentage ?? 0 }}");
  const IS_GST_ENABLED = {{ $isGstEnabled ? 'true' : 'false' }};
  
  let cart = {}; // { dishId: { id, name, price, qty, discount_percent } }
  let orderType = 'takeaway';
  let activeCategoryId = 'all';
  let activeFoodType = 'all';
  let lastOrderInvoiceUrl = null;

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

  // Add Dish to Cart
  $(document).on('click', '.dish-card', function(e) {
    let dishId = $(this).data('id');
    let dishName = $(this).data('name');
    let dishPrice = parseFloat($(this).data('price')) || 0;
    let dishDiscount = parseFloat($(this).data('discount')) || 0;

    if (cart[dishId]) {
      cart[dishId].qty += 1;
    } else {
      cart[dishId] = {
        id: dishId,
        name: dishName,
        price: dishPrice,
        qty: 1,
        discount_percent: dishDiscount
      };
    }
    renderCart();
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
      let row = $(`.cart-item-row[data-id="${id}"]`);
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

      html += `
        <div class="cart-item-row" data-id="${item.id}">
          <div>
            <div class="cart-item-title" title="${item.name}">${item.name}</div>
            <div class="cart-item-price">₹${item.price.toFixed(2)}</div>
          </div>
          
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

          <button type="button" class="btn-remove-item" data-id="${item.id}">
            <i class="fa-solid fa-xmark"></i>
          </button>
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
          price: i.price,
          discount_percentage: parseFloat(i.discount_percent) || 0
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
      itemsHtml += `
        <div style="display:flex; justify-content:space-between; margin-bottom: 3px;">
          <span>${it.name} x${it.qty}</span>
          <span>₹${parseFloat(it.total).toFixed(2)}</span>
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

</body>
</html>
