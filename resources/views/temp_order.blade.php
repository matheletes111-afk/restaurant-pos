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

    .cat-pill-tab:focus {
      outline: none;
    }

    .cat-pill-tab.active .cat-count-pill {
      background: rgba(255, 255, 255, 0.25);
      color: #ffffff;
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
      min-width: 110px;
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
      min-width: 90px;
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

    /* Dish Customisable Badge */
    .dish-customise-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: rgba(255, 94, 20, 0.1);
      color: var(--primary);
      border: 1px solid rgba(255, 94, 20, 0.25);
      border-radius: var(--radius-full);
      padding: 2px 8px;
      font-size: 0.72rem;
      font-weight: 700;
      margin-top: 4px;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .dish-customise-badge:hover {
      background: rgba(255, 94, 20, 0.2);
      border-color: var(--primary);
      transform: scale(1.02);
    }

    .dish-action-buttons {
      display: flex;
      align-items: center;
      gap: 6px;
      width: 100%;
    }

    .dish-selected-stepper-group {
      display: none;
      align-items: center;
      gap: 6px;
      width: 100%;
    }

    .btn-customise-addon {
      background: #fff7ed;
      color: #ea580c;
      border: 1.5px solid #fed7aa;
      border-radius: var(--radius-full);
      padding: 8px 12px;
      font-size: 0.82rem;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 5px;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      white-space: nowrap;
    }

    .btn-customise-addon:hover {
      background: #ea580c;
      color: #ffffff;
      border-color: transparent;
      box-shadow: 0 4px 12px rgba(234, 88, 12, 0.3);
      transform: translateY(-1px);
    }

    .cat-pill-tab.tab-addons.active {
      background: linear-gradient(135deg, #ea580c 0%, #f59e0b 100%) !important;
      color: #ffffff !important;
      border-color: transparent !important;
      box-shadow: 0 6px 18px rgba(234, 88, 12, 0.35) !important;
    }

    .cat-pill-tab.tab-addons.active .cat-count-pill {
      background: rgba(255, 255, 255, 0.25);
      color: #ffffff;
    }

    .badge-addon-pill {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: #fff7ed;
      color: #ea580c;
      border: 1px solid #fed7aa;
      border-radius: var(--radius-full);
      padding: 3px 10px;
      font-size: 0.72rem;
      font-weight: 700;
    }

    .tray-addon-chips-wrap {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 6px;
      margin-top: 6px;
    }

    .tray-addon-chip {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      background: #fff7ed;
      color: #ea580c;
      border: 1px solid #fed7aa;
      border-radius: 6px;
      padding: 2px 7px;
      font-size: 0.73rem;
      font-weight: 700;
    }

    .btn-remove-tray-addon {
      background: transparent;
      border: none;
      color: #ea580c;
      font-size: 0.88rem;
      line-height: 1;
      padding: 0 0 0 3px;
      cursor: pointer;
      font-weight: 800;
      opacity: 0.7;
      transition: opacity 0.15s, color 0.15s;
    }

    .btn-remove-tray-addon:hover {
      opacity: 1;
      color: #dc2626;
    }

    .btn-tray-addon-manage {
      background: #fff7ed;
      color: #ea580c;
      border: 1px dashed #fdba74;
      border-radius: var(--radius-full);
      padding: 2px 10px;
      font-size: 0.73rem;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 4px;
      transition: all 0.2s ease;
      text-decoration: none !important;
    }

    .btn-tray-addon-manage:hover {
      background: #ea580c;
      color: #ffffff;
      border-color: #ea580c;
    }

    /* Customer Addon Modal / Bottom Sheet */
    .customer-addon-modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      width: 100vw;
      height: 100vh;
      background: rgba(15, 23, 42, 0.7);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      z-index: 10000;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      touch-action: pan-y;
    }

    @media (min-width: 768px) {
      .customer-addon-modal-overlay {
        align-items: center;
        padding: 20px;
      }
    }

    .customer-addon-modal-card {
      background: #ffffff;
      width: 100%;
      max-width: 520px;
      max-height: 88vh;
      border-radius: 28px 28px 0 0;
      display: flex;
      flex-direction: column;
      box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
      overflow: hidden;
      animation: slideUpCustModal 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @media (min-width: 768px) {
      .customer-addon-modal-card {
        border-radius: 24px;
        max-height: 80vh;
      }
    }

    @keyframes slideUpCustModal {
      from { transform: translateY(100%); }
      to { transform: translateY(0); }
    }

    .customer-addon-modal-header {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
      color: #ffffff;
      padding: 16px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 3px solid var(--primary);
    }

    .cust-addon-modal-title {
      margin: 0;
      font-family: 'Outfit', sans-serif;
      font-size: 1.15rem;
      font-weight: 700;
      color: #ffffff;
    }

    .cust-addon-modal-base {
      font-size: 0.8rem;
      color: rgba(255, 255, 255, 0.7);
      display: block;
    }

    .btn-close-addon-modal {
      background: rgba(255, 255, 255, 0.15);
      border: none;
      color: #ffffff;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      font-size: 1.4rem;
      line-height: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-close-addon-modal:hover {
      background: rgba(255, 255, 255, 0.3);
    }

    .customer-addon-modal-body {
      padding: 18px 20px;
      overflow-y: auto;
      flex: 1;
      -webkit-overflow-scrolling: touch;
    }

    .addon-section-title {
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 0.86rem;
      font-weight: 700;
      color: var(--text-main);
      margin-bottom: 12px;
      padding-bottom: 8px;
      border-bottom: 1px solid var(--border);
    }

    .optional-pill {
      background: var(--surface-3);
      color: var(--text-muted);
      font-size: 0.7rem;
      font-weight: 600;
      padding: 2px 8px;
      border-radius: var(--radius-full);
    }

    .cust-addon-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px 14px;
      border: 1.5px solid var(--border);
      border-radius: var(--radius-md);
      margin-bottom: 10px;
      background: #ffffff;
      transition: all 0.2s;
      cursor: pointer;
      user-select: none;
    }

    .cust-addon-row:hover {
      border-color: rgba(255, 94, 20, 0.4);
      background: #fffdfc;
    }

    .cust-addon-row.selected {
      border-color: var(--primary);
      background: #fffaf7;
    }

    .cust-addon-info {
      display: flex;
      align-items: center;
      gap: 10px;
      flex: 1;
    }

    .cust-addon-name {
      font-weight: 700;
      color: var(--text-main);
      font-size: 0.92rem;
      margin-bottom: 2px;
    }

    .cust-addon-price {
      font-weight: 700;
      color: var(--primary);
      font-size: 0.86rem;
    }

    .cust-addon-stepper {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      background: #ffffff;
      border: 1px solid var(--border);
      border-radius: var(--radius-full);
      padding: 2px 4px;
    }

    .cust-addon-step-btn {
      width: 26px;
      height: 26px;
      border-radius: 50%;
      border: none;
      background: var(--surface-3);
      color: var(--text-main);
      font-weight: 800;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }

    .cust-addon-step-btn:hover {
      background: var(--primary);
      color: #fff;
    }

    .cust-addon-step-val {
      min-width: 20px;
      text-align: center;
      font-weight: 800;
      font-size: 0.86rem;
      color: var(--text-main);
    }

    .customer-addon-modal-footer {
      background: #f8fafc;
      border-top: 1px solid var(--border);
      padding: 14px 20px calc(14px + env(safe-area-inset-bottom, 0px)) 20px;
    }

    .cust-addon-total-preview {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 12px;
    }

    .cust-addon-total-label {
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--text-muted);
    }

    .cust-addon-total-price {
      font-family: 'Outfit', sans-serif;
      font-size: 1.25rem;
      font-weight: 800;
      color: var(--primary);
    }

    .cust-addon-actions-row {
      display: flex;
      gap: 10px;
    }

    .btn-skip-addons {
      flex: 1;
      background: #ffffff;
      border: 1.5px solid var(--border);
      color: var(--text-muted);
      border-radius: var(--radius-md);
      padding: 12px 14px;
      font-weight: 700;
      font-size: 0.85rem;
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-skip-addons:hover {
      background: var(--surface-3);
      color: var(--text-main);
    }

    .btn-apply-cust-addons {
      flex: 1.8;
      background: var(--gradient-primary);
      border: none;
      color: #ffffff;
      border-radius: var(--radius-md);
      padding: 12px 16px;
      font-weight: 800;
      font-size: 0.92rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      box-shadow: 0 4px 14px rgba(255, 94, 20, 0.35);
      cursor: pointer;
      transition: all 0.2s;
    }

    .btn-apply-cust-addons:hover {
      transform: translateY(-1px);
      box-shadow: 0 6px 18px rgba(255, 94, 20, 0.45);
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

    /* Active Dining Order & Live Kitchen Panel */
    /* Active Dining Order & Live Kitchen Panel */
    .active-order-panel {
      background: #ffffff;
      border-radius: var(--radius-md);
      border: 1px solid rgba(255, 94, 20, 0.25);
      box-shadow: 0 10px 30px rgba(255, 94, 20, 0.08);
      margin-bottom: 28px;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .active-order-header-bar {
      background: linear-gradient(135deg, rgba(255, 94, 20, 0.06) 0%, rgba(255, 140, 66, 0.09) 100%);
      padding: 18px 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
      border-bottom: 1px solid rgba(255, 94, 20, 0.15);
    }

    .active-title-group {
      display: flex;
      align-items: center;
      gap: 14px;
      min-width: 0;
    }

    .active-pulse-ring {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: rgba(16, 185, 129, 0.15);
      border: 2px solid #10b981;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
      flex-shrink: 0;
    }

    .pulse-core {
      width: 12px;
      height: 12px;
      background: #10b981;
      border-radius: 50%;
      animation: pulseCoreAnim 1.6s infinite;
    }

    @keyframes pulseCoreAnim {
      0% { transform: scale(0.85); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
      70% { transform: scale(1.1); box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
      100% { transform: scale(0.85); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .active-session-tag {
      font-size: 0.73rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #059669;
    }

    .active-table-order-title {
      font-size: 1.18rem;
      font-weight: 800;
      color: var(--text-main);
      margin: 0;
      word-break: break-word;
    }

    .active-badge-status-wrap {
      display: flex;
      align-items: center;
      gap: 8px;
      flex-wrap: wrap;
    }

    .badge-active-live {
      background: #ecfdf5;
      color: #047857;
      border: 1px solid rgba(16, 185, 129, 0.3);
      padding: 6px 14px;
      border-radius: var(--radius-full);
      font-size: 0.82rem;
      font-weight: 700;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      white-space: nowrap;
    }

    .btn-toggle-active-items {
      background: #ffffff;
      color: var(--text-body);
      border: 1px solid var(--border);
      padding: 6px 14px;
      border-radius: var(--radius-full);
      font-size: 0.82rem;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s;
      white-space: nowrap;
    }

    .btn-toggle-active-items:hover {
      background: #f8fafc;
      border-color: #cbd5e1;
    }

    .active-items-collapsible {
      padding: 20px 24px;
      background: #ffffff;
    }

    .active-items-info-strip {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
      margin-bottom: 16px;
      padding-bottom: 12px;
      border-bottom: 1px solid var(--surface-3);
      font-size: 0.88rem;
      font-weight: 600;
      color: var(--text-body);
    }

    /* Clean, Elegant & Responsive Active Dishes List */
    .active-dishes-list {
      display: flex;
      flex-direction: column;
      gap: 10px;
      margin-bottom: 18px;
    }

    .active-dish-row {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      padding: 12px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      transition: all 0.2s ease;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }

    .active-dish-row:hover {
      border-color: #cbd5e1;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      background: #fafbfc;
    }

    .active-dish-main-info {
      display: flex;
      align-items: center;
      gap: 12px;
      flex: 1;
      min-width: 0;
    }

    .active-dish-details-col {
      min-width: 0;
      flex: 1;
    }

    .active-dish-qty-chip {
      background: rgba(255, 94, 20, 0.1);
      color: var(--primary);
      font-weight: 800;
      font-size: 0.82rem;
      padding: 4px 9px;
      border-radius: 8px;
      flex-shrink: 0;
      letter-spacing: -0.01em;
    }

    .active-dish-title-text {
      font-weight: 700;
      color: var(--text-main);
      font-size: 0.96rem;
      line-height: 1.35;
      display: block;
      word-break: break-word;
    }

    .active-dish-status-price-wrap {
      display: flex;
      align-items: center;
      gap: 14px;
      flex-shrink: 0;
    }

    .active-dish-price-action-group {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      flex-shrink: 0;
    }

    .active-dish-exact-price {
      font-size: 1.05rem;
      font-weight: 800;
      color: var(--text-main);
      min-width: 60px;
      text-align: right;
      letter-spacing: -0.01em;
      white-space: nowrap;
    }

    .active-dish-row:hover .active-dish-exact-price {
      color: var(--primary);
    }

    .active-dish-action-slot {
      display: inline-flex;
      align-items: center;
    }

    .dish-status-pill {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 0.74rem;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 9999px;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      white-space: nowrap;
    }

    .dish-status-pill.status-pending {
      background: #fffbeb;
      color: #b45309;
      border: 1px solid rgba(245, 158, 11, 0.35);
    }

    .dish-status-pill.status-cooking {
      background: #eff6ff;
      color: #1d4ed8;
      border: 1px solid rgba(59, 130, 246, 0.35);
    }

    .dish-status-pill.status-done {
      background: #ecfdf5;
      color: #047857;
      border: 1px solid rgba(16, 185, 129, 0.35);
    }

    .btn-cancel-active-dish {
      background: #fef2f2;
      color: #dc2626;
      border: 1px solid rgba(239, 68, 68, 0.25);
      border-radius: 8px;
      width: 32px;
      height: 32px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 0.78rem;
      cursor: pointer;
      transition: all 0.2s ease;
      padding: 0;
    }

    .btn-cancel-active-dish:hover {
      background: #dc2626;
      color: #ffffff;
      box-shadow: 0 2px 8px rgba(220, 38, 38, 0.25);
      transform: scale(1.05);
    }

    .active-items-count-badge {
      font-size: 0.78rem;
      font-weight: 700;
      color: var(--primary);
      background: rgba(255, 94, 20, 0.08);
      padding: 3px 10px;
      border-radius: 9999px;
      border: 1px solid rgba(255, 94, 20, 0.2);
      white-space: nowrap;
    }

    .active-section-heading {
      font-size: 0.92rem;
      font-weight: 800;
      color: var(--text-main);
      display: flex;
      align-items: center;
    }

    .btn-refresh-status {
      background: #ffffff;
      color: var(--primary);
      border: 1px solid rgba(255, 94, 20, 0.35);
      border-radius: var(--radius-full);
      padding: 5px 13px;
      font-size: 0.8rem;
      font-weight: 700;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
      white-space: nowrap;
    }

    .btn-refresh-status:hover {
      background: var(--surface-2);
      border-color: var(--primary);
      transform: translateY(-1px);
      box-shadow: 0 3px 8px rgba(255, 94, 20, 0.18);
    }

    .btn-refresh-status:active {
      transform: translateY(0);
    }

    .btn-refresh-status:disabled {
      opacity: 0.65;
      cursor: not-allowed;
      transform: none;
    }

    .already-ordered-ribbon {
      position: absolute;
      top: 14px;
      left: 14px;
      background: rgba(15, 23, 42, 0.90);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      color: #34d399;
      font-size: 0.74rem;
      font-weight: 800;
      padding: 4px 10px;
      border-radius: var(--radius-full);
      border: 1px solid rgba(52, 211, 153, 0.35);
      display: inline-flex;
      align-items: center;
      gap: 5px;
      z-index: 4;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .active-order-footer-note {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
      background: #f8fafc;
      border-radius: 12px;
      padding: 12px 18px;
      font-size: 0.9rem;
      color: var(--text-main);
      border: 1px solid var(--border);
    }

    .add-more-hint {
      color: var(--primary);
      font-weight: 700;
      font-size: 0.86rem;
    }

    /* Mobile Responsive Optimizations */
    @media (max-width: 640px) {
      .active-order-header-bar {
        padding: 14px 16px;
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
      }
      .active-title-group {
        width: 100%;
        gap: 10px;
      }
      .active-table-order-title {
        font-size: 1.05rem;
      }
      .active-badge-status-wrap {
        width: 100%;
        gap: 6px;
      }
      .btn-refresh-status, .btn-view-details-pill, .badge-active-live, .btn-toggle-active-items {
        font-size: 0.76rem;
        padding: 5px 10px;
      }
      .active-items-collapsible {
        padding: 14px 14px;
      }
      .active-items-info-strip {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        margin-bottom: 12px;
        padding-bottom: 10px;
      }
      .active-section-heading {
        font-size: 0.84rem;
        line-height: 1.35;
      }
      .active-dish-row {
        flex-direction: column;
        align-items: stretch;
        padding: 12px 14px;
        gap: 8px;
      }
      .active-dish-main-info {
        align-items: flex-start;
        gap: 10px;
        width: 100%;
      }
      .active-dish-qty-chip {
        margin-top: 2px;
        font-size: 0.78rem;
        padding: 3px 8px;
      }
      .active-dish-title-text {
        font-size: 0.92rem;
        white-space: normal;
      }
      .active-dish-status-price-wrap {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px dashed #f1f5f9;
        padding-top: 8px;
        margin-top: 2px;
        gap: 8px;
      }
      .dish-status-pill {
        padding: 3px 9px;
        font-size: 0.7rem;
      }
      .active-dish-price-action-group {
        gap: 8px;
      }
      .active-dish-exact-price {
        font-size: 1rem;
        min-width: auto;
        color: var(--primary);
      }
      .btn-cancel-active-dish {
        width: 28px;
        height: 28px;
        font-size: 0.72rem;
      }
      .active-order-footer-note {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
        padding: 12px 14px;
        font-size: 0.82rem;
      }
      .active-order-footer-note > div {
        width: 100%;
        justify-content: space-between;
      }
      .previously-ordered-tray-wrap .tray-table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
      }
    }

    /* Toast for temp order */
    .temp-toast {
      position: fixed;
      bottom: 90px;
      left: 50%;
      transform: translateX(-50%) translateY(40px);
      background: #0f172a;
      color: #ffffff;
      padding: 12px 24px;
      border-radius: 9999px;
      font-size: 0.9rem;
      font-weight: 600;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
      z-index: 9999;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .temp-toast.show {
      opacity: 1;
      visibility: visible;
      transform: translateX(-50%) translateY(0);
    }
  </style>
</head>
<body>

@php
  $addonCount = (isset($restaurant_addons) && $restaurant_addons->count() > 0) ? $restaurant_addons->count() : 0;
  $totalDishesCount = $categories->sum(function($c) { return $c->subcategories->count(); }) + $addonCount;
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
      <div class="restaurant-avatar-wrap restaurant-logo-card">
        <img src="{{ $restaurant_details->logo_url }}" alt="{{ $restaurant_details->name ?? 'Restaurant Logo' }}" onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'restaurant-icon-circle header-icon-ring\'><i class=\'fas fa-utensils\'></i></div>';">
      </div>
    @else
      <div class="restaurant-icon-circle header-icon-ring"><i class="fas fa-utensils"></i></div>
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

      @if(!empty($restaurant_details?->gstin))
        <span class="meta-pill">
          <i class="fas fa-receipt"></i> GSTIN: {{ $restaurant_details->gstin }} ({{ $restaurant_details->gst_percentage ?? 0 }}% GST)
        </span>
      @else
        <span class="meta-pill">
          <i class="fas fa-file-invoice"></i> Non-GST Bill
        </span>
      @endif

      @if(!empty($restaurant_details?->fssai_number))
        <span class="meta-pill">
          <i class="fas fa-shield-halved"></i> FSSAI: {{ $restaurant_details->fssai_number }}
        </span>
      @endif

      <span class="meta-pill" style="background: rgba(255, 94, 20, 0.08); color: var(--primary); border-color: rgba(255, 94, 20, 0.2);">
        <i class="fas fa-qrcode"></i> Digital Menu
      </span>
    </div>
  </div>

  @if(isset($pendingTempOrder) && $pendingTempOrder)
    <!-- Notice: Order is awaiting approval -->
    <div class="pending-notice-card" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid rgba(245, 158, 11, 0.35); border-radius: 20px; padding: 20px 24px; margin-bottom: 24px; box-shadow: var(--shadow-sm); display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
      <div style="display: flex; align-items: center; gap: 14px;">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: #f59e0b; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
          <i class="fas fa-hourglass-half"></i>
        </div>
        <div>
          <h5 style="margin: 0 0 3px; font-weight: 800; color: #92400e;">Order Placed &bull; Awaiting Restaurant Approval</h5>
          <p style="margin: 0; font-size: 0.88rem; color: #b45309;">Your initial order is pending confirmation from the restaurant. Once approved, you can add more dishes anytime.</p>
        </div>
      </div>
      <a href="{{ route('order.success', $pendingTempOrder->id) }}" class="btn" style="background: #d97706; color: #fff; font-weight: 700; border-radius: 9999px; padding: 8px 20px; font-size: 0.86rem; text-decoration: none;">
        <i class="fas fa-clock me-1"></i> Check Status
      </a>
    </div>
  @endif

  @if(isset($activeOrder) && $activeOrder)
    <!-- Active Dining Session & Live Kitchen Status Tracker -->
    <div class="active-order-panel" id="activeOrderPanel">
      <div class="active-order-header-bar">
        <div class="active-title-group">
          <div class="active-pulse-ring"><span class="pulse-core"></span></div>
          <div>
            <div class="active-session-tag">Active Dining Session</div>
            <h3 class="active-table-order-title">
              Table {{ $table_details->name ?? '' }} &bull; Order #{{ $activeOrder->order_id }}
            </h3>
            <div style="font-size: 0.85rem; font-weight: 700; color: var(--primary); margin-top: 2px;">
              Active Bill: ₹{{ number_format($activeOrder->grand_total, 2) }} &bull; {{ $activeOrder->orderItems->count() }} dishes sent to kitchen
            </div>
          </div>
        </div>
        <div class="active-badge-status-wrap">
          <button type="button" class="btn-refresh-status" title="Refresh Kitchen Status">
            <i class="fas fa-sync-alt"></i> <span>Refresh</span>
          </button>
          <a href="{{ route('order.details', $activeOrder->id) }}" class="btn-view-details-pill" style="display: inline-flex; align-items: center; gap: 5px; background: #ffffff; color: var(--primary); font-size: 0.8rem; font-weight: 700; padding: 5px 12px; border-radius: 9999px; border: 1px solid var(--primary); text-decoration: none; transition: all 0.2s ease;">
            <i class="fas fa-file-invoice"></i> View Details
          </a>
          <span class="badge-active-live">
            <i class="fas fa-fire-burner"></i> In Kitchen
          </span>
          <button type="button" class="btn-toggle-active-items" id="toggleActiveItemsBtn">
            <span id="toggleActiveText">Hide Dishes</span>
            <i class="fas fa-chevron-up" id="toggleActiveIcon"></i>
          </button>
        </div>
      </div>

      <div class="active-items-collapsible" id="activeItemsCollapsible">
        <div class="active-items-info-strip">
          <div class="active-section-heading">
            <i class="fas fa-fire-burner text-primary me-2"></i> Dishes Sent to Kitchen &bull; Live Status &amp; Pricing:
          </div>
          <div style="display: flex; align-items: center; gap: 8px;">
            <button type="button" class="btn-refresh-status" id="btnRefreshStatus" title="Refresh Kitchen Status">
              <i class="fas fa-sync-alt"></i> <span>Refresh Status</span>
            </button>
            <span class="active-items-count-badge">
              {{ $activeOrder->orderItems->count() }} {{ \Illuminate\Support\Str::plural('Item', $activeOrder->orderItems->count()) }}
            </span>
          </div>
        </div>

        <div class="active-dishes-list" id="activeDishesGrid">
          @foreach($activeOrder->orderItems as $actItem)
            @php
              $actName = $actItem->subcategory->name ?? ($actItem->addons_list[0]['name'] ?? 'Dish');
              $actStatus = strtoupper($actItem->order_status ?? 'PENDING');
              $actPrice = floatval($actItem->discounted_price ?: ($actItem->price ?: ($actItem->total_amount / max(1, $actItem->quantity))));
              $actTotal = floatval($actItem->total_amount ?: ($actPrice * $actItem->quantity));
              $isStandaloneAddon = empty($actItem->subcategory_id);
            @endphp
            <div class="active-dish-row" id="activeCard_{{ $actItem->id }}" data-item-id="{{ $actItem->id }}">
              <div class="active-dish-main-info">
                <span class="active-dish-qty-chip">{{ $actItem->quantity }}x</span>
                <div class="active-dish-details-col">
                  <span class="active-dish-title-text">
                    {{ $actName }}
                  </span>
                  @if(!empty($actItem->addons_list) && !$isStandaloneAddon)
                    <div class="tray-addon-chips-wrap" style="margin-top: 2px;">
                      @foreach($actItem->addons_list as $ad)
                        @php
                          $adQty = $ad['qty'] ?? $ad['quantity'] ?? 1;
                          $adPrice = floatval($ad['price'] ?? 0);
                        @endphp
                        <span class="tray-addon-chip" style="font-size: 0.68rem; padding: 1px 6px; display: inline-flex; align-items: center; gap: 3px;">
                          <span>+ {{ $ad['name'] ?? '' }}</span>
                          <span style="color: #9a3412;">(₹{{ number_format($adPrice, 2) }})</span>
                          <span style="background: #ffedd5; color: #c2410c; font-weight: 800; font-size: 0.65rem; padding: 0 4px; border-radius: 3px; border: 1px solid #fed7aa;">x{{ $adQty }}</span>
                        </span>
                      @endforeach
                    </div>
                  @endif
                </div>
              </div>

              <div class="active-dish-status-price-wrap">
                <span class="dish-status-pill status-{{ strtolower($actStatus) }}" id="activeStatusPill_{{ $actItem->id }}">
                  @if($actStatus === 'COOKING')
                    <i class="fas fa-fire-burner"></i> Cooking
                  @elseif($actStatus === 'DONE')
                    <i class="fas fa-check-circle"></i> Cooked
                  @else
                    <i class="fas fa-hourglass-half"></i> Pending
                  @endif
                </span>

                <div class="active-dish-price-action-group">
                  <span class="active-dish-exact-price" id="activeItemPrice_{{ $actItem->id }}">
                    ₹{{ number_format($actTotal, 2) }}
                  </span>

                  <div class="active-dish-action-slot" id="activeItemActions_{{ $actItem->id }}">
                    @if($actStatus === 'PENDING')
                      <button type="button" class="btn-cancel-active-dish" onclick="cancelActiveOrderItem({{ $actItem->id }})" title="Cancel pending dish">
                        <i class="fas fa-trash-alt"></i>
                      </button>
                    @endif
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>

        <div class="active-order-footer-note" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
          <span>Current Active Total: <strong style="font-size: 1.15rem; color: var(--primary);">₹{{ number_format($activeOrder->grand_total, 2) }}</strong> ({{ $activeOrder->orderItems->count() }} dishes sent to kitchen)</span>
          <div style="display: flex; align-items: center; gap: 12px;">
            <a href="{{ route('order.details', $activeOrder->id) }}" style="color: var(--primary); font-weight: 700; font-size: 0.84rem; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
              <i class="fas fa-receipt"></i> Full Order Details &bull; Status &rarr;
            </a>
            <span class="add-more-hint"><i class="fas fa-arrow-down text-primary me-1"></i> Add items below</span>
          </div>
        </div>
      </div>
    </div>
  @endif

  <!-- Guest Details Form -->
  <div class="guest-info-card" @if(isset($activeOrder) && $activeOrder) style="background: rgba(248, 250, 252, 0.7); border-color: #cbd5e1;" @endif>
    <div class="card-heading-clean" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
      <div style="display: flex; align-items: center; gap: 10px;">
        <div class="card-heading-icon"><i class="fas fa-user"></i></div>
        <h4 style="margin: 0;">
          Guest Information
          @if(isset($activeOrder) && $activeOrder)
            <span style="font-size: 0.76rem; font-weight: 700; color: #059669; background: #ecfdf5; border: 1px solid rgba(16,185,129,0.3); padding: 3px 10px; border-radius: 9999px; margin-left: 8px;">
              <i class="fas fa-check me-1"></i> Active Table Guest
            </span>
          @endif
        </h4>
      </div>
      @if(isset($activeOrder) && $activeOrder)
        <a href="{{ route('temp.order.fresh', [$table_id, $restaurant_id]) }}" class="btn-start-fresh" onclick="return confirm('Start a fresh new order?');" style="font-size: 0.8rem; font-weight: 700; color: #64748b; background: #fff; border: 1px solid #cbd5e1; padding: 6px 14px; border-radius: 9999px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;">
          <i class="fas fa-plus-circle text-primary"></i> Start Fresh Order
        </a>
      @endif
    </div>
    <div class="row mt-3">
      <div class="col-md-6 mb-3 mb-md-0">
        <label class="input-label-clean">Full Name <span class="text-danger">*</span></label>
        <div class="input-icon-group">
          <input type="text" id="customer_name" class="form-input-premium" placeholder="e.g. Sayan Ghosh" autocomplete="name"
            value="{{ $activeOrder->customer_name ?? (session('customer_name') ?? '') }}" @if(isset($activeOrder) && $activeOrder) readonly style="background: #f1f5f9; cursor: not-allowed;" @endif>
          <i class="fas fa-user"></i>
        </div>
      </div>
      <div class="col-md-6">
        <label class="input-label-clean">Mobile Number <span class="text-danger">*</span></label>
        <div class="input-icon-group">
          <input type="tel" id="phone" class="form-input-premium" placeholder="e.g. 9876543210" autocomplete="tel"
            maxlength="10"
            inputmode="numeric"
            pattern="[0-9]{10}"
            onkeydown="if(['e','E','+','-','.'].includes(event.key)) event.preventDefault();"
            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
            value="{{ $activeOrder->customer_phone ?? (session('customer_phone') ?? '') }}" @if(isset($activeOrder) && $activeOrder) readonly style="background: #f1f5f9; cursor: not-allowed;" @endif>
          <i class="fas fa-phone-alt"></i>
        </div>
      </div>
    </div>
    <input type="hidden" id="table_id" value="{{ $table_id }}">
    <input type="hidden" id="restaurant_id" value="{{ $restaurant_id }}">
    <input type="hidden" id="active_order_id" value="{{ $activeOrder->id ?? '' }}">
    <input type="hidden" id="is_gst_registered" value="{{ !empty($restaurant_details?->gstin) ? 'true' : 'false' }}">
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
      <button type="button" class="diet-btn active" data-type="" title="Show all items">
        <i class="fas fa-utensils"></i> All
      </button>
      <button type="button" class="diet-btn" data-type="veg" title="Vegetarian dishes only">
        <span class="fssai-box fssai-veg" style="width:14px;height:14px;border-width:1.5px;display:inline-flex;"><span class="fssai-symbol" style="width:6px;height:6px;"></span></span> Veg
      </button>
      <button type="button" class="diet-btn" data-type="non-veg" title="Non-Vegetarian dishes only">
        <span class="fssai-box fssai-nonveg" style="width:14px;height:14px;border-width:1.5px;display:inline-flex;"><span class="fssai-symbol" style="border-left-width:4px;border-right-width:4px;border-bottom-width:7px;"></span></span> Non-Veg
      </button>
    </div>
  </div>

  <!-- Category Filter Bar -->
  <div class="category-nav-bar">
    <div class="category-pills-scroll" role="tablist">
      <!-- ALL Categories Tab (Default Active) -->
      <button type="button" class="cat-pill-tab active" data-category="all">
        <i class="fas fa-layer-group"></i> All Items
        <span class="cat-count-pill">{{ $totalDishesCount }}</span>
      </button>

      <!-- Individual Category Tabs -->
      @foreach($categories as $cat)
        @if($cat->subcategories->count() > 0)
          <button type="button" class="cat-pill-tab" data-category="{{ $cat->id }}">
            {{ $cat->name }}
            <span class="cat-count-pill">{{ $cat->subcategories->count() }}</span>
          </button>
        @endif
      @endforeach

      <!-- Add-ons Category Tab -->
      @if(isset($restaurant_addons) && $restaurant_addons->count() > 0)
        <button type="button" class="cat-pill-tab tab-addons" data-category="addons">
          <i class="fas fa-puzzle-piece text-warning"></i> Add-ons
          <span class="cat-count-pill" style="background: rgba(245, 158, 11, 0.2); color: #d97706;">{{ $restaurant_addons->count() }}</span>
        </button>
      @endif
    </div>
  </div>

  <!-- Menu Dishes Container -->
  <div class="menu-dishes-container" id="menuDishesContainer">
    @php
      $activeOrderedDishCounts = [];
      if (isset($activeOrder) && $activeOrder && $activeOrder->orderItems) {
        foreach ($activeOrder->orderItems as $aItm) {
          $sid = $aItm->subcategory_id;
          if ($sid) {
            $activeOrderedDishCounts[$sid] = ($activeOrderedDishCounts[$sid] ?? 0) + $aItm->quantity;
          }
        }
      }
    @endphp
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
                  $rawType = trim(strtolower($item->food_type ?? 'veg'));
                  $isVeg = !in_array($rawType, ['non-veg', 'non_veg', 'non veg', 'nonveg', 'egg']);
                  $hasDiscount = ($item->discount_percentage ?? 0) > 0;
                  $discountedPrice = $hasDiscount ? ($item->price - ($item->price * $item->discount_percentage / 100)) : $item->price;
                  $alreadyOrderedQty = $activeOrderedDishCounts[$item->id] ?? 0;
                  
                  $mappedAddons = ($item->addons && $item->addons->count() > 0) ? $item->addons->where('status', '!=', 'D')->where('status', '!=', 'I')->filter(function($a) use ($item) {
                      return strtolower(trim($a->name)) !== strtolower(trim($item->name));
                  })->map(function($a) {
                      return [
                          'id' => $a->id,
                          'name' => $a->name,
                          'price' => floatval($a->price),
                          'food_type' => $a->food_type ?? 'Veg',
                      ];
                  })->values() : collect([]);
                  
                  $availableDishAddons = $mappedAddons;
                  $hasAddons = $availableDishAddons->count() > 0;
                @endphp
                <div class="food-card-wrapper" data-id="{{ $item->id }}" data-category-id="{{ $cat->id }}" data-name="{{ strtolower($item->name) }}" data-desc="{{ strtolower($item->description ?? '') }}" data-type="{{ $isVeg ? 'veg' : 'non-veg' }}">
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

                      <!-- Already Ordered Badge -->
                      @if($alreadyOrderedQty > 0)
                        <div class="already-ordered-ribbon" title="Currently ordered for this table">
                          <i class="fas fa-check-circle"></i> {{ $alreadyOrderedQty }}x Ordered
                        </div>
                      @endif

                      <!-- Discount Tag -->
                      @if($hasDiscount)
                        <div class="discount-ribbon">
                          <i class="fas fa-fire"></i> {{ $item->discount_percentage }}% OFF
                        </div>
                      @endif
                    </div>

                    <div class="food-card-body">
                      <h3 class="food-item-title">{{ $item->name }}</h3>
                      @if($alreadyOrderedQty > 0)
                        <div style="font-size: 0.78rem; font-weight: 700; color: #059669; margin-bottom: 6px; display: inline-flex; align-items: center; gap: 5px;">
                          <i class="fas fa-fire-burner"></i> {{ $alreadyOrderedQty }}x active in kitchen
                        </div>
                      @endif
                      <p class="food-item-desc">{{ $item->description ?? 'Expertly crafted dish prepared with the freshest authentic ingredients.' }}</p>

                      <div class="food-card-footer">
                        <div class="price-block">
                          @if($hasDiscount)
                            <span class="cross-price-label">₹{{ number_format($item->price, 2) }}</span>
                          @endif
                          <span class="active-price-label">₹{{ number_format($discountedPrice, 2) }}</span>
                          @if(!empty($restaurant_details?->gstin))
                            <span class="gst-tax-note">+ {{ $restaurant_details->gst_percentage ?? 0 }}% GST</span>
                          @endif
                        </div>

                        <div class="card-stepper-wrap">
                          <button type="button" class="btn-add-tray addItemBtn initial-dish-actions" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}" data-discounted-price="{{ $discountedPrice }}" data-discount="{{ $item->discount_percentage ?? 0 }}" data-food-type="{{ $isVeg ? 'Veg' : 'Non-Veg' }}" data-addons='@json($availableDishAddons)'>
                            <i class="fas fa-plus"></i> Add
                          </button>
                          @if($hasAddons)
                            <span class="dish-customise-badge openAddonCustomiseBtn" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}" data-discounted-price="{{ $discountedPrice }}" data-discount="{{ $item->discount_percentage ?? 0 }}" data-food-type="{{ $isVeg ? 'Veg' : 'Non-Veg' }}" data-addons='@json($availableDishAddons)'>
                              <i class="fas fa-sliders-h"></i> Customisable
                            </span>
                          @endif
                          <div class="card-active-counter" data-card-id="{{ $item->id }}">
                            <button type="button" class="card-counter-btn decreaseCardQty" data-id="{{ $item->id }}">−</button>
                            <span class="card-counter-val">1</span>
                            <button type="button" class="card-counter-btn increaseCardQty" data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}" data-discounted-price="{{ $discountedPrice }}" data-discount="{{ $item->discount_percentage ?? 0 }}" data-food-type="{{ $isVeg ? 'Veg' : 'Non-Veg' }}" data-addons='@json($availableDishAddons)'>+</button>
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

      <!-- Standalone Restaurant Add-ons Group Block -->
      @if(isset($restaurant_addons) && $restaurant_addons->count() > 0)
        <div class="category-group-block mb-4" data-category-id="addons">
          <div class="category-section-divider">
            <div class="category-section-title">
              <i class="fas fa-puzzle-piece text-warning me-2"></i> Add-ons &amp; Extras
            </div>
            <span class="category-item-count">{{ $restaurant_addons->count() }} add-ons</span>
          </div>

          <div class="food-grid">
            @foreach($restaurant_addons as $addon)
              @php
                $rawType = trim(strtolower($addon->food_type ?? 'veg'));
                $isVeg = !in_array($rawType, ['non-veg', 'non_veg', 'non veg', 'nonveg', 'egg']);
                $addonPrice = floatval($addon->price ?? 0);
              @endphp
              <div class="food-card-wrapper addon-card-wrapper" data-id="addon_{{ $addon->id }}" data-category-id="addons" data-name="{{ strtolower($addon->name) }}" data-desc="{{ strtolower($addon->description ?? 'Add-on extra portion') }}" data-type="{{ $isVeg ? 'veg' : 'non-veg' }}" data-is-addon="1">
                <div class="food-card">
                  <div class="food-image-frame" style="height: 140px; background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); display: flex; align-items: center; justify-content: center; position: relative;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; color: #ea580c; box-shadow: 0 4px 12px rgba(234, 88, 12, 0.15);">
                      <i class="fas fa-puzzle-piece"></i>
                    </div>

                    <!-- FSSAI Veg / Non-Veg Indicator -->
                    <div class="fssai-indicator" title="{{ $isVeg ? 'Vegetarian' : 'Non-Vegetarian' }}">
                      <div class="fssai-box {{ $isVeg ? 'fssai-veg' : 'fssai-nonveg' }}">
                        <div class="fssai-symbol"></div>
                      </div>
                    </div>

                    <span class="badge-addon-pill" style="position: absolute; top: 12px; left: 12px; background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa; border-radius: 9999px; padding: 3px 10px; font-size: 0.72rem; font-weight: 700;">
                      <i class="fas fa-plus-circle"></i> Standalone Add-on
                    </span>
                  </div>

                  <div class="food-card-body">
                    <h3 class="food-item-title">{{ $addon->name }}</h3>
                    <p class="food-item-desc">{{ $addon->description ?? 'Standalone add-on portion to enjoy separately with your meal.' }}</p>

                    <div class="food-card-footer">
                      <div class="price-block">
                        <span class="active-price-label">₹{{ number_format($addonPrice, 2) }}</span>
                        <span class="small text-muted" style="font-size: 0.74rem;">/ portion</span>
                      </div>

                      <div class="card-stepper-wrap">
                        <button type="button" class="btn-add-tray addStandaloneAddonBtn initial-dish-actions" data-id="{{ $addon->id }}" data-name="{{ $addon->name }}" data-price="{{ $addonPrice }}" data-food-type="{{ $isVeg ? 'Veg' : 'Non-Veg' }}">
                          <i class="fas fa-plus"></i> Add
                        </button>
                        <div class="card-active-counter" data-card-id="addon_{{ $addon->id }}">
                          <button type="button" class="card-counter-btn decreaseCardQty" data-id="addon_{{ $addon->id }}">−</button>
                          <span class="card-counter-val">1</span>
                          <button type="button" class="card-counter-btn increaseStandaloneAddonQty" data-id="{{ $addon->id }}" data-name="{{ $addon->name }}" data-price="{{ $addonPrice }}" data-food-type="{{ $isVeg ? 'Veg' : 'Non-Veg' }}">+</button>
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

    @else
      <div class="text-center py-5">
        <p class="text-muted">No dishes currently available in the menu.</p>
      </div>
    @endif

    <div class="no-results-state" id="noResultsState">
      <i class="fas fa-search fa-3x text-muted mb-3"></i>
      <h5>No Matching Dishes Found</h5>
      <p class="text-muted">Try adjusting your search or category filter to explore other items.</p>
    </div>
  </div>

  <!-- Order Summary Tray Section -->
  <div class="order-summary-card" id="orderSummaryCard">
    <div class="card-heading-clean">
      <div class="card-heading-icon"><i class="fas fa-receipt"></i></div>
      @if(isset($activeOrder) && $activeOrder)
        <h4>Add Dishes to Order</h4>
      @else
        <h4>Your Order Tray</h4>
      @endif
    </div>

    @if(isset($activeOrder) && $activeOrder && $activeOrder->orderItems && $activeOrder->orderItems->count() > 0)
      <!-- Itemized Previously Ordered Dishes Section -->
      <div class="previously-ordered-tray-wrap mb-4" id="previouslyOrderedTableContainer">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
          <h5 style="margin: 0; font-size: 1rem; font-weight: 800; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
            <i class="fas fa-fire-burner text-primary"></i> Previously Ordered Dishes (In Kitchen)
          </h5>
          <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">
            {{ $activeOrder->orderItems->count() }} items &bull; <strong style="color: var(--primary);">₹{{ number_format($activeOrder->grand_total, 2) }}</strong>
          </span>
        </div>
        <div class="tray-table-container" style="display: block; margin-bottom: 16px; background: #fafafa;">
          <table class="tray-table">
            <thead>
              <tr>
                <th>Dish</th>
                <th style="text-align: center;">Quantity</th>
                <th>Unit Price</th>
                <th>Kitchen Status</th>
                <th>Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach($activeOrder->orderItems as $actItm)
                @php
                  $pName = $actItm->subcategory->name ?? ($actItm->addons_list[0]['name'] ?? 'Dish');
                  $pPrice = floatval($actItm->discounted_price ?: ($actItm->price ?: ($actItm->total_amount / max(1, $actItm->quantity))));
                  $pTotal = floatval($actItm->total_amount ?: ($pPrice * $actItm->quantity));
                  $pStatus = strtoupper($actItm->order_status ?? 'PENDING');
                  $isPrevStandaloneAddon = empty($actItm->subcategory_id);
                @endphp
                <tr>
                  <td>
                    <span class="tray-item-title">
                      {{ $pName }}
                    </span>
                    @if(!empty($actItm->addons_list) && !$isPrevStandaloneAddon)
                      <div class="tray-addon-chips-wrap" style="margin-top: 2px;">
                        @foreach($actItm->addons_list as $ad)
                          @php
                            $adQty = $ad['qty'] ?? $ad['quantity'] ?? 1;
                            $adPrice = floatval($ad['price'] ?? 0);
                          @endphp
                          <span class="tray-addon-chip" style="font-size: 0.68rem; padding: 1px 6px; display: inline-flex; align-items: center; gap: 3px;">
                            <span>+ {{ $ad['name'] ?? '' }}</span>
                            <span style="color: #9a3412;">(₹{{ number_format($adPrice, 2) }})</span>
                            <span style="background: #ffedd5; color: #c2410c; font-weight: 800; font-size: 0.65rem; padding: 0 4px; border-radius: 3px; border: 1px solid #fed7aa;">x{{ $adQty }}</span>
                          </span>
                        @endforeach
                      </div>
                    @endif
                    @if(!empty($actItm->kot_no))
                      <span class="kot-badge" style="font-size: 0.7rem; padding: 2px 7px;"><i class="fas fa-receipt"></i> {{ $actItm->kot_no }}</span>
                    @endif
                  </td>
                  <td style="text-align: center;">
                    <span style="display: inline-block; font-weight: 800; background: var(--surface-3); border-radius: 6px; padding: 3px 10px; font-size: 0.88rem; color: var(--text-main);">
                      {{ $actItm->quantity }}x
                    </span>
                  </td>
                  <td style="font-weight: 600; color: var(--text-muted);">₹{{ number_format($pPrice, 2) }}</td>
                  <td>
                    <span class="dish-status-pill status-{{ strtolower($pStatus) }}">
                      @if($pStatus === 'COOKING')
                        <i class="fas fa-fire-burner"></i> Cooking
                      @elseif($pStatus === 'DONE')
                        <i class="fas fa-check-circle"></i> Cooked
                      @else
                        <i class="fas fa-hourglass-half"></i> Pending
                      @endif
                    </span>
                  </td>
                  <td style="font-weight: 800; color: var(--text-main); font-size: 0.98rem;">₹{{ number_format($pTotal, 2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endif

    <div id="orderItemsContainer">
      @if(isset($activeOrder) && $activeOrder)
        <div style="font-size: 0.92rem; font-weight: 800; color: var(--text-main); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
          <i class="fas fa-plus-circle text-primary"></i> New Dishes to Add
        </div>
      @endif

      <div class="tray-table-container" id="newItemsTableContainer" style="display: none;">
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
        @if(isset($activeOrder) && $activeOrder)
          <h5>Your New Item Tray is Empty</h5>
          <p>Browse our menu above and click <strong>Add +</strong> on any dish to add more items to your table order.</p>
        @else
          <h5>Your Tray is Empty</h5>
          <p>Explore our delicious menu above and click <strong>Add +</strong> on any dish to begin your order.</p>
        @endif
      </div>
    </div>

    <div class="tray-totals-box">
      @if(isset($activeOrder) && $activeOrder)
        <div class="tray-calc-row" style="color: var(--text-main); font-weight: 700; border-bottom: 1px dashed #cbd5e1; padding-bottom: 10px; margin-bottom: 8px;">
          <span><i class="fas fa-receipt text-primary me-1"></i> Active Table Bill (In Kitchen)</span>
          <span style="font-size: 1.15rem; color: var(--primary);">₹<span id="active_order_amount">{{ number_format($activeOrder->grand_total, 2) }}</span></span>
        </div>
      @endif

      <div class="tray-calc-row">
        <span>@if(isset($activeOrder) && $activeOrder) New Items Original Subtotal @else Original Subtotal @endif</span>
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
      <div class="tray-calc-row" style="font-weight: 700; color: var(--text-main);">
        <span>@if(isset($activeOrder) && $activeOrder) New Items Total @else Grand Total @endif</span>
        <span style="font-size: 1.15rem;">₹<span id="final_total">0.00</span></span>
      </div>

      @if(isset($activeOrder) && $activeOrder)
        <div class="tray-calc-row grand-total-row" style="margin-top: 14px; padding-top: 16px; border-top: 2px solid var(--text-main);">
          <span>Combined Total Bill (Active + New)</span>
          <span class="grand-total-amount">₹<span id="combined_final_total">{{ number_format($activeOrder->grand_total, 2) }}</span></span>
        </div>
      @endif
    </div>
  </div>

</div><!-- /.main-container -->

<!-- Customer Addon Customization Modal / Bottom Sheet -->
<div class="customer-addon-modal-overlay" id="customerAddonModal" style="display: none;">
  <div class="customer-addon-modal-card">
    <div class="customer-addon-modal-header">
      <div style="display: flex; align-items: center; gap: 10px;">
        <div class="fssai-indicator" style="position: static; box-shadow: none;" id="custAddonDishSymbolWrap">
          <div class="fssai-box fssai-veg" id="custAddonDishSymbol">
            <div class="fssai-symbol"></div>
          </div>
        </div>
        <div>
          <h4 class="cust-addon-modal-title" id="custAddonDishTitle">Customise Dish</h4>
          <span class="cust-addon-modal-base" id="custAddonBasePrice">Base Price: ₹0.00</span>
        </div>
      </div>
      <button type="button" class="btn-close-addon-modal" id="btnCloseCustAddonModal" aria-label="Close">&times;</button>
    </div>

    <div class="customer-addon-modal-body">
      <div class="addon-section-title">
        <span>Choose Add-ons &amp; Extras</span>
        <span class="optional-pill">Optional</span>
      </div>

      <div class="cust-addons-list" id="custAddonsList">
        <!-- Addon items rendered here dynamically -->
      </div>
    </div>

    <div class="customer-addon-modal-footer">
      <div class="cust-addon-total-preview">
        <span class="cust-addon-total-label">Dish Total with Add-ons:</span>
        <span class="cust-addon-total-price" id="custAddonModalTotalPrice">₹0.00</span>
      </div>
      <div class="cust-addon-actions-row">
        <button type="button" class="btn-skip-addons" id="btnSkipAddons">
          Skip Add-ons
        </button>
        <button type="button" class="btn-apply-cust-addons" id="btnApplyCustAddons">
          <span>Add to Tray</span> &bull; <strong id="btnApplyCustAddonsPrice">₹0.00</strong>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Floating Bottom Sticky Bar -->
<div class="floating-checkout-bar" id="floatingBar">
  <div class="floating-summary-info">
    <span class="floating-items-count"><span id="floatingItemCount">0</span> Items in Tray</span>
    <span class="floating-grand-total">₹<span id="floatingGrandTotal">0.00</span></span>
  </div>

  <div id="floatingBarBtnWrap">
    @if(isset($activeOrder) && $activeOrder)
      <a href="{{ route('order.details', $activeOrder->id) }}" class="btn-place-order-master" style="text-decoration: none;">
        <i class="fas fa-file-invoice"></i> View Live Status
      </a>
    @else
      <button type="button" class="btn-place-order-master" id="placeOrderBtn" disabled>
        <span>Confirm Order</span>
        <i class="fas fa-arrow-right"></i>
      </button>
    @endif
  </div>
</div>

<div id="tempToast" class="temp-toast"><i class="fas fa-check-circle text-success"></i> <span id="tempToastMsg">Success</span></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
let cart = [];
let isGstRegistered = $('#is_gst_registered').val() === 'true';
let gstPercentage = parseFloat($('#gst_percentage').val()) || 0;
let hasActiveOrder = {{ isset($activeOrder) && $activeOrder ? 'true' : 'false' }};
let activeOrderTotal = parseFloat('{{ $activeOrder->grand_total ?? 0 }}') || 0;
window.POS_RESTAURANT_ADDONS = @json($restaurant_addons ?? []);

let currentCustomisingDish = null;
let currentSelectedAddons = {}; // { addonId: { id, name, price, qty, food_type } }

function escapeHtml(text) {
    if (!text) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function calculateItemDetails(item) {
    let isAddon = item.is_addon || String(item.id).startsWith('addon_');
    let basePrice = item.base_price !== undefined ? parseFloat(item.base_price) : parseFloat(item.price || 0);
    let qty = parseInt(item.qty) || 1;
    let discount = parseFloat(item.discount) || 0;
    let addonsCost = 0;
    if (!isAddon && item.addons && Array.isArray(item.addons)) {
        addonsCost = item.addons.reduce((sum, a) => sum + ((parseFloat(a.price) || 0) * (parseInt(a.qty || a.quantity) || 1)), 0);
    }
    let discountedPrice = basePrice - (basePrice * discount / 100);
    let taxableAmount = isAddon ? (discountedPrice * qty) : ((discountedPrice * qty) + addonsCost);
    let originalSubtotal = isAddon ? (basePrice * qty) : ((basePrice * qty) + addonsCost);
    let discountAmount = (basePrice * discount / 100) * qty;
    let gstAmount = isGstRegistered ? (taxableAmount * gstPercentage) / 100 : 0;
    let totalAmount = taxableAmount + gstAmount;

    return {
        basePrice: basePrice,
        discountedPrice: discountedPrice,
        taxableAmount: taxableAmount,
        originalSubtotal: originalSubtotal,
        gstAmount: gstAmount,
        totalAmount: totalAmount,
        discountAmount: discountAmount,
        addonsCost: addonsCost
    };
}

function openAddonModalForDish(dishData) {
    currentEditingCartIndex = null;
    currentCustomisingDish = dishData;
    currentSelectedAddons = {};

    let availableAddons = dishData.available_addons || [];
    if (typeof availableAddons === 'string') {
        try { availableAddons = JSON.parse(availableAddons); } catch(e) { availableAddons = []; }
    }
    availableAddons = (availableAddons || []).filter(a => a && a.status !== 'D' && a.status !== 'I' && (!dishData.name || a.name.toLowerCase().trim() !== dishData.name.toLowerCase().trim()));

    $('#custAddonDishTitle').text(dishData.name);
    let basePrice = dishData.discounted_price !== undefined ? parseFloat(dishData.discounted_price) : (dishData.price !== undefined ? parseFloat(dishData.price) : 0);
    $('#custAddonBasePrice').text('Base Price: ₹' + basePrice.toFixed(2));

    let isVeg = (dishData.food_type || 'veg').toLowerCase().includes('veg') && !(dishData.food_type || '').toLowerCase().includes('non');
    $('#custAddonDishSymbol').attr('class', 'fssai-box ' + (isVeg ? 'fssai-veg' : 'fssai-nonveg'));

    let listHtml = '';
    if (availableAddons.length === 0) {
        listHtml = '<div class="text-center py-4 text-muted" style="font-size: 0.85rem;">No add-ons available for this item.</div>';
    } else {
        availableAddons.forEach(a => {
            let aVeg = (a.food_type || 'veg').toLowerCase().includes('veg') && !(a.food_type || '').toLowerCase().includes('non');
            let aPrice = parseFloat(a.price) || 0;
            let isSelected = currentSelectedAddons.hasOwnProperty(a.id);
            let selectedQty = isSelected ? currentSelectedAddons[a.id].qty : 1;

            listHtml += `
                <div class="cust-addon-row ${isSelected ? 'selected' : ''}" data-addon-id="${a.id}" data-addon-name="${escapeHtml(a.name)}" data-addon-price="${aPrice}" data-food-type="${a.food_type || 'Veg'}">
                    <div class="cust-addon-info">
                        <div class="fssai-box ${aVeg ? 'fssai-veg' : 'fssai-nonveg'}" style="width: 14px; height: 14px; padding: 2px;">
                            <div class="fssai-symbol" style="width: 6px; height: 6px;"></div>
                        </div>
                        <div>
                            <div class="cust-addon-name">${escapeHtml(a.name)}</div>
                            <div class="cust-addon-price">+ ₹${aPrice.toFixed(2)}</div>
                        </div>
                    </div>
                    <div class="cust-addon-stepper" style="${isSelected ? 'display:inline-flex;' : 'display:none;'}" id="addonStepper_${a.id}">
                        <button type="button" class="cust-addon-step-btn btn-addon-minus" data-id="${a.id}">−</button>
                        <span class="cust-addon-step-val" id="addonVal_${a.id}">${selectedQty}</span>
                        <button type="button" class="cust-addon-step-btn btn-addon-plus" data-id="${a.id}">+</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary btn-addon-add" id="addonAddBtn_${a.id}" style="${isSelected ? 'display:none;' : ''} border-radius: 9999px; font-weight: 700; font-size: 0.78rem; padding: 4px 12px;">
                        + Add
                    </button>
                </div>
            `;
        });
    }

    $('#custAddonsList').html(listHtml);
    updateCustAddonModalSummary();
    $('#customerAddonModal').css('display', 'flex').hide().fadeIn(180);
}

function openAddonModalForCartItem(cartIndex) {
    currentEditingCartIndex = cartIndex;
    let item = cart[cartIndex];
    if (!item) return;

    let availableAddons = item.available_addons || [];
    if (typeof availableAddons === 'string') {
        try { availableAddons = JSON.parse(availableAddons); } catch(e) { availableAddons = []; }
    }
    availableAddons = (availableAddons || []).filter(a => a && a.status !== 'D' && a.status !== 'I' && (!item.name || a.name.toLowerCase().trim() !== item.name.toLowerCase().trim()));

    currentCustomisingDish = item;
    currentSelectedAddons = {};

    // Pre-populate with existing selected addons on this cart item
    (item.addons || []).forEach(a => {
        currentSelectedAddons[a.id] = {
            id: a.id,
            name: a.name,
            price: parseFloat(a.price) || 0,
            qty: a.qty || a.quantity || 1,
            food_type: a.food_type || 'Veg'
        };
    });

    $('#custAddonDishTitle').text(item.name);
    let basePrice = item.base_price !== undefined ? parseFloat(item.base_price) : parseFloat(item.price);
    $('#custAddonBasePrice').text('Base Price: ₹' + basePrice.toFixed(2));

    let isVeg = (item.food_type || 'veg').toLowerCase().includes('veg') && !(item.food_type || '').toLowerCase().includes('non');
    $('#custAddonDishSymbol').attr('class', 'fssai-box ' + (isVeg ? 'fssai-veg' : 'fssai-nonveg'));

    let listHtml = '';
    if (availableAddons.length === 0) {
        listHtml = '<div class="text-center py-4 text-muted" style="font-size: 0.85rem;">No add-ons available for this item.</div>';
    } else {
        availableAddons.forEach(a => {
            let aVeg = (a.food_type || 'veg').toLowerCase().includes('veg') && !(a.food_type || '').toLowerCase().includes('non');
            let aPrice = parseFloat(a.price) || 0;
            let isSelected = currentSelectedAddons.hasOwnProperty(a.id);
            let selectedQty = isSelected ? currentSelectedAddons[a.id].qty : 1;

            listHtml += `
                <div class="cust-addon-row ${isSelected ? 'selected' : ''}" data-addon-id="${a.id}" data-addon-name="${escapeHtml(a.name)}" data-addon-price="${aPrice}" data-food-type="${a.food_type || 'Veg'}">
                    <div class="cust-addon-info">
                        <div class="fssai-box ${aVeg ? 'fssai-veg' : 'fssai-nonveg'}" style="width: 14px; height: 14px; padding: 2px;">
                            <div class="fssai-symbol" style="width: 6px; height: 6px;"></div>
                        </div>
                        <div>
                            <div class="cust-addon-name">${escapeHtml(a.name)}</div>
                            <div class="cust-addon-price">+ ₹${aPrice.toFixed(2)}</div>
                        </div>
                    </div>
                    <div class="cust-addon-stepper" style="${isSelected ? 'display:inline-flex;' : 'display:none;'}" id="addonStepper_${a.id}">
                        <button type="button" class="cust-addon-step-btn btn-addon-minus" data-id="${a.id}">−</button>
                        <span class="cust-addon-step-val" id="addonVal_${a.id}">${selectedQty}</span>
                        <button type="button" class="cust-addon-step-btn btn-addon-plus" data-id="${a.id}">+</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary btn-addon-add" id="addonAddBtn_${a.id}" style="${isSelected ? 'display:none;' : ''} border-radius: 9999px; font-weight: 700; font-size: 0.78rem; padding: 4px 12px;">
                        + Add
                    </button>
                </div>
            `;
        });
    }

    $('#custAddonsList').html(listHtml);
    updateCustAddonModalSummary();
    $('#customerAddonModal').css('display', 'flex').hide().fadeIn(180);
}

function updateCustAddonModalSummary() {
    if (!currentCustomisingDish) return;
    let basePrice = currentCustomisingDish.base_price !== undefined ? parseFloat(currentCustomisingDish.base_price) : parseFloat(currentCustomisingDish.price);
    let addonTotal = 0;

    Object.values(currentSelectedAddons).forEach(a => {
        addonTotal += a.price * a.qty;
    });

    let finalUnitPrice = basePrice + addonTotal;
    $('#custAddonModalTotalPrice').text('₹' + finalUnitPrice.toFixed(2));
    $('#btnApplyCustAddonsPrice').text('₹' + finalUnitPrice.toFixed(2));
}

// Addon row toggle / Add button
$(document).on('click', '.btn-addon-add', function(e) {
    e.stopPropagation();
    let row = $(this).closest('.cust-addon-row');
    let id = row.data('addon-id');
    let name = row.data('addon-name');
    let price = parseFloat(row.data('addon-price')) || 0;
    let foodType = row.data('food-type');

    currentSelectedAddons[id] = { id: id, name: name, price: price, qty: 1, food_type: foodType };
    row.addClass('selected');
    $(this).hide();
    $(`#addonStepper_${id}`).css('display', 'inline-flex');
    $(`#addonVal_${id}`).text(1);
    updateCustAddonModalSummary();
});

// Stepper Plus
$(document).on('click', '.btn-addon-plus', function(e) {
    e.stopPropagation();
    let id = $(this).data('id');
    if (currentSelectedAddons[id]) {
        currentSelectedAddons[id].qty++;
        $(`#addonVal_${id}`).text(currentSelectedAddons[id].qty);
        updateCustAddonModalSummary();
    }
});

// Stepper Minus
$(document).on('click', '.btn-addon-minus', function(e) {
    e.stopPropagation();
    let id = $(this).data('id');
    if (currentSelectedAddons[id]) {
        if (currentSelectedAddons[id].qty > 1) {
            currentSelectedAddons[id].qty--;
            $(`#addonVal_${id}`).text(currentSelectedAddons[id].qty);
        } else {
            delete currentSelectedAddons[id];
            let row = $(this).closest('.cust-addon-row');
            row.removeClass('selected');
            $(`#addonStepper_${id}`).hide();
            $(`#addonAddBtn_${id}`).show();
        }
        updateCustAddonModalSummary();
    }
});

// Close Modal
$(document).on('click', '#btnCloseCustAddonModal, .customer-addon-modal-overlay', function(e) {
    if (e.target === this || $(this).attr('id') === 'btnCloseCustAddonModal') {
        $('#customerAddonModal').fadeOut(150);
        currentCustomisingDish = null;
        currentSelectedAddons = {};
        currentEditingCartIndex = null;
    }
});

$(document).on('click', '.customer-addon-modal-card', function(e) {
    e.stopPropagation();
});

// Apply Addons
$(document).on('click', '#btnApplyCustAddons', function() {
    let selectedList = Object.values(currentSelectedAddons);

    if (currentEditingCartIndex !== null && cart[currentEditingCartIndex]) {
        let item = cart[currentEditingCartIndex];
        let base = item.base_price !== undefined ? item.base_price : item.price;
        item.addons = selectedList;
        item.price = base;
        refreshTable();
        $('#customerAddonModal').fadeOut(150);
        showTempToast('Updated Add-ons for ' + item.name + ' in tray!', true);
        currentEditingCartIndex = null;
        currentCustomisingDish = null;
        currentSelectedAddons = {};
        return;
    }

    if (currentCustomisingDish) {
        let base = currentCustomisingDish.base_price || currentCustomisingDish.price;
        addDishToCartWithAddons(currentCustomisingDish, selectedList, base);
        $('#customerAddonModal').fadeOut(150);
        showTempToast('Added ' + currentCustomisingDish.name + ' with Add-ons to tray!', true);
        currentCustomisingDish = null;
        currentSelectedAddons = {};
    }
});

// Skip Addons
$(document).on('click', '#btnSkipAddons', function() {
    if (currentEditingCartIndex !== null && cart[currentEditingCartIndex]) {
        let item = cart[currentEditingCartIndex];
        let base = item.base_price !== undefined ? item.base_price : item.price;
        item.addons = [];
        item.price = base;
        refreshTable();
        $('#customerAddonModal').fadeOut(150);
        showTempToast('Removed Add-ons from ' + item.name, true);
        currentEditingCartIndex = null;
        currentCustomisingDish = null;
        currentSelectedAddons = {};
        return;
    }

    if (currentCustomisingDish) {
        let base = currentCustomisingDish.base_price || currentCustomisingDish.price;
        addDishToCartWithAddons(currentCustomisingDish, [], base);
        $('#customerAddonModal').fadeOut(150);
        showTempToast('Added ' + currentCustomisingDish.name + ' to tray!', true);
        currentCustomisingDish = null;
        currentSelectedAddons = {};
    }
});

function addDishToCartWithAddons(dishData, addonsList, unitPrice) {
    let basePrice = dishData.base_price !== undefined ? parseFloat(dishData.base_price) : parseFloat(dishData.price || 0);
    addonsList = Array.isArray(addonsList) ? addonsList : [];

    if (addonsList.length === 0) {
        let existing = cart.find(i => String(i.id) === String(dishData.id) && (!i.addons || i.addons.length === 0));
        if (existing) {
            existing.qty++;
        } else {
            cart.push({
                id: dishData.id,
                name: dishData.name,
                price: basePrice,
                base_price: basePrice,
                qty: 1,
                discount: dishData.discount || 0,
                addons: [],
                food_type: dishData.food_type || 'Veg',
                available_addons: dishData.available_addons || dishData.addons || []
            });
        }
    } else {
        let addonFingerprint = JSON.stringify(addonsList.map(a => ({ id: a.id, qty: a.qty || 1 })).sort((a,b) => a.id - b.id));
        let existing = cart.find(i => {
            if (String(i.id) !== String(dishData.id)) return false;
            let iFp = JSON.stringify((i.addons || []).map(a => ({ id: a.id, qty: a.qty || 1 })).sort((a,b) => a.id - b.id));
            return iFp === addonFingerprint;
        });

        if (existing) {
            existing.qty++;
        } else {
            cart.push({
                id: dishData.id,
                name: dishData.name,
                price: basePrice,
                base_price: basePrice,
                qty: 1,
                discount: dishData.discount || 0,
                addons: addonsList,
                food_type: dishData.food_type || 'Veg',
                available_addons: dishData.available_addons || dishData.addons || []
            });
        }
    }
    refreshTable();
}

function syncCardCounters() {
    // Reset all card counters to default state
    $('.card-active-counter').hide();
    $('.btn-add-tray').show();
    $('.dish-customise-badge').show();

    let dishTotals = {};
    cart.forEach(item => {
        let key = String(item.id);
        dishTotals[key] = (dishTotals[key] || 0) + item.qty;
    });

    Object.keys(dishTotals).forEach(key => {
        let $counter = $(`.card-active-counter[data-card-id="${key}"]`);
        if ($counter.length) {
            let $wrap = $counter.closest('.card-stepper-wrap');
            $wrap.find('.btn-add-tray').hide();
            $wrap.find('.dish-customise-badge').hide();
            $counter.find('.card-counter-val').text(dishTotals[key]);
            $counter.css('display', 'inline-flex');
        }
    });
}

function updateEmptyState() {
    let totalQty = cart.reduce((sum, item) => sum + item.qty, 0);
    $('#topCartBadge').text(totalQty);

    if (cart.length === 0) {
        $('#emptyOrderState').show();
        $('#newItemsTableContainer').hide();
        $('#placeOrderBtn').prop('disabled', true);

        if (hasActiveOrder) {
            $('#floatingItemCount').text('0 New');
            $('#floatingGrandTotal').text(activeOrderTotal.toFixed(2));
            $('#floatingBar').css('transform', 'translateY(0)');
            $('#floatingBarBtnWrap').html(`
                <a href="{{ route('order.details', $activeOrder->id ?? 0) }}" class="btn-place-order-master" style="text-decoration:none;">
                    <i class="fas fa-file-invoice"></i> View Live Status
                </a>
            `);
        } else {
            $('#floatingItemCount').text('0');
            $('#floatingGrandTotal').text('0.00');
            $('#floatingBar').css('transform', 'translateY(100%)');
        }
    } else {
        $('#emptyOrderState').hide();
        $('#newItemsTableContainer').show();
        $('#placeOrderBtn').prop('disabled', false);
        $('#floatingBar').css('transform', 'translateY(0)');

        if (hasActiveOrder) {
            $('#floatingItemCount').text(totalQty + ' New');
            $('#floatingBarBtnWrap').html(`
                <button type="button" class="btn-place-order-master" id="placeOrderBtn">
                    <span><i class="fas fa-plus-circle me-1"></i> Save &amp; Order</span>
                </button>
            `);
        } else {
            $('#floatingItemCount').text(totalQty);
            $('#floatingBarBtnWrap').html(`
                <button type="button" class="btn-place-order-master" id="placeOrderBtn">
                    <span>Confirm Order</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            `);
        }
    }
}

function refreshTable() {
    let tbody = $('#orderItemsBody');
    tbody.html('');
    let originalSubtotal = 0, totalTaxable = 0, totalGst = 0, totalDiscount = 0;

    cart.forEach((item, i) => {
        let details = calculateItemDetails(item);
        originalSubtotal += details.originalSubtotal;
        totalTaxable    += details.taxableAmount;
        totalGst        += details.gstAmount;
        totalDiscount   += details.discountAmount;

        let isAddon = item.is_addon || String(item.id).startsWith('addon_');

        let addonsHtml = '';
        if (!isAddon) {
            let available = item.available_addons || [];
            if (typeof available === 'string') {
                try { available = JSON.parse(available); } catch(e) { available = []; }
            }
            available = (available || []).filter(a => a && a.status !== 'D' && a.status !== 'I' && (!item.name || a.name.toLowerCase().trim() !== item.name.toLowerCase().trim()));

            let hasAvailable = (available && available.length > 0);
            let hasAttached = (item.addons && item.addons.length > 0);

            if (hasAttached) {
                let addonRows = item.addons.map(a => {
                    let aPrice = parseFloat(a.price) || 0;
                    let aQty = a.qty || 1;
                    let aTotal = aPrice * aQty;
                    let aVeg = (a.food_type || 'veg').toLowerCase().includes('veg') && !(a.food_type || '').toLowerCase().includes('non');

                    return `
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 3px 0; border-bottom: 1px dashed rgba(254, 215, 170, 0.6); font-size: 0.74rem;">
                            <div style="display: flex; align-items: center; gap: 4px; min-width: 0;">
                                <span class="fssai-box ${aVeg ? 'fssai-veg' : 'fssai-nonveg'}" style="width: 10px; height: 10px; display: inline-flex; border-width: 1px; flex-shrink: 0;"><span class="fssai-symbol" style="width: 4px; height: 4px;"></span></span>
                                <span style="font-weight: 700; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${escapeHtml(a.name)}</span>
                                <span style="color: var(--text-muted); font-weight: 600; font-size: 0.7rem;">(+₹${aPrice.toFixed(2)})</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 6px; flex-shrink: 0;">
                                <div style="display: inline-flex; align-items: center; background: #ffffff; border: 1px solid #fed7aa; border-radius: 4px; padding: 1px 3px; gap: 3px;">
                                    <button type="button" class="btn-addon-chip-dec" data-cart-index="${i}" data-addon-id="${a.id}" title="Decrease add-on quantity" style="border:none; background:transparent; font-weight:800; color:#ea580c; font-size:0.8rem; cursor:pointer; line-height:1; padding:0 2px;">−</button>
                                    <span style="font-weight: 800; font-size: 0.74rem; color: var(--text-main); min-width: 12px; text-align: center;">${aQty}</span>
                                    <button type="button" class="btn-addon-chip-inc" data-cart-index="${i}" data-addon-id="${a.id}" title="Increase add-on quantity" style="border:none; background:transparent; font-weight:800; color:#ea580c; font-size:0.8rem; cursor:pointer; line-height:1; padding:0 2px;">+</button>
                                </div>
                                <span style="font-weight: 800; color: #ea580c; min-width: 48px; text-align: right;">₹${aTotal.toFixed(2)}</span>
                                <button type="button" class="btn-remove-tray-addon" data-cart-index="${i}" data-addon-id="${a.id}" title="Remove add-on" style="background:transparent; border:none; color:#94a3b8; font-size:0.95rem; line-height:1; cursor:pointer; padding:0 2px;">&times;</button>
                            </div>
                        </div>
                    `;
                }).join('');

                addonsHtml = `
                    <div style="background: #fffaf7; border: 1px solid #fed7aa; border-radius: 8px; padding: 6px 10px; margin-top: 6px; max-width: 380px;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 4px;">
                            <span style="font-size: 0.7rem; font-weight: 800; color: #ea580c; text-transform: uppercase; letter-spacing: 0.04em;">
                                <i class="fas fa-puzzle-piece me-1"></i> Add-ons
                            </span>
                            <button type="button" class="btn-tray-addon-manage openTrayAddonBtn" data-cart-index="${i}" style="font-size: 0.68rem; padding: 1px 7px;">
                                <i class="fas fa-plus"></i> Add more
                            </button>
                        </div>
                        ${addonRows}
                    </div>
                `;
            } else if (hasAvailable) {
                addonsHtml = `
                    <div class="mt-1">
                        <button type="button" class="btn-tray-addon-manage openTrayAddonBtn" data-cart-index="${i}" title="Customize dish with add-ons">
                            <i class="fas fa-plus"></i> Add-on
                        </button>
                    </div>
                `;
            }
        }

        let row = `
            <tr>
                <td>
                    <span class="tray-item-title">${escapeHtml(item.name)}</span>
                    ${addonsHtml}
                </td>
                <td style="text-align:center;">
                    <div class="stepper-pill">
                        <button type="button" class="step-btn decreaseQty" data-index="${i}">−</button>
                        <span class="step-value">${item.qty}</span>
                        <button type="button" class="step-btn increaseQty" data-index="${i}">+</button>
                    </div>
                </td>
                <td>
                    ${item.discount > 0 ? `<div style="text-decoration:line-through;color:var(--text-light);font-size:0.78rem;">₹${details.basePrice.toFixed(2)}</div>` : ''}
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

    if (hasActiveOrder) {
        let combined = activeOrderTotal + grandTotal;
        $('#combined_final_total').text(combined.toFixed(2));
        if (cart.length > 0) {
            $('#floatingGrandTotal').text(combined.toFixed(2));
        } else {
            $('#floatingGrandTotal').text(activeOrderTotal.toFixed(2));
        }
    } else {
        $('#floatingGrandTotal').text(grandTotal.toFixed(2));
    }

    syncCardCounters();
    updateEmptyState();
}

/* Open Customize Addon Modal from Table Row */
$(document).on('click', '.openTrayAddonBtn', function(e) {
    e.preventDefault();
    let cartIndex = parseInt($(this).data('cart-index'));
    if (isNaN(cartIndex) || !cart[cartIndex]) return;
    openAddonModalForCartItem(cartIndex);
});

/* Increment addon qty directly from table row */
$(document).on('click', '.btn-addon-chip-inc', function(e) {
    e.preventDefault();
    e.stopPropagation();
    let cartIndex = parseInt($(this).data('cart-index'));
    let addonId = $(this).data('addon-id');
    if (!isNaN(cartIndex) && cart[cartIndex] && cart[cartIndex].addons) {
        let addon = cart[cartIndex].addons.find(a => String(a.id) === String(addonId));
        if (addon) {
            addon.qty = (addon.qty || 1) + 1;
            let base = cart[cartIndex].base_price !== undefined ? cart[cartIndex].base_price : cart[cartIndex].price;
            cart[cartIndex].price = base;
            refreshTable();
        }
    }
});

/* Decrement addon qty directly from table row */
$(document).on('click', '.btn-addon-chip-dec', function(e) {
    e.preventDefault();
    e.stopPropagation();
    let cartIndex = parseInt($(this).data('cart-index'));
    let addonId = $(this).data('addon-id');
    if (!isNaN(cartIndex) && cart[cartIndex] && cart[cartIndex].addons) {
        let addon = cart[cartIndex].addons.find(a => String(a.id) === String(addonId));
        if (addon) {
            let currentQty = addon.qty || 1;
            if (currentQty > 1) {
                addon.qty = currentQty - 1;
            } else {
                cart[cartIndex].addons = cart[cartIndex].addons.filter(a => String(a.id) !== String(addonId));
            }
            let base = cart[cartIndex].base_price !== undefined ? cart[cartIndex].base_price : cart[cartIndex].price;
            cart[cartIndex].price = base;
            refreshTable();
        }
    }
});

/* Remove Single Addon from Table Row Chip */
$(document).on('click', '.btn-remove-tray-addon', function(e) {
    e.preventDefault();
    e.stopPropagation();
    let cartIndex = parseInt($(this).data('cart-index'));
    let addonId = $(this).data('addon-id');
    if (!isNaN(cartIndex) && cart[cartIndex] && cart[cartIndex].addons) {
        cart[cartIndex].addons = cart[cartIndex].addons.filter(a => String(a.id) !== String(addonId));
        let base = cart[cartIndex].base_price !== undefined ? cart[cartIndex].base_price : cart[cartIndex].price;
        cart[cartIndex].price = base;
        refreshTable();
    }
});

/* Add standalone addon button */
$(document).on('click', '.addStandaloneAddonBtn, .increaseStandaloneAddonQty', function(e) {
    e.preventDefault();
    let addonId  = $(this).data('id');
    let name     = $(this).data('name');
    let price    = parseFloat($(this).data('price')) || 0;
    let foodType = $(this).data('food-type') || 'Veg';
    let cartKey  = 'addon_' + addonId;

    let existing = cart.find(i => i.id === cartKey || (i.is_addon && i.addon_id == addonId));
    if (existing) {
        existing.qty++;
        showTempToast(name + ' quantity increased to ' + existing.qty, true);
    } else {
        cart.push({
            id: cartKey,
            name: name,
            price: price,
            base_price: price,
            qty: 1,
            discount: 0,
            is_addon: true,
            addon_id: addonId,
            food_type: foodType,
            addons: [{
                id: addonId,
                name: name,
                price: price,
                qty: 1,
                quantity: 1,
                food_type: foodType
            }]
        });
        showTempToast('Added ' + name + ' to tray!', true);
    }
    refreshTable();
});

/* Add to cart from dish card */
$(document).on('click', '.addItemBtn', function(e) {
    e.preventDefault();
    e.stopPropagation();
    let itemId       = $(this).data('id');
    let rawAddons    = $(this).data('addons');
    let addons       = (typeof rawAddons === 'string') ? JSON.parse(rawAddons || '[]') : (rawAddons || []);
    let basePrice    = parseFloat($(this).data('price'));
    let discPrice    = parseFloat($(this).data('discounted-price')) || basePrice;
    let discount     = parseFloat($(this).data('discount')) || 0;
    let name         = $(this).data('name');
    let foodType     = $(this).data('food-type') || 'Veg';

    let existing = cart.find(i => String(i.id) === String(itemId) && (!i.addons || i.addons.length === 0));
    if (existing) {
        existing.qty++;
    } else {
        cart.push({
            id: itemId,
            name: name,
            price: basePrice,
            base_price: basePrice,
            qty: 1,
            discount: discount,
            food_type: foodType,
            available_addons: addons,
            addons: []
        });
    }
    refreshTable();
    showTempToast('Added ' + name + ' to tray!', true);
});

/* Open Addon Customization Modal from Dish Card */
$(document).on('click', '.openAddonCustomiseBtn', function(e) {
    e.preventDefault();
    let itemId       = $(this).data('id');
    let rawAddons    = $(this).data('addons');
    let addons       = (typeof rawAddons === 'string') ? JSON.parse(rawAddons || '[]') : (rawAddons || []);
    let basePrice    = parseFloat($(this).data('price'));
    let discPrice    = parseFloat($(this).data('discounted-price')) || basePrice;
    let discount     = parseFloat($(this).data('discount')) || 0;
    let name         = $(this).data('name');
    let foodType     = $(this).data('food-type') || 'Veg';

    let dishData = {
        id: itemId,
        name: name,
        price: basePrice,
        discounted_price: discPrice,
        base_price: basePrice,
        discount: discount,
        food_type: foodType,
        available_addons: addons,
        addons: []
    };

    openAddonModalForDish(dishData);
});

/* Card counter buttons */
$(document).on('click', '.increaseCardQty', function(e) {
    e.preventDefault();
    let itemId = String($(this).data('id'));
    let existing = cart.find(i => String(i.id) === itemId);
    if (existing) {
        existing.qty++;
        refreshTable();
    } else {
        let rawAddons = $(this).data('addons');
        let addons = (typeof rawAddons === 'string') ? JSON.parse(rawAddons || '[]') : (rawAddons || []);
        let basePrice = parseFloat($(this).data('price'));
        let discPrice = parseFloat($(this).data('discounted-price')) || basePrice;
        let discount = parseFloat($(this).data('discount')) || 0;
        let name = $(this).data('name');
        let foodType = $(this).data('food-type') || 'Veg';

        cart.push({
            id: itemId,
            name: name,
            price: basePrice,
            base_price: basePrice,
            qty: 1,
            discount: discount,
            food_type: foodType,
            available_addons: addons,
            addons: []
        });
        refreshTable();
    }
});

$(document).on('click', '.decreaseCardQty', function(e) {
    e.preventDefault();
    let itemId = String($(this).data('id'));
    let idx = cart.findIndex(i => String(i.id) === itemId);
    if (idx > -1) {
        if (cart[idx].qty > 1) {
            cart[idx].qty--;
        } else {
            cart.splice(idx, 1);
        }
        refreshTable();
    }
});

/* Tray table qty controls */
$(document).on('click', '.increaseQty', function(e) {
    e.preventDefault();
    let idx = $(this).data('index');
    if (cart[idx]) {
        cart[idx].qty++;
        refreshTable();
    }
});

$(document).on('click', '.decreaseQty', function(e) {
    e.preventDefault();
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

$(document).on('click', '.removeItem', function(e) {
    e.preventDefault();
    let idx = $(this).data('index');
    cart.splice(idx, 1);
    refreshTable();
});

/* Live Filter & Search Logic */
let selectedCategory = 'all';

function updateCategoryCounts() {
    let activeDiet = ($('.diet-btn.active').attr('data-type') !== undefined ? $('.diet-btn.active').attr('data-type') : ($('.diet-btn.active').data('type') || '')).toLowerCase().trim();
    let searchVal = ($('#searchBox').val() || '').toLowerCase().trim();

    let totalAllCount = 0;
    $('.category-group-block').each(function() {
        let catId = String($(this).attr('data-category-id') || $(this).data('category-id') || '');
        let countInCat = 0;

        $(this).find('.food-card-wrapper').each(function() {
            let name = String($(this).attr('data-name') || '').toLowerCase();
            let desc = String($(this).attr('data-desc') || '').toLowerCase();
            let rawFoodType = String($(this).attr('data-type') || '').toLowerCase().trim();
            let isCardVeg = !['non-veg', 'non_veg', 'non veg', 'nonveg', 'egg'].includes(rawFoodType);
            let cardFoodType = isCardVeg ? 'veg' : 'non-veg';

            let nameMatches = (searchVal === '') || name.includes(searchVal) || desc.includes(searchVal);
            let typeMatches = (activeDiet === '' || activeDiet === 'all') ? true : (cardFoodType === activeDiet);

            if (nameMatches && typeMatches) {
                countInCat++;
                totalAllCount++;
            }
        });

        $(`.cat-pill-tab[data-category="${catId}"] .cat-count-pill`).text(countInCat);
    });

    $(`.cat-pill-tab[data-category="all"] .cat-count-pill`).text(totalAllCount);
}

function applyFilters() {
    let searchVal = ($('#searchBox').val() || '').toLowerCase().trim();
    let $activeDietBtn = $('.diet-btn.active');
    let activeType = ($activeDietBtn.attr('data-type') !== undefined ? $activeDietBtn.attr('data-type') : ($activeDietBtn.data('type') || '')).toLowerCase().trim();

    // Toggle clear search button
    if (searchVal.length > 0) {
        $('#clearSearchBtn').show();
    } else {
        $('#clearSearchBtn').hide();
    }

    let isSearching = searchVal.length > 0;
    let totalVisible = 0;

    $('.category-group-block').each(function() {
        let catId = String($(this).attr('data-category-id') || $(this).data('category-id') || '');

        let catMatch = isSearching || (selectedCategory === 'all') || (catId === String(selectedCategory));

        if (!catMatch) {
            $(this).hide();
            return;
        }

        let visibleInBlock = 0;

        $(this).find('.food-card-wrapper').each(function() {
            let name = String($(this).attr('data-name') || '').toLowerCase();
            let desc = String($(this).attr('data-desc') || '').toLowerCase();
            let rawFoodType = String($(this).attr('data-type') || '').toLowerCase().trim();

            let isCardVeg = !['non-veg', 'non_veg', 'non veg', 'nonveg', 'egg'].includes(rawFoodType);
            let cardFoodType = isCardVeg ? 'veg' : 'non-veg';

            let nameMatches = (searchVal === '') || name.includes(searchVal) || desc.includes(searchVal);
            let typeMatches = (activeType === '' || activeType === 'all') ? true : (cardFoodType === activeType);

            if (nameMatches && typeMatches) {
                $(this).show();
                visibleInBlock++;
                totalVisible++;
            } else {
                $(this).hide();
            }
        });

        // Show/hide category group block based on whether any matching dish is in it
        $(this).toggle(visibleInBlock > 0);
    });

    $('#noResultsState').toggle(totalVisible === 0);
    updateCategoryCounts();
}

// 1. Click Category Pill
$(document).on('click', '.cat-pill-tab', function(e) {
    e.preventDefault();
    $('.cat-pill-tab').removeClass('active');
    $(this).addClass('active');

    selectedCategory = $(this).attr('data-category') || 'all';

    applyFilters();

    // Scroll smoothly to category block if selecting a specific category
    if (selectedCategory !== 'all') {
        let $targetBlock = $(`.category-group-block[data-category-id="${selectedCategory}"]`);
        if ($targetBlock.length > 0) {
            $('html, body').animate({
                scrollTop: $targetBlock.offset().top - 120
            }, 250);
        }
    }
});

// 2. Click Dietary Filter (All Items / Veg / Non-Veg)
$(document).on('click', '.diet-btn', function(e) {
    e.preventDefault();
    let btnType = $(this).attr('data-type') !== undefined ? $(this).attr('data-type') : ($(this).data('type') || '');
    let wasActive = $(this).hasClass('active');

    if (btnType === '') {
        $('.diet-btn').removeClass('active');
        $(this).addClass('active');
        selectedCategory = 'all';
        $('.cat-pill-tab').removeClass('active');
        $('.cat-pill-tab[data-category="all"]').addClass('active');
    } else {
        if (wasActive) {
            $(this).removeClass('active');
            $('.diet-btn[data-type=""]').addClass('active');
        } else {
            $('.diet-btn').removeClass('active');
            $(this).addClass('active');
        }
    }

    applyFilters();
});

// 3. Search Box Input
$('#searchBox').on('input', function() {
    let searchVal = $(this).val().toLowerCase().trim();
    if (searchVal.length > 0 && selectedCategory !== 'all') {
        selectedCategory = 'all';
        $('.cat-pill-tab').removeClass('active');
        $('.cat-pill-tab[data-category="all"]').addClass('active');
    }
    applyFilters();
});

$('#clearSearchBtn').on('click', function() {
    $('#searchBox').val('').focus();
    applyFilters();
});

function showTempToast(msg, isSuccess = true) {
    const toast = document.getElementById('tempToast');
    const msgEl = document.getElementById('tempToastMsg');
    if (!toast || !msgEl) return;
    msgEl.textContent = msg;
    toast.querySelector('i').className = isSuccess ? 'fas fa-check-circle text-success' : 'fas fa-exclamation-triangle text-danger';
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 3200);
}

// Toggle Active Order Items view
$('#toggleActiveItemsBtn').on('click', function() {
    let $content = $('#activeItemsCollapsible');
    let isVisible = $content.is(':visible');
    $content.slideToggle(200);
    $('#toggleActiveText').text(isVisible ? 'Show Dishes' : 'Hide Dishes');
    $('#toggleActiveIcon').attr('class', isVisible ? 'fas fa-chevron-down' : 'fas fa-chevron-up');
});

// Cancel a PENDING item from active table order
function cancelActiveOrderItem(itemId) {
    if (!confirm('Are you sure you want to cancel this pending dish?')) {
        return;
    }

    let $card = $('#activeCard_' + itemId);
    let $btn = $card.find('.btn-cancel-active-dish');
    let origHtml = $btn.html();
    $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

    $.post("{{ url('/order-customer/item/delete') }}/" + itemId, {
        _token: "{{ csrf_token() }}"
    }, function(res) {
        if (res.status) {
            showTempToast(res.message || 'Item cancelled successfully.');
            $card.fadeOut(300, function() {
                $card.remove();
                setTimeout(function() {
                    window.location.reload();
                }, 500);
            });
        } else {
            alert(res.message || 'Could not cancel item.');
            $btn.html(origHtml).prop('disabled', false);
        }
    }).fail(function(xhr) {
        let msg = 'Could not cancel item.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
        }
        alert(msg);
        $btn.html(origHtml).prop('disabled', false);
    });
}

/* Place Order / Save & Order Submission */
$(document).on('click', '#placeOrderBtn', function() {
    if (cart.length === 0) {
        alert('Please add at least one dish or add-on to your order tray.');
        return;
    }

    let activeOrderId = $('#active_order_id').val();
    let isAddOrder = !!activeOrderId;
    let name  = $('#customer_name').val().trim();
    let phone = $('#phone').val().trim();

    if (!name) {
        alert('Please enter your full name before confirming.');
        $('html, body').animate({ scrollTop: $('.guest-info-card').offset().top - 80 }, 400);
        $('#customer_name').focus();
        return;
    }

    if (!phone) {
        alert('Please enter your 10-digit mobile phone number.');
        $('html, body').animate({ scrollTop: $('.guest-info-card').offset().top - 80 }, 400);
        $('#phone').focus();
        return;
    }

    if (!/^[0-9]{10}$/.test(phone)) {
        alert('Mobile phone number must be exactly 10 digits.');
        $('html, body').animate({ scrollTop: $('.guest-info-card').offset().top - 80 }, 400);
        $('#phone').focus();
        return;
    }

    let orderItems = cart.map(item => ({
        id: item.id,
        name: item.name,
        price: item.base_price !== undefined ? item.base_price : item.price,
        qty: item.qty,
        item_discount: item.discount || 0,
        is_addon: item.is_addon || false,
        addons: item.addons || []
    }));

    let $btn = $(this);
    $btn.html(isAddOrder ? '<i class="fas fa-spinner fa-spin"></i> Saving &amp; Ordering...' : '<i class="fas fa-spinner fa-spin"></i> Processing Order...').prop('disabled', true);

    let submitUrl = isAddOrder ? "{{ route('temp.order.add_items') }}" : "{{ route('temp.order.store') }}";
    let postData = {
        _token:          "{{ csrf_token() }}",
        customer_name:   name,
        customer_phone:  phone,
        table_id:        $('#table_id').val(),
        restaurant_id:   $('#restaurant_id').val(),
        order_items:     orderItems
    };
    if (isAddOrder) {
        postData.order_id = activeOrderId;
    }

    $.post(submitUrl, postData, function(res) {
        if (res.status) {
            if (res.order_id) {
                try { localStorage.setItem('customer_qr_order_id', res.order_id); } catch(e) {}
            }
            window.location.href = res.redirect;
        } else {
            alert(res.message || 'Something went wrong while placing your order. Please try again.');
            $btn.html(isAddOrder ? '<span>Save &amp; Order</span> <i class="fas fa-plus-circle"></i>' : '<span>Confirm Order</span> <i class="fas fa-arrow-right"></i>').prop('disabled', false);
        }
    }).fail(function(xhr) {
        let msg = 'Network connection error. Please check your internet and try again.';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
        }
        alert(msg);
        $btn.html(isAddOrder ? '<span>Save &amp; Order</span> <i class="fas fa-plus-circle"></i>' : '<span>Confirm Order</span> <i class="fas fa-arrow-right"></i>').prop('disabled', false);
    });
});

@if(isset($activeOrder) && $activeOrder)
// Manual on-demand status refresh (no continuous background polling)
let activeOrderId = {{ $activeOrder->id }};

function manualRefreshStatus() {
    let $btns = $('.btn-refresh-status');
    let $icons = $('.btn-refresh-status i');
    $icons.addClass('fa-spin');
    $btns.prop('disabled', true);

    $.get("{{ route('order.status.check', ':id') }}".replace(':id', activeOrderId) + '?type=main', function(res) {
        $icons.removeClass('fa-spin');
        $btns.prop('disabled', false);

        if (res.is_completed) {
            showTempToast('Dining completed by restaurant! Refreshing...', true);
            setTimeout(function() {
                window.location.reload();
            }, 1200);
            return;
        }

        if (res.status && res.items && Array.isArray(res.items)) {
            res.items.forEach(function(itm) {
                let $pill = $('#activeStatusPill_' + itm.id);
                let $actions = $('#activeItemActions_' + itm.id);
                let $kotBadge = $('#activeKotBadge_' + itm.id);
                let $priceEl = $('#activeItemPrice_' + itm.id);

                if ($kotBadge.length && itm.kot_no) {
                    $kotBadge.html('<i class="fas fa-receipt"></i> ' + itm.kot_no);
                }

                if ($priceEl.length && itm.total !== undefined) {
                    $priceEl.text('₹' + parseFloat(itm.total).toFixed(2));
                }

                if ($pill.length) {
                    let statusLower = itm.order_status.toLowerCase();
                    $pill.attr('class', 'dish-status-pill status-' + statusLower);
                    if (itm.order_status === 'COOKING') {
                        $pill.html('<i class="fas fa-fire-burner"></i> Cooking');
                    } else if (itm.order_status === 'DONE') {
                        $pill.html('<i class="fas fa-check-circle"></i> Cooked');
                    } else {
                        $pill.html('<i class="fas fa-hourglass-half"></i> Pending');
                    }
                }

                if ($actions.length) {
                    if (itm.order_status === 'PENDING') {
                        $actions.html(`<button type="button" class="btn-cancel-active-dish" onclick="cancelActiveOrderItem(${itm.id})" title="Cancel pending dish"><i class="fas fa-trash-alt"></i></button>`);
                    } else {
                        $actions.html('');
                    }
                }
            });
            showTempToast('Kitchen status updated successfully!', true);
        }
    }).fail(function() {
        $icons.removeClass('fa-spin');
        $btns.prop('disabled', false);
        showTempToast('Could not fetch latest status. Please try again.', false);
    });
}

$(document).on('click', '.btn-refresh-status', function(e) {
    e.preventDefault();
    manualRefreshStatus();
});
@endif

$(document).ready(function() {
    updateEmptyState();
    applyFilters();
});
</script>
</body>
</html>