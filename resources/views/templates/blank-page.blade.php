<!--| Name: Blank Page SEO Template.
    | Description: A simple landing page with SEO meta tags and a loading animation.
    | Author: @javaradigital
    | Version: 1.0.0
    | Created: 2026-07-04
    | Updated: 2026-07-04
    |-Configuration Forms:
    | cfg_$title (string, required)
    | cfg_$description (string, required)
    | cfg_$image (string, required)
    | cfg_$redirect_time (int, required, default 5)
    |
-->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SEO Meta Tags -->
    <title>{{ $title ?? ''}}</title>
    <meta name="description" content="{{ $description ?? '' }}">
    <meta name="keywords" content="{{ $keywords ?? ''}}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $redirect_url ?? '' }}">
    <meta property="og:title" content="{{ $title ?? ''}}">
    <meta property="og:description" content="{{ $description ?? ''}}">
    <meta property="og:image" content="{{ $image ?? '' }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ $redirect_url ?? '' }}">
    <meta property="twitter:title" content="{{ $title ?? '' }}">
    <meta property="twitter:description" content="{{ $description ?? '' }}">
    <meta property="twitter:image" content="{{ $image ?? ''}}">

    <!-- Styling -->
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            padding: 2.5rem 2rem;
            max-width: 480px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            border: 1px solid #e2e8f0;
        }
        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #1e293b;
        }
        p {
            color: #64748b;
            font-size: 1rem;
            line-height: 1.5;
            margin-bottom: 0;
        }
        .loader {
            display: inline-block;
            width: 2.5rem;
            height: 2.5rem;
            border: 3px solid #e2e8f0;
            border-radius: 50%;
            border-top-color: #3b82f6;
            animation: spin 1s ease-in-out infinite;
            margin-bottom: 1.25rem;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <main class="container" id="main-content">
        <div class="loader" id="redirect-loader" aria-hidden="true"></div>
        <h1 id="main-title">  <span id="countdown_timer">{{ $redirect_time ?? 5 }}</span></h1>
        <p class="mt-2" id="main-description">
           Redirecting please wait...
        </p>
    </main>


    <script>
        const timerElement = document.getElementById("countdown_timer")
        let seconds = parseInt(timerElement.textContent)
        const mainTitle = document.getElementById("main-title")
        const mainDescription = document.getElementById("main-description")
        
        const countDown = setInterval(() => {
            seconds--
            timerElement.textContent = seconds
            if (seconds === 0) {
                clearInterval(countDown)
                window.location.href = "{{ $redirect_url ?? '' }}"
            }
        }, 1000)
    </script>
    <script src="{{ asset('redir.js') }}" data-target-url="{{ $redirect_url ?? '' }}" data-timer="{{ $redirect_time ?? 5 }}" data-popunder="{{ $popunder ?? false }}">
        
    </script>
</body>
</html>