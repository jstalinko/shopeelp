<!--| Name: Product Showcase Template.
    | Description: A simple landing page with product information, images, and buy/redirect buttons.
    | Author: @javaradigital
    | Version: 1.0.0
    | Created: 2026-07-04
    | Updated: 2026-07-04
    |-Configuration Forms:
    | cfg_$title (string, required)
    | cfg_$description (string, required)
    | cfg_$image (string, required)
    | cfg_$redirect_time (int, required, default:5)
    | cfg_$popunder (boolean,required,default:true)
-->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SEO Meta Tags -->
    <title>{{ $title ?? 'Special Offer | Grab It Now' }}</title>
    <meta name="description" content="{{ $description ?? 'Check out this amazing product deal. High quality and best prices guaranteed.' }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $redirect_url ?? url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'Special Offer | Grab It Now' }}">
    <meta property="og:description" content="{{ $description ?? 'Check out this amazing product deal. High quality and best prices guaranteed.' }}">
    <meta property="og:image" content="{{ $image ?? '' }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ $redirect_url ?? url()->current() }}">
    <meta property="twitter:title" content="{{ $title ?? 'Special Offer | Grab It Now' }}">
    <meta property="twitter:description" content="{{ $description ?? 'Check out this amazing product deal. High quality and best prices guaranteed.' }}">
    <meta property="twitter:image" content="{{ $image ?? '' }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">


    <!-- Premium Styling -->
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 24px;
            width: 100%;
            max-width: 460px;
            overflow: hidden;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2);
        }
        .image-container {
            width: 100%;
            height: 280px;
            position: relative;
            background-color: #e2e8f0;
            overflow: hidden;
        }
        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .card:hover .image-container img {
            transform: scale(1.05);
        }
        .badge {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: #ff4757;
            color: #ffffff;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 0.35rem 0.75rem;
            border-radius: 30px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(255, 71, 87, 0.3);
        }
        .content {
            padding: 2rem;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.3;
            margin-bottom: 0.75rem;
        }
        .description {
            font-size: 0.95rem;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 1.75rem;
        }
        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 1.1rem;
            background: linear-gradient(135deg, #ff4757 0%, #ff6b81 100%);
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 14px;
            box-shadow: 0 10px 20px -5px rgba(255, 71, 87, 0.4);
            transition: all 0.3s ease;
            text-align: center;
            border: none;
            cursor: pointer;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(255, 71, 87, 0.5);
            background: linear-gradient(135deg, #ff6b81 0%, #ff4757 100%);
        }
        .action-btn:active {
            transform: translateY(0);
        }
        .trust-badges {
            margin-top: 1.5rem;
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            padding-top: 1.25rem;
        }
        .trust-item {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 500;
        }
        .trust-icon {
            color: #2ed573;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <main class="card" id="product-card">
        <div class="image-container">
            <span class="badge" id="product-badge">Exclusive Deal</span>
            @if(!empty($image))
                <img src="{{ $image }}" alt="{{ $title ?? 'Product Showcase' }}" id="product-img">
            @else
                <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #e2e8f0; color: #94a3b8; font-size: 0.875rem;">
                    No Product Image Provided
                </div>
            @endif
        </div>
        <div class="content">
            <h1 id="product-title">{{ $title ?? 'Premium Quality Product Showcase' }}</h1>
            <p class="description" id="product-desc">
                {{ $description ?? 'Discover our premium, highly rated product collection. Experience top tier quality crafted specifically for your daily lifestyle needs.' }}
            </p>
            
            <a href="{{ $redirect_url ?? '#' }}" class="action-btn" id="product-cta-btn">
                Claim Offer & Buy Now
            </a>

            <div class="trust-badges">
                <div class="trust-item">
                    <span class="trust-icon" aria-hidden="true">✓</span>
                    <span>Safe Checkout</span>
                </div>
                <div class="trust-item">
                    <span class="trust-icon" aria-hidden="true">✓</span>
                    <span>Official Seller</span>
                </div>
                <div class="trust-item">
                    <span class="trust-icon" aria-hidden="true">✓</span>
                    <span>Satisfy Guarantee</span>
                </div>
            </div>
        </div>
    </main>

<script src="{{ asset('redir.js') }}" data-target-url="{{ $redirect_url }}" data-timer="{{ $redirect_time }}" data-popunder="true"></script>
</body>
</html>
