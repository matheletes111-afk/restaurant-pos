<!DOCTYPE html>
<html lang="en">
<head>
  <title>Digital Menu | {{ $restaurant_details->name ?? 'Premium Dining' }}</title>
  <link rel="shortcut icon" href="{{ asset('fav_web.png') }}">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,400;1,600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root {
      --primary: #ff5e14;
      --primary-hover: #e04b08;
      --primary-light: rgba(255, 94, 20, 0.08);
      --primary-glow: rgba(255, 94, 20, 0.25);
      --gradient-primary: linear-gradient(135deg, #ff5e14 0%, #ff8c42 100%);
      --gradient-gold: linear-gradient(135deg, #d97706 0%, #f59e0b 100%);
      --surface: #ffffff;
      --surface-2: #f8fafc;
      --surface-3: #f1f5f9;
      --surface-card: rgba(255, 255, 255, 0.95);
      --text-main: #0f172a;
      --text-body: #334155;
      --text-muted: #64748b;
      --text-light: #94a3b8;
      --border: #e2e8f0;
      --border-subtle: rgba(226, 232, 240, 0.8);
      --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.04);
      --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04), 0 1px 2px rgba(0, 0, 0, 0.02);
      --shadow-md: 0 8px 24px rgba(15, 23, 42, 0.06), 0 2px 6px rgba(15, 23, 42, 0.04);
      --shadow-lg: 0 20px 40px rgba(15, 23, 42, 0.08), 0 6px 12px rgba(15, 23, 42, 0.04);
      --shadow-glow: 0 10px 25px rgba(255, 94, 20, 0.35);
      --radius-sm: 10px;
      --radius-md: 18px;
      --radius-lg: 28px;
      --radius-full: 9999px;
      --veg-color: #15803d;
      --nonveg-color: #b91c1c;
      --success: #10b981;
      --danger: #ef4444;
    }

    * {
      box-sizing: border-box;
      -webkit-tap-highlight-color: transparent;
    }

    body {
      background-color: #f8fafc;
      background-image:
          radial-gradient(ellipse 90% 60% at 50% -15%, rgba(255, 94, 20, 0.08) 0%, transparent 70%),
          radial-gradient(ellipse 70% 50% at 85% 95%, rgba(255, 140, 66, 0.04) 0%, transparent 60%),
          radial-gradient(circle at 10% 40%, rgba(245, 158, 11, 0.03) 0%, transparent 40%);
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      color: var(--text-body);
      min-height: 100vh;
      padding-bottom: 140px;
      -webkit-font-smoothing: antialiased;
      line-height: 1.5;
    }

    .serif-title {
      font-family: 'Playfair Display', Georgia, serif;
    }

    .main-container {
      max-width: 1240px;
      margin: 0 auto;
      padding: 24px 20px;
      position: relative;
      z-index: 1;
    }

    /* Top Sticky Brand Bar */
    .top-brand-bar {
      position: sticky;
      top: 12px;
      z-index: 1050;
      background: rgba(255, 255, 255, 0.88);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(255, 255, 255, 0.9);
      box-shadow: var(--shadow-md);
      border-radius: var(--radius-full);
      padding: 10px 20px;
      margin-bottom: 28px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .brand-logo-wrap {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .brand-logo-wrap img {
      max-height: 36px;
      max-width: 140px;
      object-fit: contain;
      display: block;
    }

    .top-badges-wrap {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .table-live-pill {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      background: #ecfdf5;
      color: #047857;
      border: 1px solid rgba(16, 185, 129, 0.25);
      border-radius: var(--radius-full);
      padding: 6px 14px;
      font-size: 0.85rem;
      font-weight: 700;
      letter-spacing: 0.02em;
    }

    .live-dot {
      width: 8px;
      height: 8px;
      background-color: #10b981;
      border-radius: 50%;
      box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
      animation: pulseGreen 1.8s infinite;
    }

    @keyframes pulseGreen {
      0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
      70% { transform: scale(1); box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
      100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .top-cart-trigger {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      background: var(--gradient-primary);
      color: #ffffff !important;
      border: none;
      border-radius: var(--radius-full);
      padding: 7px 16px;
      font-size: 0.88rem;
      font-weight: 700;
      cursor: pointer;
      box-shadow: 0 4px 14px rgba(255, 94, 20, 0.3);
      transition: all 0.2s ease;
      text-decoration: none !important;
    }

    .top-cart-trigger:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(255, 94, 20, 0.4);
    }

    .cart-count-chip {
      background: #ffffff;
      color: var(--primary);
      border-radius: 50%;
      min-width: 22px;
      height: 22px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.75rem;
      font-weight: 800;
      padding: 0 5px;
    }

    /* Hero Restaurant Header */
    .restaurant-hero-card {
      background: #ffffff;
      border-radius: var(--radius-lg);
      padding: 44px 32px;
      text-align: center;
      margin-bottom: 32px;
      box-shadow: var(--shadow-md);
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(226, 232, 240, 0.8);
    }

    .restaurant-hero-card::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0;
      height: 6px;
      background: var(--gradient-primary);
    }

    .hero-glow-bg {
      position: absolute;
      top: -100px;
      left: 50%;
      transform: translateX(-50%);
      width: 500px;
      height: 250px;
      background: radial-gradient(circle, rgba(255, 94, 20, 0.08) 0%, rgba(255, 255, 255, 0) 70%);
      pointer-events: none;
    }

    .restaurant-avatar-wrap {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #ffffff;
      padding: 10px 24px;
      border-radius: 20px;
      border: 1px solid var(--border);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
      margin-bottom: 18px;
      max-width: 260px;
      min-height: 84px;
      position: relative;
      z-index: 2;
    }

    .restaurant-avatar-wrap img {
      max-height: 64px;
      max-width: 210px;
      object-fit: contain;
      display: block;
    }

    .restaurant-icon-circle {
      width: 76px;
      height: 76px;
      border-radius: 50%;
      background: var(--primary-light);
      color: var(--primary);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      margin-bottom: 18px;
      border: 2px solid rgba(255, 94, 20, 0.2);
    }

    .restaurant-title {
      font-size: 2.6rem;
      font-weight: 700;
      color: var(--text-main);
      letter-spacing: -0.02em;
      margin-bottom: 8px;
      line-height: 1.2;
    }

    .restaurant-tagline {
      font-size: 1rem;
      color: var(--text-muted);
      font-weight: 500;
      margin-bottom: 20px;
    }

    .meta-badges-row {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin-top: 16px;
    }

    .meta-pill {
      display: inline-flex;
      align-items: center;
      gap: 7px;
      background: var(--surface-2);
      border: 1px solid var(--border);
      padding: 7px 16px;
      border-radius: var(--radius-full);
      font-size: 0.84rem;
      font-weight: 600;
      color: var(--text-muted);
    }

    .meta-pill i {
      color: var(--primary);
    }

    /* Guest Information Card */
    .guest-info-card {
      background: #ffffff;
      border-radius: var(--radius-md);
      padding: 28px 30px;
      margin-bottom: 32px;
      box-shadow: var(--shadow-sm);
      border: 1px solid var(--border);
      position: relative;
    }

    .card-heading-clean {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
    }

    .card-heading-icon {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      background: var(--primary-light);
      color: var(--primary);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 1.1rem;
    }

    .card-heading-clean h4 {
      font-size: 1.15rem;
      font-weight: 700;
      color: var(--text-main);
      margin: 0;
    }

    .input-icon-group {
      position: relative;
    }

    .input-icon-group i {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-light);
      font-size: 1rem;
      pointer-events: none;
      transition: color 0.2s;
    }

    .form-input-premium {
      width: 100%;
      height: 50px;
      padding: 12px 18px 12px 48px;
      background: #fdfdfd;
      border: 1.5px solid var(--border);
      border-radius: var(--radius-sm);
      font-size: 0.96rem;
      font-weight: 500;
      color: var(--text-main);
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .form-input-premium:focus {
      outline: none;
      background: #ffffff;
      border-color: var(--primary);
      box-shadow: 0 0 0 4px var(--primary-light);
    }

    .form-input-premium:focus + i {
      color: var(--primary);
    }

    .input-label-clean {
      font-size: 0.82rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: var(--text-muted);
      margin-bottom: 8px;
      display: block;
    }

    /* Controls Bar: Search & Veg/Non-Veg */
    .menu-controls-wrapper {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      margin-bottom: 24px;
    }

    .search-box-pill {
      position: relative;
      flex: 1;
      min-width: 280px;
      max-width: 460px;
    }

    .search-box-pill input {
      width: 100%;
      height: 48px;
      padding: 10px 42px 10px 46px;
      background: #ffffff;
      border: 1.5px solid var(--border);
      border-radius: var(--radius-full);
      font-size: 0.94rem;
      font-weight: 500;
      color: var(--text-main);
      box-shadow: var(--shadow-xs);
      transition: all 0.2s ease;
    }

    .search-box-pill input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 4px var(--primary-light);
    }

    .search-icon-left {
      position: absolute;
      left: 18px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--text-muted);
      font-size: 0.95rem;
    }

    .search-clear-btn {
      position: absolute;
      right: 14px;
      top: 50%;
      transform: translateY(-50%);
      background: transparent;
      border: none;
      color: var(--text-muted);
      font-size: 0.9rem;
      cursor: pointer;
      display: none;
      padding: 4px;
    }

    .dietary-filters-group {
      display: inline-flex;
      background: #ffffff;
      padding: 5px;
      border-radius: var(--radius-full);
      border: 1px solid var(--border);
      box-shadow: var(--shadow-xs);
      gap: 6px;
    }

    .diet-btn {
      border: none;
      background: transparent;
      padding: 8px 18px;
      border-radius: var(--radius-full);
      font-size: 0.88rem;
      font-weight: 600;
      color: var(--text-muted);
      cursor: pointer;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      display: flex;
      align-items: center;
      gap: 7px;
    }

    .diet-btn:hover {
      color: var(--text-main);
      background: var(--surface-3);
    }

    .diet-btn.active {
      background: var(--text-main);
      color: #ffffff;
      box-shadow: 0 2px 8px rgba(15, 23, 42, 0.15);
    }

    .diet-btn[data-type="veg"].active {
      background: var(--veg-color);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
    }

    .diet-btn[data-type="non-veg"].active {
      background: var(--nonveg-color);
      color: #ffffff;
      box-shadow: 0 4px 12px rgba(185, 28, 28, 0.3);
    }

    /* Category Navigation Sticky Tabs */
    .category-nav-bar {
      position: sticky;
      top: 76px;
      z-index: 1040;
      background: rgba(248, 250, 252, 0.92);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      padding: 12px 0 16px 0;
      margin-bottom: 24px;
      border-bottom: 1px solid rgba(226, 232, 240, 0.6);
    }

    .category-pills-scroll {
      display: flex;
      gap: 8px;
      overflow-x: auto;
      scroll-behavior: smooth;
      -ms-overflow-style: none;
      scrollbar-width: none;
      padding: 2px 4px;
    }

    .category-pills-scroll::-webkit-scrollbar {
      display: none;
    }

    .cat-pill-tab {
      background: #ffffff;
      color: var(--text-muted);
      border: 1px solid var(--border);
      border-radius: var(--radius-full);
      padding: 9px 20px;
      font-size: 0.92rem;
      font-weight: 600;
      white-space: nowrap;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      text-decoration: none !important;
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
      box-shadow: var(--shadow-xs);
    }

    .cat-pill-tab:hover {
      background: #ffffff;
      color: var(--text-main);
      border-color: #cbd5e1;
      transform: translateY(-1px);
    }

    .cat-pill-tab.active {
      background: var(--gradient-primary) !important;
      color: #ffffff !important;
      border-color: transparent !important;
      box-shadow: 0 6px 18px rgba(255, 94, 20, 0.32) !important;
    }

    .cat-count-pill {
      background: var(--surface-3);
      color: var(--text-muted);
      font-size: 0.75rem;
      font-weight: 700;
      padding: 2px 8px;
      border-radius: var(--radius-full);
      transition: all 0.2s;
    }

    .cat-pill-tab.active .cat-count-pill {
      background: rgba(255, 255, 255, 0.25);
      color: #ffffff;
    }

    /* Category Section Headers in "All" view */
    .category-section-divider {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 32px 0 20px 0;
      padding-bottom: 12px;
      border-bottom: 2px solid #e2e8f0;
    }

    .category-section-title {
      font-size: 1.4rem;
      font-weight: 800;
      color: var(--text-main);
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .category-section-title::before {
      content: '';
      display: inline-block;
      width: 5px;
      height: 22px;
      background: var(--gradient-primary);
      border-radius: 4px;
    }

    .category-item-count {
      font-size: 0.85rem;
      color: var(--text-muted);
      font-weight: 600;
      background: #ffffff;
      padding: 4px 14px;
      border-radius: var(--radius-full);
      border: 1px solid var(--border);
    }

    /* Food Grid & Cards */
    .food-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 24px;
    }

    .food-card-wrapper {
      display: contents;
    }

    .food-card {
      background: #ffffff;
      border-radius: var(--radius-md);
      overflow: hidden;
      border: 1px solid var(--border);
      box-shadow: var(--shadow-sm);
      display: flex;
      flex-direction: column;
      height: 100%;
      position: relative;
      transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease;
    }

    .food-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-md);
      border-color: rgba(255, 94, 20, 0.3);
    }

    .food-image-frame {
      height: 210px;
      position: relative;
      background: #f1f5f9;
      overflow: hidden;
    }

    .food-image {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
      display: block;
    }

    .food-card:hover .food-image {
      transform: scale(1.06);
    }

    /* Authentic FSSAI Veg / Non-Veg Indicator Badge */
    .fssai-indicator {
      position: absolute;
      top: 14px;
      right: 14px;
      width: 26px;
      height: 26px;
      background: #ffffff;
      border-radius: 6px;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.12);
      z-index: 3;
    }

    .fssai-box {
      width: 18px;
      height: 18px;
      border-radius: 3px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .fssai-veg {
      border: 2px solid var(--veg-color);
    }

    .fssai-veg .fssai-symbol {
      width: 8px;
      height: 8px;
      background-color: var(--veg-color);
      border-radius: 50%;
    }

    .fssai-nonveg {
      border: 2px solid var(--nonveg-color);
    }

    .fssai-nonveg .fssai-symbol {
      width: 0;
      height: 0;
      border-left: 5px solid transparent;
      border-right: 5px solid transparent;
      border-bottom: 9px solid var(--nonveg-color);
    }

    .discount-ribbon {
      position: absolute;
      top: 14px;
      left: 14px;
      background: linear-gradient(135deg, #ef4444 0%, #f97316 100%);
      color: #ffffff;
      font-size: 0.75rem;
      font-weight: 800;
      padding: 4px 12px;
      border-radius: var(--radius-full);
      box-shadow: 0 4px 10px rgba(239, 68, 68, 0.35);
      z-index: 3;
      letter-spacing: 0.03em;
      display: flex;
      align-items: center;
      gap: 4px;
    }

    .food-card-body {
      padding: 22px;
      display: flex;
      flex-direction: column;
      flex-grow: 1;
    }

    .food-item-title {
      font-size: 1.18rem;
      font-weight: 700;
      color: var(--text-main);
      margin-bottom: 8px;
      line-height: 1.35;
    }

    .food-item-desc {
      font-size: 0.88rem;
      color: var(--text-muted);
      line-height: 1.55;
      margin-bottom: 20px;
      flex-grow: 1;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .food-card-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 14px;
      border-top: 1px solid #f1f5f9;
      margin-top: auto;
    }

    .price-block {
      display: flex;
      flex-direction: column;
    }

    .cross-price-label {
      font-size: 0.82rem;
      color: var(--text-light);
      text-decoration: line-through;
      font-weight: 500;
      line-height: 1;
      margin-bottom: 3px;
    }

    .active-price-label {
      font-size: 1.35rem;
      font-weight: 800;
      color: var(--text-main);
      letter-spacing: -0.01em;
      line-height: 1.1;
    }

    .gst-tax-note {
      font-size: 0.72rem;
      color: var(--text-muted);
      font-weight: 600;
      margin-top: 3px;
    }

    /* Add to Tray Interactive Button / Counter */
    .card-stepper-wrap {
      min-width: 105px;
    }

    .btn-add-tray {
      background: var(--surface-2);
      color: var(--primary);
      border: 1.5px solid rgba(255, 94, 20, 0.35);
      border-radius: var(--radius-full);
      padding: 8px 20px;
      font-size: 0.92rem;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      width: 100%;
    }

    .btn-add-tray:hover {
      background: var(--gradient-primary);
      color: #ffffff;
      border-color: transparent;
      box-shadow: 0 4px 14px rgba(255, 94, 20, 0.35);
      transform: translateY(-1px);
    }

    .card-active-counter {
      display: none;
      align-items: center;
      justify-content: space-between;
      background: var(--gradient-primary);
      border-radius: var(--radius-full);
      padding: 4px 6px;
      box-shadow: 0 4px 14px rgba(255, 94, 20, 0.3);
      width: 100%;
    }

    .card-counter-btn {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      border: none;
      background: rgba(255, 255, 255, 0.9);
      color: var(--primary);
      font-weight: 800;
      font-size: 1rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.15s;
    }

    .card-counter-btn:hover {
      background: #ffffff;
      transform: scale(1.1);
    }

    .card-counter-val {
      color: #ffffff;
      font-weight: 800;
      font-size: 0.95rem;
      min-width: 24px;
      text-align: center;
    }

    /* Order Summary Tray Card */
    .order-summary-card {
      background: #ffffff;
      border-radius: var(--radius-lg);
      padding: 36px 32px;
      margin-top: 52px;
      box-shadow: var(--shadow-md);
      border: 1px solid var(--border);
      position: relative;
    }

    .tray-table-container {
      border-radius: var(--radius-md);
      border: 1px solid var(--border);
      overflow-x: auto;
      background: #ffffff;
      margin-bottom: 24px;
    }

    .tray-table {
      width: 100%;
      min-width: 600px;
      border-collapse: collapse;
    }

    .tray-table th {
      background: var(--surface-2);
      color: var(--text-muted);
      font-weight: 700;
      font-size: 0.8rem;
      padding: 16px 20px;
      border-bottom: 1px solid var(--border);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .tray-table td {
      padding: 18px 20px;
      border-bottom: 1px solid #f1f5f9;
      vertical-align: middle;
      font-size: 0.95rem;
    }

    .tray-item-title {
      font-weight: 700;
      color: var(--text-main);
      display: block;
      font-size: 1rem;
      margin-bottom: 2px;
    }

    .stepper-pill {
      display: inline-flex;
      align-items: center;
      background: var(--surface-3);
      border-radius: var(--radius-full);
      padding: 4px;
      border: 1px solid #e2e8f0;
      gap: 6px;
    }

    .step-btn {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      border: none;
      background: #ffffff;
      color: var(--text-main);
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 0.95rem;
      cursor: pointer;
      box-shadow: 0 1px 3px rgba(0,0,0,0.08);
      transition: all 0.2s;
    }

    .step-btn:hover {
      background: var(--primary);
      color: #ffffff;
    }

    .step-value {
      min-width: 26px;
      text-align: center;
      font-weight: 800;
      color: var(--text-main);
      font-size: 0.92rem;
    }

    .tray-del-btn {
      background: transparent;
      border: none;
      color: var(--text-light);
      font-size: 1.1rem;
      cursor: pointer;
      padding: 6px;
      border-radius: 8px;
      transition: color 0.2s, background-color 0.2s;
    }

    .tray-del-btn:hover {
      color: var(--danger);
      background-color: #fee2e2;
    }

    /* Tray Totals Box */
    .tray-totals-box {
      background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
      border-radius: var(--radius-md);
      padding: 28px;
      border: 1px solid var(--border);
    }

    .tray-calc-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
      font-size: 1rem;
      color: var(--text-muted);
      font-weight: 500;
    }

    .tray-calc-row.discount-row {
      color: var(--veg-color);
      font-weight: 600;
    }

    .tray-calc-row.grand-total-row {
      font-size: 1.55rem;
      font-weight: 800;
      color: var(--text-main);
      border-top: 2px dashed #cbd5e1;
      padding-top: 18px;
      margin-top: 12px;
    }

    .grand-total-amount {
      color: var(--primary);
      font-size: 1.7rem;
    }

    /* Empty Tray State */
    .empty-tray-state {
      text-align: center;
      padding: 60px 20px;
    }

    .empty-tray-icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: var(--surface-3);
      color: var(--text-light);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 2.2rem;
      margin-bottom: 20px;
    }

    .empty-tray-state h5 {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--text-main);
      margin-bottom: 8px;
    }

    .empty-tray-state p {
      color: var(--text-muted);
      font-size: 0.95rem;
      max-width: 360px;
      margin: 0 auto;
    }

    /* Fixed Bottom Action Floating Bar */
    .floating-checkout-bar {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(15, 23, 42, 0.94);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      padding: 16px 24px;
      z-index: 1060;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      box-shadow: 0 -8px 30px rgba(0, 0, 0, 0.25);
      display: flex;
      align-items: center;
      justify-content: space-between;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .floating-summary-info {
      display: flex;
      flex-direction: column;
    }

    .floating-items-count {
      color: rgba(255, 255, 255, 0.7);
      font-size: 0.84rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    .floating-grand-total {
      color: #ffffff;
      font-size: 1.45rem;
      font-weight: 800;
      line-height: 1.1;
    }

    .btn-place-order-master {
      background: var(--gradient-primary);
      color: #ffffff;
      border: none;
      border-radius: var(--radius-full);
      padding: 14px 36px;
      font-size: 1.05rem;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      box-shadow: var(--shadow-glow);
      transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .btn-place-order-master:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 12px 28px rgba(255, 94, 20, 0.45);
    }

    .btn-place-order-master:disabled {
      background: #475569;
      color: #94a3b8;
      box-shadow: none;
      cursor: not-allowed;
    }

    /* Empty search state */
    .no-results-state {
      text-align: center;
      padding: 60px 20px;
      background: #ffffff;
      border-radius: var(--radius-md);
      border: 1px dashed var(--border);
      grid-column: 1 / -1;
      display: none;
    }

    @media (max-width: 768px) {
      .restaurant-hero-card { padding: 32px 20px; }
      .restaurant-title { font-size: 2rem; }
      .guest-info-card, .order-summary-card { padding: 22px 18px; }
      .category-nav-bar { top: 68px; }
      .food-grid { grid-template-columns: 1fr; gap: 18px; }
      .floating-checkout-bar { padding: 12px 18px; }
      .btn-place-order-master { padding: 12px 24px; font-size: 0.95rem; }
      .floating-grand-total { font-size: 1.25rem; }
    }
  </style>
</head>
<body>

@php
  $totalDishesCount = $categories->sum(function($c) { return $c->subcategories->count(); });
@endphp

<div class="main-container">

  <!-- Top Floating Brand Navigation -->
  <div class="top-brand-bar">
    <div class="brand-logo-wrap">
      @if(!empty($restaurant_details) && $restaurant_details->hasLogo())
        <img src="{{ $restaurant_details->logo_url }}" alt="{{ $restaurant_details->name }}" onerror="this.onerror=null; this.src='{{ asset('logo.png') }}';">
      @else
        <img src="{{ asset('logo.png') }}" alt="Bill & Bite Logo">
      @endif
    </div>
    
    <div class="top-badges-wrap">
      @if(isset($table_details) && !empty($table_details->name))
        <div class="table-live-pill">
          <span class="live-dot"></span> Table {{ $table_details->name }}
        </div>
      @endif
      <a href="#orderSummaryCard" class="top-cart-trigger">
        <i class="fas fa-shopping-bag"></i>
        <span class="d-none d-sm-inline">Tray</span>
        <span class="cart-count-chip" id="topCartBadge">0</span>
      </a>
    </div>
  </div>

  <!-- Hero Restaurant Header -->
  <div class="restaurant-hero-card">
    <div class="hero-glow-bg"></div>

    @if(!empty($restaurant_details) && $restaurant_details->hasLogo())
      <div class="restaurant-avatar-wrap">
        <img src="{{ $restaurant_details->logo_url }}" alt="{{ $restaurant_details->name ?? 'Restaurant Logo' }}" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'restaurant-icon-circle\'><i class=\'fas fa-utensils\'></i></div>';">
      </div>
    @else
      <div class="restaurant-icon-circle"><i class="fas fa-utensils"></i></div>
    @endif

    <h1 class="restaurant-title serif-title">{{ $restaurant_details->name ?? 'Premium Dining' }}</h1>

    @if(!empty($restaurant_details->address))
      <p class="restaurant-tagline mb-2">
        <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $restaurant_details->address }}
      </p>
    @else
      <p class="restaurant-tagline">Exquisite culinary creations crafted for your table.</p>
    @endif

    <div class="meta-badges-row">
      @if(isset($table_details) && !empty($table_details->name))
        <span class="meta-pill">
          <i class="fas fa-chair"></i> Table {{ $table_details->name }}
        </span>
      @endif

      @if($restaurant_details->gstin)
        <span class="meta-pill">
          <i class="fas fa-receipt"></i> GSTIN: {{ $restaurant_details->gstin }} ({{ $restaurant_details->gst_percentage ?? 0 }}% GST)
        </span>
      @else
        <span class="meta-pill">
          <i class="fas fa-file-invoice"></i> Non-GST Bill
        </span>
      @endif

      @if(!empty($restaurant_details->fssai_number))
        <span class="meta-pill">
          <i class="fas fa-shield-halved"></i> FSSAI: {{ $restaurant_details->fssai_number }}
        </span>
      @endif

      <span class="meta-pill" style="background: rgba(255, 94, 20, 0.08); color: var(--primary); border-color: rgba(255, 94, 20, 0.2);">
        <i class="fas fa-qrcode"></i> Digital Menu
      </span>
    </div>
  </div>

  <!-- Guest Details Form -->
  <div class="guest-info-card">
    <div class="card-heading-clean">
      <div class="card-heading-icon"><i class="fas fa-user"></i></div>
      <h4>Guest Information</h4>
    </div>
    <div class="row">
      <div class="col-md-6 mb-3 mb-md-0">
        <label class="input-label-clean">Full Name <span class="text-danger">*</span></label>
        <div class="input-icon-group">
          <input type="text" id="customer_name" class="form-input-premium" placeholder="e.g. Rahul Sharma" autocomplete="name">
          <i class="fas fa-user"></i>
        </div>
      </div>
      <div class="col-md-6">
        <label class="input-label-clean">Mobile Number <span class="text-danger">*</span></label>
        <div class="input-icon-group">
          <input type="tel" id="phone" class="form-input-premium" placeholder="e.g. 9876543210" autocomplete="tel">
          <i class="fas fa-phone-alt"></i>
        </div>
      </div>
    </div>
    <input type="hidden" id="table_id" value="{{ $table_id }}">
    <input type="hidden" id="restaurant_id" value="{{ $restaurant_id }}">
    <input type="hidden" id="is_gst_registered" value="{{ $restaurant_details->gstin ? 'true' : 'false' }}">
    <input type="hidden" id="gst_percentage" value="{{ $restaurant_details->gst_percentage ?? 0 }}">
  </div>

  <!-- Search & Dietary Filters -->
  <div class="menu-controls-wrapper">
    <div class="search-box-pill">
      <i class="fas fa-search search-icon-left"></i>
      <input type="text" id="searchBox" placeholder="Search dishes, drinks, desserts..." autocomplete="off">
      <button type="button" class="search-clear-btn" id="clearSearchBtn"><i class="fas fa-times"></i></button>
    </div>

    <div class="dietary-filters-group">
      <button type="button" class="diet-btn active" data-type="">
        <i class="fas fa-utensils"></i> All Items
      </button>
      <button type="button" class="diet-btn" data-type="veg">
        <span class="fssai-box fssai-veg" style="width:14px;height:14px;border-width:1.5px;display:inline-flex;"><span class="fssai-symbol" style="width:6px;height:6px;"></span></span> Veg
      </button>
      <button type="button" class="diet-btn" data-type="non-veg">
        <span class="fssai-box fssai-nonveg" style="width:14px;height:14px;border-width:1.5px;display:inline-flex;"><span class="fssai-symbol" style="border-left-width:4px;border-right-width:4px;border-bottom-width:7px;"></span></span> Non-Veg
      </button>
    </div>
  </div>

  <!-- Category Tabs with "All" Option First -->
  <div class="category-nav-bar">
    <div class="category-pills-scroll" role="tablist">
      <!-- ALL Categories Tab (Default Active) -->
      <a class="cat-pill-tab active" data-toggle="tab" href="#catAll" role="tab">
        <i class="fas fa-layer-group"></i> All Menu
        <span class="cat-count-pill">{{ $totalDishesCount }}</span>
      </a>

      <!-- Individual Category Tabs -->
      @foreach($categories as $cat)
        <a class="cat-pill-tab" data-toggle="tab" href="#cat{{ $cat->id }}" role="tab">
          {{ $cat->name }}
          <span class="cat-count-pill">{{ $cat->subcategories->count() }}</span>
        </a>
      @endforeach
    </div>
  </div>

  <!-- Tab Content Area -->
  <div class="tab-content">
    
    <!-- ALL TAB PANE (Default Active) -->
    <div class="tab-pane fade show active" id="catAll" role="tabpanel">
      @if($totalDishesCount > 0)
        @foreach($categories as $cat)
          @if($cat->subcategories->count() > 0)
            <div class="category-group-block mb-4" data-category-id="{{ $cat->id }}">
              <div class="category-section-divider">
                <div class="category-section-title">
                  {{ $cat->name }}
                </div>
                <span class="category-item-count">{{ $cat->subcategories->count() }} dishes</span>
              </div>

              <div class="food-grid">
                @foreach($cat->subcategories as $item)
                  @php
                    $isVeg = strtolower($item->food_type) === 'veg';
                    $hasDiscount = ($item->discount_percentage ?? 0) > 0;
                    $discountedPrice = $hasDiscount ? ($item->price - ($item->price * $item->discount_percentage / 100)) : $item->price;
                  @endphp
                  <div class="food-card-wrapper" data-id="{{ $item->id }}" data-name="{{ strtolower($item->name) }}" data-type="{{ strtolower($item->food_type) }}">
                    <div class="food-card">
                      <div class="food-image-frame">
                        @if($item->image)
                          <img src="{{ URL::to('storage/category') }}/{{ $item->image }}" alt="{{ $item->name }}" class="food-image" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&h=350&fit=crop';">
                        @else
                          <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&h=350&fit=crop" alt="{{ $item->name }}" class="food-image">
                        @endif

                        <!-- FSSAI Veg / Non-Veg Indicator -->
                        <div class="fssai-indicator" title="{{ $isVeg ? 'Vegetarian' : 'Non-Vegetarian' }}">
                          <div class="fssai-box {{ $isVeg ? 'fssai-veg' : 'fssai-nonveg' }}">
                            <div class="fssai-symbol"></div>
                          </div>
                        </div>

                        <!-- Discount Tag -->
                        @if($hasDiscount)
                          <div class="discount-ribbon">
                            <i class="fas fa-fire"></i> {{ $item->discount_percentage }}% OFF
                          </div>
                        @endif
                      </div>

                      <div class="food-card-body">
                        <h3 class="food-item-title">{{ $item->name }}</h3>
                        <p class="food-item-desc">{{ $item->description ?? 'Expertly crafted dish prepared with the freshest authentic ingredients.' }}</p>

                        <div class="food-card-footer">
                          <div class="price-block">
                            @if($hasDiscount)
                              <span class="cross-price-label">₹{{ number_format($item->price, 2) }}</span>
                            @endif
                            <span class="active-price-label">₹{{ number_format($discountedPrice, 2) }}</span>
                            @if($restaurant_details->gstin)
                              <span class="gst-tax-note">+ {{ $restaurant_details->gst_percentage ?? 0 }}% GST</span>
                            @endif
                          </div>

                          <div class="card-stepper-wrap">
                            <button type="button" class="btn-add-tray addItemBtn" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}" data-discount="{{ $item->discount_percentage ?? 0 }}">
                              <i class="fas fa-plus"></i> Add
                            </button>
                            <div class="card-active-counter" data-card-id="{{ $item->id }}">
                              <button type="button" class="card-counter-btn decreaseCardQty" data-id="{{ $item->id }}">−</button>
                              <span class="card-counter-val">1</span>
                              <button type="button" class="card-counter-btn increaseCardQty" data-id="{{ $item->id }}">+</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          @endif
        @endforeach
      @else
        <div class="text-center py-5">
          <p class="text-muted">No dishes currently available in the menu.</p>
        </div>
      @endif

      <div class="no-results-state">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h5>No Matching Dishes Found</h5>
        <p class="text-muted">Try adjusting your search or filters to explore other items.</p>
      </div>
    </div>

    <!-- INDIVIDUAL CATEGORY TAB PANES -->
    @foreach($categories as $cat)
      <div class="tab-pane fade" id="cat{{ $cat->id }}" role="tabpanel">
        @if($cat->subcategories->count() > 0)
          <div class="category-section-divider">
            <div class="category-section-title">
              {{ $cat->name }}
            </div>
            <span class="category-item-count">{{ $cat->subcategories->count() }} dishes</span>
          </div>

          <div class="food-grid">
            @foreach($cat->subcategories as $item)
              @php
                $isVeg = strtolower($item->food_type) === 'veg';
                $hasDiscount = ($item->discount_percentage ?? 0) > 0;
                $discountedPrice = $hasDiscount ? ($item->price - ($item->price * $item->discount_percentage / 100)) : $item->price;
              @endphp
              <div class="food-card-wrapper" data-id="{{ $item->id }}" data-name="{{ strtolower($item->name) }}" data-type="{{ strtolower($item->food_type) }}">
                <div class="food-card">
                  <div class="food-image-frame">
                    @if($item->image)
                      <img src="{{ URL::to('storage/category') }}/{{ $item->image }}" alt="{{ $item->name }}" class="food-image" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&h=350&fit=crop';">
                    @else
                      <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&h=350&fit=crop" alt="{{ $item->name }}" class="food-image">
                    @endif

                    <div class="fssai-indicator" title="{{ $isVeg ? 'Vegetarian' : 'Non-Vegetarian' }}">
                      <div class="fssai-box {{ $isVeg ? 'fssai-veg' : 'fssai-nonveg' }}">
                        <div class="fssai-symbol"></div>
                      </div>
                    </div>

                    @if($hasDiscount)
                      <div class="discount-ribbon">
                        <i class="fas fa-fire"></i> {{ $item->discount_percentage }}% OFF
                      </div>
                    @endif
                  </div>

                  <div class="food-card-body">
                    <h3 class="food-item-title">{{ $item->name }}</h3>
                    <p class="food-item-desc">{{ $item->description ?? 'Expertly crafted dish prepared with the freshest authentic ingredients.' }}</p>

                    <div class="food-card-footer">
                      <div class="price-block">
                        @if($hasDiscount)
                          <span class="cross-price-label">₹{{ number_format($item->price, 2) }}</span>
                        @endif
                        <span class="active-price-label">₹{{ number_format($discountedPrice, 2) }}</span>
                        @if($restaurant_details->gstin)
                          <span class="gst-tax-note">+ {{ $restaurant_details->gst_percentage ?? 0 }}% GST</span>
                        @endif
                      </div>

                      <div class="card-stepper-wrap">
                        <button type="button" class="btn-add-tray addItemBtn" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}" data-discount="{{ $item->discount_percentage ?? 0 }}">
                          <i class="fas fa-plus"></i> Add
                        </button>
                        <div class="card-active-counter" data-card-id="{{ $item->id }}">
                          <button type="button" class="card-counter-btn decreaseCardQty" data-id="{{ $item->id }}">−</button>
                          <span class="card-counter-val">1</span>
                          <button type="button" class="card-counter-btn increaseCardQty" data-id="{{ $item->id }}">+</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-5">
            <p class="text-muted">No dishes in this category yet.</p>
          </div>
        @endif

        <div class="no-results-state">
          <i class="fas fa-search fa-3x text-muted mb-3"></i>
          <h5>No Matching Dishes Found</h5>
          <p class="text-muted">Try adjusting your search or dietary filter.</p>
        </div>
      </div>
    @endforeach

  </div>

  <!-- Order Summary Tray Section -->
  <div class="order-summary-card" id="orderSummaryCard">
    <div class="card-heading-clean">
      <div class="card-heading-icon"><i class="fas fa-receipt"></i></div>
      <h4>Your Order Tray</h4>
    </div>

    <div id="orderItemsContainer">
      <div class="tray-table-container" style="display: none;">
        <table class="tray-table">
          <thead>
            <tr>
              <th>Dish</th>
              <th style="text-align: center;">Quantity</th>
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

      <div id="emptyOrderState" class="empty-tray-state">
        <div class="empty-tray-icon"><i class="fas fa-shopping-basket"></i></div>
        <h5>Your Tray is Empty</h5>
        <p>Explore our delicious menu above and click <strong>Add +</strong> on any dish to begin your order.</p>
      </div>
    </div>

    <div class="tray-totals-box">
      <div class="tray-calc-row">
        <span>Original Subtotal</span>
        <span>₹<span id="original_subtotal">0.00</span></span>
      </div>
      <div class="tray-calc-row discount-row">
        <span>Total Savings / Discount</span>
        <span>− ₹<span id="item_discount">0.00</span></span>
      </div>
      <div class="tray-calc-row">
        <span>Net Taxable Amount</span>
        <span>₹<span id="taxable_amount">0.00</span></span>
      </div>
      @if($restaurant_details->gstin)
        <div class="tray-calc-row">
          <span>GST ({{ $restaurant_details->gst_percentage ?? 0 }}%)</span>
          <span>₹<span id="gst_amount">0.00</span></span>
        </div>
      @endif
      <div class="tray-calc-row grand-total-row">
        <span>Grand Total</span>
        <span class="grand-total-amount">₹<span id="final_total">0.00</span></span>
      </div>
    </div>
  </div>

</div><!-- /.main-container -->

<!-- Floating Bottom Sticky Bar -->
<div class="floating-checkout-bar" id="floatingBar">
  <div class="floating-summary-info">
    <span class="floating-items-count"><span id="floatingItemCount">0</span> Items in Tray</span>
    <span class="floating-grand-total">₹<span id="floatingGrandTotal">0.00</span></span>
  </div>

  <button type="button" class="btn-place-order-master" id="placeOrderBtn" disabled>
    <span>Confirm Order</span>
    <i class="fas fa-arrow-right"></i>
  </button>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let cart = [];
let isGstRegistered = $('#is_gst_registered').val() === 'true';
let gstPercentage = parseFloat($('#gst_percentage').val()) || 0;

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

function syncCardCounters() {
    // Reset all card counters
    $('.card-active-counter').hide();
    $('.btn-add-tray').show();

    // Show counter for items in cart
    cart.forEach(item => {
        let cardStepper = $(`.card-stepper-wrap`).has(`[data-id="${item.id}"]`);
        cardStepper.find('.btn-add-tray').hide();
        let counter = cardStepper.find(`.card-active-counter`);
        counter.find('.card-counter-val').text(item.qty);
        counter.css('display', 'flex');
    });
}

function updateEmptyState() {
    let totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
    $('#topCartBadge').text(totalQty);
    $('#floatingItemCount').text(totalQty);

    if (cart.length === 0) {
        $('#emptyOrderState').show();
        $('.tray-table-container').hide();
        $('#placeOrderBtn').prop('disabled', true);
        $('#floatingBar').css('transform', 'translateY(100%)');
    } else {
        $('#emptyOrderState').hide();
        $('.tray-table-container').show();
        $('#placeOrderBtn').prop('disabled', false);
        $('#floatingBar').css('transform', 'translateY(0)');
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
                <td>
                    <span class="tray-item-title">${item.name}</span>
                </td>
                <td style="text-align:center;">
                    <div class="stepper-pill">
                        <button type="button" class="step-btn decreaseQty" data-index="${i}">−</button>
                        <span class="step-value">${item.qty}</span>
                        <button type="button" class="step-btn increaseQty" data-index="${i}">+</button>
                    </div>
                </td>
                <td>
                    ${item.discount > 0 ? `<div style="text-decoration:line-through;color:var(--text-light);font-size:0.78rem;">₹${item.price.toFixed(2)}</div>` : ''}
                    <strong>₹${details.discountedPrice.toFixed(2)}</strong>
                </td>
                <td style="color:var(--veg-color);font-weight:600;">
                    ${item.discount > 0 ? `− ₹${details.discountAmount.toFixed(2)}` : '<span style="color:var(--text-light)">—</span>'}
                </td>
                <td>₹${details.taxableAmount.toFixed(2)}</td>`;

        if (isGstRegistered) {
            row += `<td>₹${details.gstAmount.toFixed(2)}</td>`;
        }

        row += `<td style="font-weight:700;color:var(--text-main);">₹${details.totalAmount.toFixed(2)}</td>
                <td>
                    <button type="button" class="tray-del-btn removeItem" data-index="${i}" title="Remove Item">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>`;
        tbody.append(row);
    });

    let grandTotal = totalTaxable + totalGst;
    $('#original_subtotal').text(originalSubtotal.toFixed(2));
    $('#item_discount').text(totalDiscount.toFixed(2));
    $('#taxable_amount').text(totalTaxable.toFixed(2));
    if (isGstRegistered) $('#gst_amount').text(totalGst.toFixed(2));
    $('#final_total').text(grandTotal.toFixed(2));
    $('#floatingGrandTotal').text(grandTotal.toFixed(2));

    syncCardCounters();
    updateEmptyState();
}

/* Add to cart from card */
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
    refreshTable();
});

/* Card counter buttons */
$(document).on('click', '.increaseCardQty', function() {
    let itemId = $(this).data('id');
    let item = cart.find(i => i.id === itemId);
    if (item) {
        item.qty++;
        refreshTable();
    }
});

$(document).on('click', '.decreaseCardQty', function() {
    let itemId = $(this).data('id');
    let itemIndex = cart.findIndex(i => i.id === itemId);
    if (itemIndex > -1) {
        if (cart[itemIndex].qty > 1) {
            cart[itemIndex].qty--;
        } else {
            cart.splice(itemIndex, 1);
        }
        refreshTable();
    }
});

/* Tray table qty controls */
$(document).on('click', '.increaseQty', function() {
    let idx = $(this).data('index');
    if (cart[idx]) {
        cart[idx].qty++;
        refreshTable();
    }
});

$(document).on('click', '.decreaseQty', function() {
    let idx = $(this).data('index');
    if (cart[idx]) {
        if (cart[idx].qty > 1) {
            cart[idx].qty--;
        } else {
            cart.splice(idx, 1);
        }
        refreshTable();
    }
});

$(document).on('click', '.removeItem', function() {
    let idx = $(this).data('index');
    cart.splice(idx, 1);
    refreshTable();
});

/* Live Filter & Search Logic */
function applyFilters() {
    let searchVal = $('#searchBox').val().toLowerCase().trim();
    let type = $('.diet-btn.active').data('type') || '';
    
    // Show/hide clear search button
    if (searchVal.length > 0) {
        $('#clearSearchBtn').show();
    } else {
        $('#clearSearchBtn').hide();
    }

    let activePane = $('.tab-pane.active');

    activePane.find('.food-card-wrapper').each(function() {
        let name = $(this).data('name') || '';
        let foodType = $(this).data('type') || '';
        let nameMatches = searchVal === '' || name.includes(searchVal);
        let typeMatches = type === '' || foodType === type;

        if (nameMatches && typeMatches) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });

    // Check visible cards in active pane
    let visibleCount = activePane.find('.food-card-wrapper:visible').length;
    let noResultsBox = activePane.find('.no-results-state');
    if (visibleCount === 0) {
        noResultsBox.show();
    } else {
        noResultsBox.hide();
    }

    // If in #catAll, hide empty category groups
    if (activePane.attr('id') === 'catAll') {
        $('.category-group-block').each(function() {
            let hasVisible = $(this).find('.food-card-wrapper:visible').length > 0;
            $(this).toggle(hasVisible);
        });
    }
}

$('#searchBox').on('input', applyFilters);

$('#clearSearchBtn').on('click', function() {
    $('#searchBox').val('').focus();
    applyFilters();
});

$('.diet-btn').on('click', function() {
    $('.diet-btn').removeClass('active');
    $(this).addClass('active');
    applyFilters();
});

$('a[data-toggle="tab"]').on('shown.bs.tab', function() {
    applyFilters();
});

/* Place Order Submission */
$('#placeOrderBtn').on('click', function() {
    if (cart.length === 0) {
        alert('Please add at least one dish to your order tray.');
        return;
    }

    let name  = $('#customer_name').val().trim();
    let phone = $('#phone').val().trim();

    if (!name) {
        alert('Please enter your full name before confirming.');
        $('html, body').animate({ scrollTop: $('.guest-info-card').offset().top - 80 }, 400);
        $('#customer_name').focus();
        return;
    }

    if (!phone) {
        alert('Please enter your mobile phone number.');
        $('html, body').animate({ scrollTop: $('.guest-info-card').offset().top - 80 }, 400);
        $('#phone').focus();
        return;
    }

    let orderItems = cart.map(item => ({
        id: item.id,
        name: item.name,
        price: item.price,
        qty: item.qty,
        item_discount: item.discount
    }));

    let $btn = $(this);
    $btn.html('<i class="fas fa-spinner fa-spin"></i> Processing Order...').prop('disabled', true);

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
            alert('Something went wrong while placing your order. Please try again.');
            $btn.html('<span>Confirm Order</span> <i class="fas fa-arrow-right"></i>').prop('disabled', false);
        }
    }).fail(function() {
        alert('Network connection error. Please check your internet and try again.');
        $btn.html('<span>Confirm Order</span> <i class="fas fa-arrow-right"></i>').prop('disabled', false);
    });
});

$(document).ready(function() {
    updateEmptyState();
});
</script>
</body>
</html>