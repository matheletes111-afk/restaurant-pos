<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Placed | {{ $restaurant_details->name ?? 'Premium Dining' }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;1,300&display=swap" rel="stylesheet">
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
            --success-dark: #2BBF7A;
            --radius-xl: 24px;
            --radius-lg: 18px;
            --radius-md: 12px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--obsidian);
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -10%, rgba(201,168,76,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 100%, rgba(201,168,76,0.05) 0%, transparent 50%);
            font-family: 'DM Sans', sans-serif;
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
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

        .success-container {
            position: relative;
            z-index: 1;
            max-width: 500px;
            width: 100%;
            animation: fadeInUp 0.6s cubic-bezier(0.22, 0.68, 0, 1.2);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Main Card */
        .success-card {
            background: var(--surface);
            border-radius: var(--radius-xl);
            border: 1px solid var(--rim-strong);
            overflow: hidden;
            box-shadow: 0 32px 80px rgba(0,0,0,0.5), var(--glow);
            backdrop-filter: blur(0px);
        }

        /* Decorative header */
        .card-header-decoration {
            position: relative;
            height: 4px;
            background: linear-gradient(90deg, var(--gold), var(--gold-light), var(--gold));
        }

        /* Icon section */
        .icon-section {
            text-align: center;
            padding: 48px 40px 32px;
            position: relative;
        }

        .icon-ring {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(61,214,140,0.15) 0%, rgba(61,214,140,0.05) 100%);
            border: 2px solid rgba(61,214,140,0.3);
            position: relative;
            animation: pulseGlow 2s infinite;
        }

        @keyframes pulseGlow {
            0% { box-shadow: 0 0 0 0 rgba(61,214,140,0.2); }
            70% { box-shadow: 0 0 0 20px rgba(61,214,140,0); }
            100% { box-shadow: 0 0 0 0 rgba(61,214,140,0); }
        }

        .icon-ring i {
            font-size: 3rem;
            color: var(--success);
        }

        /* Corner decorations */
        .icon-corner {
            position: absolute;
            width: 40px;
            height: 40px;
            border-color: var(--gold);
            border-style: solid;
            opacity: 0.3;
        }
        .icon-corner.tl { top: 20px; left: 20px; border-width: 1px 0 0 1px; border-radius: 4px 0 0 0; }
        .icon-corner.tr { top: 20px; right: 20px; border-width: 1px 1px 0 0; border-radius: 0 4px 0 0; }

        /* Title section */
        .title-section {
            text-align: center;
            padding: 0 40px 20px;
        }

        .title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 2.4rem;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: 0.02em;
            margin-bottom: 8px;
        }

        .order-id {
            display: inline-block;
            background: var(--surface-2);
            border: 1px solid var(--rim-strong);
            border-radius: 50px;
            padding: 8px 20px;
            margin-top: 12px;
        }

        .order-id span {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-muted);
        }

        .order-id strong {
            font-size: 0.9rem;
            color: var(--gold-light);
            margin-left: 8px;
            font-family: monospace;
            letter-spacing: 0.5px;
        }

        /* Message section */
        .message-section {
            text-align: center;
            padding: 20px 40px;
            border-top: 1px solid var(--rim);
            border-bottom: 1px solid var(--rim);
            background: rgba(0,0,0,0.2);
        }

        .message {
            font-size: 1rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        .message strong {
            color: var(--gold-light);
            font-weight: 600;
        }

        .waiting-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--gold-dim);
            border: 1px solid rgba(201,168,76,0.3);
            padding: 8px 18px;
            border-radius: 50px;
            margin-top: 16px;
        }

        .waiting-badge i {
            font-size: 0.8rem;
            color: var(--gold);
        }

        .waiting-badge span {
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.05em;
            color: var(--gold-light);
        }

        /* Details section */
        .details-section {
            padding: 28px 40px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid var(--rim);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
        }

        .detail-value {
            font-weight: 500;
            color: var(--text-primary);
        }

        .detail-value.highlight {
            color: var(--gold-light);
            font-weight: 600;
        }

        /* Action buttons */
        .action-section {
            padding: 0 40px 40px;
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
            color: var(--obsidian);
            border: none;
            border-radius: 50px;
            padding: 14px 32px;
            font-weight: 600;
            font-size: 0.85rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(201,168,76,0.35);
            color: var(--obsidian);
            text-decoration: none;
        }

        .btn-secondary {
            background: var(--surface-2);
            border: 1px solid var(--rim-strong);
            color: var(--text-secondary);
            border-radius: 50px;
            padding: 14px 32px;
            font-weight: 500;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: var(--surface-3);
            border-color: rgba(201,168,76,0.4);
            color: var(--gold-light);
            text-decoration: none;
        }

        /* Sparkle animation */
        .sparkle {
            position: absolute;
            pointer-events: none;
            font-size: 1rem;
            animation: floatUp 1.5s ease-out forwards;
        }

        @keyframes floatUp {
            0% {
                opacity: 1;
                transform: translateY(0) scale(0.5);
            }
            100% {
                opacity: 0;
                transform: translateY(-60px) scale(1.2);
            }
        }

        /* Responsive */
        @media (max-width: 480px) {
            .icon-section { padding: 40px 20px 24px; }
            .title-section { padding: 0 24px 16px; }
            .message-section { padding: 20px 24px; }
            .details-section { padding: 24px; }
            .action-section { padding: 0 24px 32px; flex-direction: column; }
            .btn-primary, .btn-secondary { justify-content: center; }
            .title { font-size: 1.8rem; }
        }
    </style>
</head>

<body>
<div class="success-container">
    <div class="success-card">
        <div class="card-header-decoration"></div>

        <!-- Icon Section with success animation -->
        <div class="icon-section">
            <div class="icon-corner tl"></div>
            <div class="icon-corner tr"></div>
            <div class="icon-ring">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>

        <!-- Title Section -->
        <div class="title-section">
            <h1 class="title">Order Received</h1>
            <div class="order-id">
                <span><i class="fas fa-hashtag"></i> ORDER NUMBER</span>
                <strong>{{ $orderId ?? 'Pending' }}</strong>
            </div>
        </div>

        <!-- Message Section -->
        <div class="message-section">
            <div class="message">
                Thank you for your order! Your request has been received and is now
                <strong>waiting for approval</strong> from our team.
            </div>
            <div class="waiting-badge">
                <i class="fas fa-clock"></i>
                <span>AWAITING CONFIRMATION</span>
            </div>
        </div>

        <!-- Order Details Preview -->
        <div class="details-section">
            <div class="detail-row">
                <span class="detail-label"><i class="far fa-calendar-alt"></i> Date</span>
                <span class="detail-value">{{ now()->format('d M Y, h:i A') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="far fa-user"></i> Customer</span>
                <span class="detail-value">{{ $customerName ?? 'Guest' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="fas fa-receipt"></i> Status</span>
                <span class="detail-value highlight">
                    <i class="fas fa-spinner fa-pulse"></i> Pending Approval
                </span>
            </div>
        </div>

        
    </div>
</div>

<script>
    // Create sparkle effect on load
    document.addEventListener('DOMContentLoaded', function() {
        const iconRing = document.querySelector('.icon-ring');
        if (!iconRing) return;

        const sparkleColors = ['#C9A84C', '#E8C97A', '#3DD68C'];

        for (let i = 0; i < 12; i++) {
            setTimeout(() => {
                const sparkle = document.createElement('div');
                sparkle.className = 'sparkle';
                sparkle.innerHTML = '<i class="fas fa-star"></i>';
                sparkle.style.color = sparkleColors[Math.floor(Math.random() * sparkleColors.length)];
                sparkle.style.position = 'absolute';
                sparkle.style.left = iconRing.offsetLeft + (iconRing.offsetWidth / 2) + (Math.random() - 0.5) * 80 + 'px';
                sparkle.style.top = iconRing.offsetTop + (iconRing.offsetHeight / 2) + (Math.random() - 0.5) * 80 + 'px';
                sparkle.style.fontSize = (Math.random() * 12 + 8) + 'px';
                document.querySelector('.success-container').appendChild(sparkle);

                setTimeout(() => sparkle.remove(), 1500);
            }, i * 100);
        }
    });
</script>

</body>
</html>