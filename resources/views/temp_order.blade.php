<!DOCTYPE html>
<html lang="en">
<head>
  <title>Customer Order | {{ $restaurant_details->name ?? 'Premium Dining' }}</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root {
      --gold: #C9A84C;
      --gold-light: #E8C97A;
      --gold-dim: rgba(201,168,76,0.15);
      --obsidian: #0A0A0B;
      --deep: #111114;
      --surface: #17171C;
      --surface-2: #1E1E25;
      --surface-3: #26262F;
      --rim: rgba(255,255,255,0.07);
      --rim-strong: rgba(255,255,255,0.12);
      --text-primary: #F2EEE6;
      --text-secondary: rgba(242,238,230,0.55);
      --text-muted: rgba(242,238,230,0.3);
      --success: #3DD68C;
      --danger: #FF6B6B;
      --veg: #4ADE80;
      --nonveg: #F87171;
      --radius-xl: 24px;
      --radius-lg: 18px;
      --radius-md: 12px;
      --radius-sm: 8px;
      --glow: 0 0 40px rgba(201,168,76,0.12);
    }

    *, *::before, *::after { box-sizing: border-box; }

    body {
      background-color: var(--obsidian);
      background-image:
        radial-gradient(ellipse 80% 50% at 50% -10%, rgba(201,168,76,0.08) 0%, transparent 60%),
        radial-gradient(ellipse 60% 40% at 80% 100%, rgba(201,168,76,0.05) 0%, transparent 50%);
      font-family: 'DM Sans', sans-serif;
      color: var(--text-primary);
      min-height: 100vh;
      padding-bottom: 80px;
    }

    /* Noise texture overlay */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
      pointer-events: none;
      z-index: 0;
      opacity: 0.6;
    }

    .container { max-width: 1380px; padding-top: 40px; position: relative; z-index: 1; }

    .gold-text { color: var(--gold); }

    /* Restaurant Header */
    .restaurant-header {
      position: relative;
      text-align: center;
      padding: 64px 40px 52px;
      margin-bottom: 48px;
      border-radius: var(--radius-xl);
      background: var(--surface);
      border: 1px solid var(--rim-strong);
      overflow: hidden;
      box-shadow: var(--glow), 0 32px 80px rgba(0,0,0,0.5);
    }

    .restaurant-header::before {
      content: '';
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse 70% 60% at 50% 0%, rgba(201,168,76,0.14) 0%, transparent 65%),
        linear-gradient(180deg, rgba(201,168,76,0.04) 0%, transparent 100%);
      pointer-events: none;
    }

    .header-corner {
      position: absolute;
      width: 60px;
      height: 60px;
      border-color: var(--gold);
      border-style: solid;
      opacity: 0.35;
    }
    .header-corner.tl { top: 20px; left: 20px; border-width: 1px 0 0 1px; border-radius: 4px 0 0 0; }
    .header-corner.tr { top: 20px; right: 20px; border-width: 1px 1px 0 0; border-radius: 0 4px 0 0; }
    .header-corner.bl { bottom: 20px; left: 20px; border-width: 0 0 1px 1px; border-radius: 0 0 0 4px; }
    .header-corner.br { bottom: 20px; right: 20px; border-width: 0 1px 1px 0; border-radius: 0 0 4px 0; }

    .header-icon-ring {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
      margin-bottom: 24px;
      position: relative;
      z-index: 1;
      box-shadow: 0 0 0 12px rgba(201,168,76,0.1), 0 0 0 24px rgba(201,168,76,0.05);
    }

    .header-icon-ring i { font-size: 1.6rem; color: var(--obsidian); }

    .restaurant-header h1 {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 700;
      font-size: clamp(2rem, 5vw, 3.4rem);
      letter-spacing: 0.02em;
      color: var(--text-primary);
      margin-bottom: 10px;
      position: relative;
      z-index: 1;
      line-height: 1.1;
    }

    .header-tagline {
      font-size: 0.95rem;
      color: var(--text-secondary);
      letter-spacing: 0.12em;
      text-transform: uppercase;
      font-weight: 300;
      position: relative;
      z-index: 1;
      margin-bottom: 24px;
    }

    .gst-info-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: var(--gold-dim);
      border: 1px solid rgba(201,168,76,0.3);
      color: var(--gold-light);
      padding: 8px 20px;
      border-radius: 50px;
      font-size: 0.78rem;
      font-weight: 500;
      letter-spacing: 0.05em;
      position: relative;
      z-index: 1;
    }

    /* Customer Card */
    .customer-card {
      background: var(--surface);
      border-radius: var(--radius-xl);
      padding: 36px;
      margin-bottom: 40px;
      border: 1px solid var(--rim-strong);
      box-shadow: 0 8px 40px rgba(0,0,0,0.3);
    }

    .section-label {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 28px;
    }

    .section-label-icon {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      background: var(--gold-dim);
      border: 1px solid rgba(201,168,76,0.3);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .section-label-icon i { color: var(--gold); font-size: 0.85rem; }

    .section-label h5 {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 600;
      font-size: 1.3rem;
      color: var(--text-primary);
      margin: 0;
      letter-spacing: 0.03em;
    }

    .form-label {
      font-size: 0.75rem;
      font-weight: 500;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--text-secondary);
      margin-bottom: 8px;
    }

    .form-control {
      background: var(--surface-2);
      border: 1px solid var(--rim-strong);
      border-radius: var(--radius-md);
      color: var(--text-primary);
      padding: 14px 18px;
      font-family: 'DM Sans', sans-serif;
      font-size: 0.95rem;
      transition: border-color 0.25s, box-shadow 0.25s;
      height: auto;
    }

    .form-control::placeholder { color: var(--text-muted); }

    .form-control:focus {
      background: var(--surface-2);
      color: var(--text-primary);
      border-color: rgba(201,168,76,0.5);
      box-shadow: 0 0 0 3px rgba(201,168,76,0.1);
      outline: none;
    }

    /* Search & Filter */
    .controls-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      margin-bottom: 32px;
      flex-wrap: wrap;
    }

    .search-container {
      position: relative;
      flex: 1;
      max-width: 380px;
      min-width: 200px;
    }

    .search-container i {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      z-index: 10;
      font-size: 0.85rem;
    }

    .search-container input {
      padding-left: 48px;
      border-radius: 50px;
      height: 50px;
    }

    .filter-container {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }

    .filter-btn {
      padding: 10px 22px;
      border-radius: 50px;
      border: 1px solid var(--rim-strong);
      background: var(--surface-2);
      color: var(--text-secondary);
      font-weight: 500;
      font-size: 0.85rem;
      transition: all 0.25s;
      display: flex;
      align-items: center;
      gap: 7px;
      cursor: pointer;
      letter-spacing: 0.02em;
    }

    .filter-btn:hover { border-color: rgba(201,168,76,0.4); color: var(--gold-light); }

    .filter-btn.active {
      background: linear-gradient(to right, #FF6A00, #FF8C42);
      color: white;
      border-color: transparent;
      font-weight: 600;
      box-shadow: 0 4px 16px rgba(255,106,0,0.3);
    }

    /* Category Tabs */
    .category-tabs-wrapper {
      position: relative;
      margin-bottom: 36px;
    }

    .category-tabs {
      border: none;
      display: flex;
      gap: 4px;
      overflow-x: auto;
      padding-bottom: 0;
      -ms-overflow-style: none;
      scrollbar-width: none;
    }

    .category-tabs::-webkit-scrollbar { display: none; }

    .category-tabs .nav-item { flex-shrink: 0; }

    .category-tabs .nav-link {
      border: 1px solid var(--rim);
      background: var(--surface-2);
      color: var(--text-secondary);
      font-weight: 500;
      font-size: 0.85rem;
      padding: 10px 22px;
      border-radius: 50px;
      transition: all 0.25s;
      letter-spacing: 0.03em;
      white-space: nowrap;
    }

    .category-tabs .nav-link:hover {
      color: var(--gold-light);
      border-color: rgba(201,168,76,0.35);
      background: var(--surface-3);
    }

    .category-tabs .nav-link.active {
      background: var(--gold-dim);
      border-color: rgba(201,168,76,0.45);
      color: var(--gold-light);
      font-weight: 600;
      box-shadow: 0 0 20px rgba(201,168,76,0.1);
    }

    .tabs-fade-line {
      height: 1px;
      background: linear-gradient(90deg, transparent, var(--rim-strong), transparent);
      margin-top: 16px;
    }

    /* Food Cards */
    .food-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      align-items: start;
    }

    @media (max-width: 1200px) { .food-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 900px)  { .food-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 540px)  { .food-grid { grid-template-columns: 1fr; } }

    .food-card-wrapper { display: contents; }

    .food-card {
      background: var(--surface);
      border-radius: var(--radius-lg);
      overflow: hidden;
      border: 1px solid var(--rim);
      transition: transform 0.35s cubic-bezier(.22,.68,0,1.2), box-shadow 0.35s ease, border-color 0.25s;
      display: flex;
      flex-direction: column;
      position: relative;
      animation: fadeUp 0.5s ease both;
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0); }
    }

    .food-card:hover {
      transform: translateY(-6px) scale(1.01);
      box-shadow: 0 20px 50px rgba(0,0,0,0.5), 0 0 0 1px rgba(201,168,76,0.2);
      border-color: rgba(201,168,76,0.25);
    }

    .food-image-container {
      height: 200px;
      overflow: hidden;
      position: relative;
      background: var(--surface-2);
    }

    .food-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.6s ease;
      filter: brightness(0.85) saturate(0.9);
    }

    .food-card:hover .food-image {
      transform: scale(1.07);
      filter: brightness(0.95) saturate(1.0);
    }

    .food-image-container::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 60%;
      background: linear-gradient(to top, rgba(10,10,11,0.8) 0%, transparent 100%);
      pointer-events: none;
    }

    .food-badge {
      position: absolute;
      top: 12px;
      right: 12px;
      width: 28px;
      height: 28px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      z-index: 2;
      border: 1.5px solid rgba(255,255,255,0.2);
    }

    .veg-badge { background: rgba(16,185,129,0.85); color: white; }
    .nonveg-badge { background: rgba(239,68,68,0.85); color: white; }

    .discount-badge {
      position: absolute;
      top: 12px;
      left: 12px;
      background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
      color: var(--obsidian);
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 0.7rem;
      font-weight: 700;
      z-index: 2;
      letter-spacing: 0.04em;
    }

    .food-details {
      padding: 20px;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
    }

    .food-title {
      font-family: 'Cormorant Garamond', serif;
      font-weight: 600;
      font-size: 1.15rem;
      margin-bottom: 6px;
      color: var(--text-primary);
      letter-spacing: 0.01em;
      line-height: 1.3;
    }

    .food-description {
      color: var(--text-muted);
      font-size: 0.8rem;
      margin-bottom: 18px;
      flex-grow: 1;
      line-height: 1.55;
      font-weight: 300;
    }

    .food-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
    }

    .food-price {
      font-weight: 600;
      font-size: 1.2rem;
      color: var(--gold-light);
      letter-spacing: 0.01em;
    }

    .food-price del {
      font-size: 0.82rem;
      color: var(--text-muted);
      font-weight: 400;
      display: block;
      line-height: 1;
      margin-bottom: 2px;
    }

    .food-price .discounted-price { color: var(--gold-light); }

    .gst-hint {
      font-size: 0.7rem;
      color: var(--text-muted);
      letter-spacing: 0.04em;
      margin-top: 3px;
    }

    /* Add to Cart Button */
    .add-to-cart-btn {
      background: linear-gradient(to right, #FF6A00, #FF8C42);
      color: white;
      border: none;
      border-radius: var(--radius-sm);
      padding: 10px 18px;
      font-weight: 600;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 7px;
      transition: all 0.25s;
      font-size: 0.82rem;
      letter-spacing: 0.04em;
      white-space: nowrap;
      cursor: pointer;
      flex-shrink: 0;
    }

    .add-to-cart-btn:hover {
      background: linear-gradient(to right, #FF8C42, #FF6A00);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255,106,0,0.35);
    }

    .add-to-cart-btn.added {
      background: linear-gradient(to right, #10b981, #059669);
      box-shadow: 0 6px 20px rgba(16,185,129,0.3);
    }

    /* Order Summary - RESPONSIVE TABLE */
    .order-summary-card {
      background: var(--surface);
      border-radius: var(--radius-xl);
      padding: 36px;
      margin-top: 48px;
      border: 1px solid var(--rim-strong);
      box-shadow: 0 8px 40px rgba(0,0,0,0.3);
    }

    .order-table-wrapper {
      border-radius: var(--radius-md);
      overflow-x: auto;
      overflow-y: visible;
      -webkit-overflow-scrolling: touch;
    }

    .order-table {
      width: 100%;
      min-width: 700px;
      border-collapse: collapse;
    }

    .order-table thead th {
      background: var(--surface-2);
      color: var(--text-muted);
      font-weight: 500;
      font-size: 0.72rem;
      padding: 14px 12px;
      border-bottom: 1px solid var(--rim);
      text-transform: uppercase;
      letter-spacing: 0.1em;
      white-space: nowrap;
    }

    .order-table tbody td {
      padding: 16px 12px;
      border-bottom: 1px solid var(--rim);
      vertical-align: middle;
      font-size: 0.85rem;
      color: var(--text-secondary);
      white-space: nowrap;
    }

    .order-table tbody tr:last-child td { border-bottom: none; }

    .order-table tbody tr { transition: background 0.2s; }
    .order-table tbody tr:hover { background: var(--surface-2); }

    /* Make first column (item name) wrap on mobile */
    .item-name-cell {
      min-width: 160px;
    }
    .item-name-cell strong {
      display: block;
      font-family: 'Cormorant Garamond', serif;
      font-size: 1rem;
      font-weight: 600;
      color: var(--text-primary);
      letter-spacing: 0.01em;
      white-space: normal;
      word-break: break-word;
    }

    .quantity-controls {
      display: flex;
      align-items: center;
      gap: 6px;
      white-space: nowrap;
    }

    .qty-btn {
      width: 28px;
      height: 28px;
      border-radius: var(--radius-sm);
      border: 1px solid var(--rim-strong);
      background: var(--surface-3);
      color: var(--text-secondary);
      font-weight: 600;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
      cursor: pointer;
      line-height: 1;
    }

    .qty-btn:hover {
      background: var(--gold-dim);
      border-color: rgba(201,168,76,0.4);
      color: var(--gold-light);
    }

    .qty-value {
      min-width: 24px;
      text-align: center;
      font-weight: 600;
      color: var(--text-primary);
    }

    .remove-btn {
      background: rgba(239,68,68,0.1);
      color: var(--danger);
      border: 1px solid rgba(239,68,68,0.2);
      border-radius: var(--radius-sm);
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s;
      cursor: pointer;
      font-size: 0.7rem;
    }

    .remove-btn:hover {
      background: rgba(239,68,68,0.25);
      border-color: rgba(239,68,68,0.5);
      transform: scale(1.05);
    }

    /* Totals */
    .totals-section {
      background: var(--surface-2);
      border-radius: var(--radius-lg);
      padding: 28px 32px;
      margin-top: 28px;
      border: 1px solid var(--rim);
    }

    .total-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 11px 0;
      border-bottom: 1px solid var(--rim);
      font-size: 0.9rem;
      color: var(--text-secondary);
    }

    .total-row:last-child { border-bottom: none; }

    .total-row.final {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--gold-light);
      padding-top: 18px;
      margin-top: 4px;
    }

    .total-row.final .total-label { color: var(--text-primary); }

    .discount-value { color: var(--success) !important; }

    /* Empty state */
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: var(--text-muted);
    }

    .empty-state-icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: var(--surface-2);
      border: 1px solid var(--rim-strong);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 20px;
      font-size: 1.8rem;
      color: var(--text-muted);
    }

    .empty-state h5 {
      font-family: 'Cormorant Garamond', serif;
      font-size: 1.4rem;
      color: var(--text-secondary);
      margin-bottom: 8px;
    }

    .empty-state p {
      font-size: 0.85rem;
      font-weight: 300;
      color: var(--text-muted);
    }

    /* Place Order Button */
    .order-action-area {
      text-align: center;
      padding-top: 48px;
    }

    .place-order-btn {
      background: linear-gradient(to right, #FF6A00, #FF8C42);
      color: white;
      border: none;
      border-radius: 50px;
      padding: 20px 64px;
      font-size: 1rem;
      font-weight: 700;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      display: inline-flex;
      align-items: center;
      gap: 12px;
      transition: all 0.3s ease;
      box-shadow: 0 12px 40px rgba(255,106,0,0.35);
      cursor: pointer;
    }

    .place-order-btn:hover {
      background: linear-gradient(to right, #FF8C42, #FF6A00);
      transform: translateY(-4px);
      box-shadow: 0 20px 60px rgba(255,106,0,0.45);
    }

    .place-order-btn:active { transform: translateY(-1px); }

    .place-order-btn:disabled {
      opacity: 0.4;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .restaurant-header { padding: 40px 24px 36px; }
      .customer-card, .order-summary-card { padding: 24px; }
      .totals-section { padding: 20px; }
      .place-order-btn { padding: 16px 40px; font-size: 0.9rem; }
      .controls-bar { flex-direction: column; align-items: stretch; }
      .search-container { max-width: 100%; }
      .filter-container { justify-content: flex-start; }
      
      /* Make table cells less padding on mobile */
      .order-table thead th { padding: 10px 8px; font-size: 0.65rem; }
      .order-table tbody td { padding: 12px 8px; font-size: 0.8rem; }
      .item-name-cell { min-width: 120px; }
    }

    /* Small mobile optimization */
    @media (max-width: 480px) {
      .order-table thead th { padding: 8px 6px; font-size: 0.6rem; }
      .order-table tbody td { padding: 10px 6px; font-size: 0.75rem; }
      .qty-btn { width: 24px; height: 24px; font-size: 0.8rem; }
      .remove-btn { width: 26px; height: 26px; }
      .total-row.final { font-size: 1.2rem; }
      .place-order-btn { padding: 12px 30px; font-size: 0.85rem; }
    }

    /* Custom scrollbar */
    .order-table-wrapper::-webkit-scrollbar {
      height: 6px;
    }
    .order-table-wrapper::-webkit-scrollbar-track {
      background: var(--surface-2);
      border-radius: 3px;
    }
    .order-table-wrapper::-webkit-scrollbar-thumb {
      background: var(--surface-3);
      border-radius: 3px;
    }
    .order-table-wrapper::-webkit-scrollbar-thumb:hover {
      background: rgba(255,106,0,0.5);
    }

    /* Stagger animation for food cards */
    .food-grid .food-card:nth-child(1) { animation-delay: 0.05s; }
    .food-grid .food-card:nth-child(2) { animation-delay: 0.10s; }
    .food-grid .food-card:nth-child(3) { animation-delay: 0.15s; }
    .food-grid .food-card:nth-child(4) { animation-delay: 0.20s; }
    .food-grid .food-card:nth-child(n+5) { animation-delay: 0.25s; }
  </style>
</head>

<body>
<div class="container">

  <!-- Restaurant Header -->
  <div class="restaurant-header">
    <div class="header-corner tl"></div>
    <div class="header-corner tr"></div>
    <div class="header-corner bl"></div>
    <div class="header-corner br"></div>

    <div class="header-icon-ring">
      <i class="fas fa-utensils"></i>
    </div>
    <h1>{{ $restaurant_details->name ?? 'Premium Dining' }}</h1>
    <p class="header-tagline">Curated dishes from our premium menu</p>

    @if($restaurant_details->gstin)
    <div class="gst-info-badge">
      <i class="fas fa-file-invoice-dollar"></i>
      GST Bill &nbsp;·&nbsp; GSTIN: {{ $restaurant_details->gstin }} &nbsp;·&nbsp; GST: {{ $restaurant_details->gst_percentage ?? 0 }}%
    </div>
    @else
    <div class="gst-info-badge">
      <i class="fas fa-receipt"></i> Non-GST Bill
    </div>
    @endif
  </div>

  <!-- Customer Details -->
  <div class="customer-card">
    <div class="section-label">
      <div class="section-label-icon"><i class="fas fa-user"></i></div>
      <h5>Guest Details</h5>
    </div>
    <div class="row">
      <div class="col-lg-6 mb-3">
        <label class="form-label">Your Name <span style="color:var(--gold)">*</span></label>
        <input type="text" id="customer_name" class="form-control" placeholder="Enter your full name">
      </div>
      <div class="col-lg-6 mb-3">
        <label class="form-label">Phone Number <span style="color:var(--gold)">*</span></label>
        <input type="text" id="phone" class="form-control" placeholder="Enter your contact number">
      </div>
    </div>
    <input type="hidden" id="table_id" value="{{ $table_id }}">
    <input type="hidden" id="restaurant_id" value="{{ $restaurant_id }}">
    <input type="hidden" id="is_gst_registered" value="{{ $restaurant_details->gstin ? 'true' : 'false' }}">
    <input type="hidden" id="gst_percentage" value="{{ $restaurant_details->gst_percentage ?? 0 }}">
  </div>

  <!-- Search & Filter -->
  <div class="controls-bar">
    <div class="search-container">
      <i class="fas fa-search"></i>
      <input type="text" id="searchBox" class="form-control" placeholder="Search dishes…">
    </div>
    <div class="filter-container">
      <button class="filter-btn active" data-type="">All Items</button>
      <button class="filter-btn" data-type="veg"><i class="fas fa-leaf"></i> Veg</button>
      <button class="filter-btn" data-type="non-veg"><i class="fas fa-drumstick-bite"></i> Non-Veg</button>
    </div>
  </div>

  <!-- Category Tabs -->
  <div class="category-tabs-wrapper">
    <ul class="nav category-tabs" role="tablist">
      @foreach($categories as $key => $cat)
      <li class="nav-item">
        <a class="nav-link {{ $key==0?'active':'' }}" data-toggle="tab" href="#cat{{ $cat->id }}">
          {{ $cat->name }}
        </a>
      </li>
      @endforeach
    </ul>
    <div class="tabs-fade-line"></div>
  </div>

  <!-- Category Content -->
  <div class="tab-content">
    @foreach($categories as $key => $cat)
    <div class="tab-pane fade {{ $key==0?'show active':'' }}" id="cat{{ $cat->id }}">
      <div class="food-grid">
        @foreach($cat->subcategories as $item)
        <div class="food-card-wrapper"
             data-name="{{ strtolower($item->name) }}"
             data-type="{{ strtolower($item->food_type) }}">
          <div class="food-card">
            <div class="food-image-container">
              @if($item->image)
                <img src="{{ URL::to('storage/app/public/category') }}/{{ $item->image }}"
                     alt="{{ $item->name }}" class="food-image"
                     onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop'">
              @else
                <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop"
                     alt="{{ $item->name }}" class="food-image">
              @endif

              <div class="food-badge {{ strtolower($item->food_type) == 'veg' ? 'veg-badge' : 'nonveg-badge' }}">
                <i class="fas {{ strtolower($item->food_type) == 'veg' ? 'fa-leaf' : 'fa-drumstick-bite' }}"></i>
              </div>
              @if(($item->discount_percentage ?? 0) > 0)
              <div class="discount-badge">{{ $item->discount_percentage }}% OFF</div>
              @endif
            </div>

            <div class="food-details">
              <h5 class="food-title">{{ $item->name }}</h5>
              <p class="food-description">{{ $item->description ?? 'Delicious dish prepared with premium ingredients.' }}</p>

              <div class="food-footer">
                <div>
                  <div class="food-price">
                    @if(($item->discount_percentage ?? 0) > 0)
                      <del>₹{{ number_format($item->price, 2) }}</del>
                      <span class="discounted-price">₹{{ number_format($item->price - ($item->price * $item->discount_percentage / 100), 2) }}</span>
                    @else
                      ₹{{ number_format($item->price, 2) }}
                    @endif
                  </div>
                  @if($restaurant_details->gstin)
                  <div class="gst-hint">+ {{ $restaurant_details->gst_percentage ?? 0 }}% GST</div>
                  @endif
                </div>
                <button class="add-to-cart-btn addItemBtn"
                        data-id="{{ $item->id }}"
                        data-name="{{ $item->name }}"
                        data-price="{{ $item->price }}"
                        data-discount="{{ $item->discount_percentage ?? 0 }}">
                  <i class="fas fa-plus"></i> Add
                </button>
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
    @endforeach
  </div>

  <!-- Order Summary -->
  <div class="order-summary-card">
    <div class="section-label mb-4">
      <div class="section-label-icon"><i class="fas fa-receipt"></i></div>
      <h5>Your Order</h5>
    </div>

    <div id="orderItemsContainer">
      <div class="order-table-wrapper table-responsive" style="display:none;">
        <table class="order-table">
          <thead>
            <tr>
              <th>Item</th>
              <th>Qty</th>
              <th>Unit Price</th>
              <th>Discount</th>
              <th>Taxable</th>
              @if($restaurant_details->gstin)
              <th>GST</th>
              @endif
              <th>Total</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="orderItemsBody"></tbody>
        </table>
      </div>

      <div id="emptyOrderState" class="empty-state" style="display: block;">
        <div class="empty-state-icon">
          <i class="fas fa-utensils"></i>
        </div>
        <h5>Your table is empty</h5>
        <p>Browse our menu and add your favourite dishes to begin</p>
      </div>
    </div>

    <div class="totals-section">
      <div class="total-row">
        <span>Original Subtotal</span>
        <span>₹<span id="original_subtotal">0.00</span></span>
      </div>
      <div class="total-row">
        <span>Item Discount</span>
        <span class="discount-value">− ₹<span id="item_discount">0.00</span></span>
      </div>
      <div class="total-row">
        <span>Taxable Amount</span>
        <span>₹<span id="taxable_amount">0.00</span></span>
      </div>
      @if($restaurant_details->gstin)
      <div class="total-row">
        <span>GST ({{ $restaurant_details->gst_percentage ?? 0 }}%)</span>
        <span>₹<span id="gst_amount">0.00</span></span>
      </div>
      @endif
      <div class="total-row final">
        <span class="total-label">Grand Total</span>
        <span>₹<span id="final_total">0.00</span></span>
      </div>
    </div>
  </div>

  <!-- Place Order -->
  <div class="order-action-area">
    <button class="place-order-btn" id="placeOrderBtn">
      <i class="fas fa-paper-plane"></i> Confirm Order
    </button>
  </div>

</div><!-- /container -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let cart = [];
let isGstRegistered = $('#is_gst_registered').val() === 'true';
let gstPercentage = parseFloat($('#gst_percentage').val());

function calculateItemDetails(originalPrice, qty, discountPercent = 0) {
    let discountedPrice = originalPrice - (originalPrice * discountPercent / 100);
    let taxableAmount = discountedPrice * qty;
    let gstAmount = isGstRegistered ? (taxableAmount * gstPercentage) / 100 : 0;
    let totalAmount = taxableAmount + gstAmount;
    return {
        discountedPrice: discountedPrice,
        taxableAmount: taxableAmount,
        gstAmount: gstAmount,
        totalAmount: totalAmount,
        discountAmount: (originalPrice * qty) - taxableAmount
    };
}

function updateEmptyState() {
    if (cart.length === 0) {
        $('#emptyOrderState').show();
        $('.order-table-wrapper').hide();
        $('#placeOrderBtn').prop('disabled', true);
    } else {
        $('#emptyOrderState').hide();
        $('.order-table-wrapper').show();
        $('#placeOrderBtn').prop('disabled', false);
    }
}

function refreshTable() {
    let tbody = $('#orderItemsBody');
    tbody.html('');
    let originalSubtotal = 0, totalTaxable = 0, totalGst = 0, totalDiscount = 0;

    cart.forEach((item, i) => {
        let details = calculateItemDetails(item.price, item.qty, item.discount);
        originalSubtotal += item.price * item.qty;
        totalTaxable    += details.taxableAmount;
        totalGst        += details.gstAmount;
        totalDiscount   += details.discountAmount;

        let row = `
            <tr>
                <td class="item-name-cell">
                    <strong>${item.name}</strong>
                </td>
                <td>
                    <div class="quantity-controls">
                        <button class="qty-btn decreaseQty" data-index="${i}">−</button>
                        <span class="qty-value">${item.qty}</span>
                        <button class="qty-btn increaseQty" data-index="${i}">+</button>
                    </div>
                </td>
                <td>
                    ${item.discount > 0 ? `<del style="display:block;color:var(--text-muted);font-size:0.78rem;">₹${item.price.toFixed(2)}</del>` : ''}
                    ₹${details.discountedPrice.toFixed(2)}
                </td>
                <td style="color:var(--success)">
                    ${item.discount > 0 ? `− ₹${details.discountAmount.toFixed(2)}` : '<span style="color:var(--text-muted)">—</span>'}
                </td>
                <td>₹${details.taxableAmount.toFixed(2)}</td>`;

        if (isGstRegistered) {
            row += `<td>₹${details.gstAmount.toFixed(2)}</td>`;
        }

        row += `<td style="color:var(--gold-light);font-weight:600;">₹${details.totalAmount.toFixed(2)}</td>
                <td>
                    <button class="remove-btn removeItem" data-index="${i}" title="Remove">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>`;
        tbody.append(row);
    });

    $('#original_subtotal').text(originalSubtotal.toFixed(2));
    $('#item_discount').text(totalDiscount.toFixed(2));
    $('#taxable_amount').text(totalTaxable.toFixed(2));
    if (isGstRegistered) $('#gst_amount').text(totalGst.toFixed(2));
    $('#final_total').text((totalTaxable + totalGst).toFixed(2));
    updateEmptyState();
}

/* Add to cart */
$(document).on('click', '.addItemBtn', function() {
    let itemId   = $(this).data('id');
    let existing = cart.find(i => i.id === itemId);
    if (existing) {
        existing.qty++;
    } else {
        cart.push({
            id:       itemId,
            name:     $(this).data('name'),
            price:    parseFloat($(this).data('price')),
            qty:      1,
            discount: parseFloat($(this).data('discount')) || 0
        });
    }
    let btn = $(this);
    btn.addClass('added').html('<i class="fas fa-check"></i> Added');
    setTimeout(() => { btn.removeClass('added').html('<i class="fas fa-plus"></i> Add'); }, 1000);
    refreshTable();
});

/* Qty controls */
$(document).on('click', '.increaseQty', function() {
    cart[$(this).data('index')].qty++;
    refreshTable();
});
$(document).on('click', '.decreaseQty', function() {
    let idx = $(this).data('index');
    if (cart[idx].qty > 1) { cart[idx].qty--; refreshTable(); }
});
$(document).on('click', '.removeItem', function() {
    cart.splice($(this).data('index'), 1);
    refreshTable();
});

/* Search */
$('#searchBox').on('input', function() {
    let val = $(this).val().toLowerCase();
    $('.food-card-wrapper').each(function() {
        $(this).toggle($(this).data('name').includes(val));
    });
});

/* Filter */
$('.filter-btn').click(function() {
    $('.filter-btn').removeClass('active');
    $(this).addClass('active');
    let type = $(this).data('type');
    $('.food-card-wrapper').each(function() {
        $(this).toggle(type === '' || $(this).data('type') === type);
    });
});

/* Place Order */
$('#placeOrderBtn').click(function() {
    if (cart.length === 0) { alert('Please add items to your order'); return; }
    let name  = $('#customer_name').val().trim();
    let phone = $('#phone').val().trim();
    if (!name)  { alert('Please enter your name'); $('#customer_name').focus(); return; }
    if (!phone) { alert('Please enter your phone number'); $('#phone').focus(); return; }

    let orderItems = cart.map(item => ({
        id: item.id, name: item.name, price: item.price, qty: item.qty, item_discount: item.discount
    }));

    $(this).html('<i class="fas fa-spinner fa-spin"></i> Processing…').prop('disabled', true);

    $.post("{{ route('temp.order.store') }}", {
        _token:          "{{ csrf_token() }}",
        customer_name:   name,
        customer_phone:  phone,
        table_id:        $('#table_id').val(),
        restaurant_id:   $('#restaurant_id').val(),
        order_items:     orderItems
    }, function(res) {
        if (res.status) {
            window.location.href = res.redirect;
        } else {
            alert('Something went wrong. Please try again.');
            $('#placeOrderBtn').html('<i class="fas fa-paper-plane"></i> Confirm Order').prop('disabled', false);
        }
    }).fail(function() {
        alert('Network error. Please check your connection and try again.');
        $('#placeOrderBtn').html('<i class="fas fa-paper-plane"></i> Confirm Order').prop('disabled', false);
    });
});

$(document).ready(function() { updateEmptyState(); });
</script>
</body>
</html>