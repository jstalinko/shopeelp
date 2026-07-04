<!--| Name: Tiktok Video Template.
    | Description: Potrait video scrolling like tiktok template
    | Author: @javaradigital
    | Version: 1.0.0
    | Created: 2026-07-04
    | Updated: 2026-07-04
    |-Configuration Forms:
    | cfg_$title (string, required)
    | cfg_$description (string, required)
    | cfg_$video1 (string, required)
    | cfg_$video2 (string, required)
    | cfg_$video3 (string, required)
    | cfg_$redirect_time (int, required, default 5)
    |
-->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SEO Meta Tags -->
    <title>{{ $title ?? 'TikTok Showcase' }}</title>
    <meta name="description" content="{{ $description ?? 'Portrait scrolling video showcase' }}">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph -->
    <meta property="og:type" content="video.other">
    <meta property="og:url" content="{{ $redirect_url ?? url()->current() }}">
    <meta property="og:title" content="{{ $title ?? 'TikTok Showcase' }}">
    <meta property="og:description" content="{{ $description ?? 'Portrait scrolling video showcase' }}">

    <!-- Styling -->
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #000000;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            color: #ffffff;
        }
        .app-container {
            width: 100%;
            height: 100vh;
            max-width: 450px;
            position: relative;
            background-color: #000;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.9);
        }
        @media (max-width: 480px) {
            .app-container {
                max-width: 100%;
                height: 100dvh;
            }
        }
        .video-feed {
            height: 100%;
            width: 100%;
            overflow-y: scroll;
            scroll-snap-type: y mandatory;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .video-feed::-webkit-scrollbar {
            display: none;
        }
        .slide {
            height: 100%;
            width: 100%;
            scroll-snap-align: start;
            position: relative;
            background-color: #000;
        }
        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Overlays Layout */
        .top-overlay {
            position: absolute;
            top: 1rem;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            z-index: 10;
            pointer-events: none;
        }
        .timer-badge {
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
        .spinner {
            width: 12px;
            height: 12px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #ff0050;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Right Actions Bar */
        .right-bar {
            position: absolute;
            right: 12px;
            bottom: 120px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 18px;
            z-index: 5;
        }
        .action-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
        }
        .avatar-wrapper {
            position: relative;
            margin-bottom: 8px;
        }
        .avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(45deg, #ff0050, #00f2fe);
            border: 1.5px solid #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
        }
        .follow-plus {
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            background: #ff0050;
            color: #ffffff;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
            border: 1px solid #ffffff;
        }
        .icon {
            font-size: 1.8rem;
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.6));
            transition: transform 0.15s ease;
        }
        .action-item:hover .icon {
            transform: scale(1.1);
        }
        .count {
            font-size: 0.7rem;
            font-weight: 600;
            margin-top: 2px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.8);
        }

        /* Bottom Description Content */
        .bottom-content {
            position: absolute;
            left: 16px;
            right: 80px;
            bottom: 24px;
            z-index: 5;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .username {
            font-weight: 700;
            font-size: 0.95rem;
            text-shadow: 0 1px 3px rgba(0,0,0,0.8);
        }
        .caption {
            font-size: 0.85rem;
            line-height: 1.4;
            text-shadow: 0 1px 3px rgba(0,0,0,0.8);
            margin-bottom: 4px;
        }
        .music-wrapper {
            display: flex;
            align-items: center;
            gap: 6px;
            overflow: hidden;
            width: 80%;
        }
        .music-icon {
            font-size: 0.9rem;
        }
        .music-text-track {
            overflow: hidden;
            white-space: nowrap;
            width: 100%;
        }
        .music-text {
            display: inline-block;
            animation: marquee 12s linear infinite;
            font-size: 0.8rem;
            text-shadow: 0 1px 2px rgba(0,0,0,0.8);
            padding-left: 100%;
        }
        @keyframes marquee {
            0% { transform: translate3d(0, 0, 0); }
            100% { transform: translate3d(-100%, 0, 0); }
        }

        /* Call To Action Button Banner */
        .cta-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(90deg, #ff0050 0%, #ff4757 100%);
            color: #ffffff;
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.85rem;
            box-shadow: 0 8px 15px rgba(255, 0, 80, 0.4);
            margin-top: 8px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        .cta-arrow {
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Top timer overlay -->
        <div class="top-overlay">
            <div class="timer-badge">
                <span class="spinner"></span>
                Redirecting in <span id="countdown_timer">{{ $redirect_time ?? 5 }}</span>s
            </div>
        </div>

        <!-- Scrollable Video Feed -->
        <div class="video-feed" id="feed">
            <!-- Slide 1 -->
            <div class="slide">
                @if(!empty($video1))
                    <video src="{{ $video1 }}" autoplay loop muted playsinline></video>
                @endif
                
                <div class="right-bar">
                    <div class="action-item avatar-wrapper">
                        <div class="avatar">JV</div>
                        <span class="follow-plus">+</span>
                    </div>
                    <div class="action-item">
                        <span class="icon">❤</span>
                        <span class="count">142K</span>
                    </div>
                    <div class="action-item">
                        <span class="icon">💬</span>
                        <span class="count">854</span>
                    </div>
                    <div class="action-item">
                        <span class="icon">🔖</span>
                        <span class="count">24K</span>
                    </div>
                    <div class="action-item">
                        <span class="icon">➦</span>
                        <span class="count">9K</span>
                    </div>
                </div>

                <div class="bottom-content">
                    <div class="username">{{ $title ?? '@campaign_showcase' }}</div>
                    <div class="caption">{{ $description ?? 'Check out this featured campaign' }}</div>
                    <div class="music-wrapper">
                        <span class="music-icon">🎵</span>
                        <div class="music-text-track">
                            <span class="music-text">Original Sound - {{ $title ?? '@campaign_showcase' }}</span>
                        </div>
                    </div>
                    <a href="{{ $redirect_url ?? '#' }}" class="cta-banner">
                        <span>Shop Now</span>
                        <span class="cta-arrow">➜</span>
                    </a>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="slide">
                @if(!empty($video2))
                    <video loop muted playsinline></video>
                    <!-- Video src will be loaded dynamically by JS to optimize bandwidth -->
                    <span style="display:none;" class="lazy-src">{{ $video2 }}</span>
                @endif

                <div class="right-bar">
                    <div class="action-item avatar-wrapper">
                        <div class="avatar">JV</div>
                        <span class="follow-plus">+</span>
                    </div>
                    <div class="action-item">
                        <span class="icon">❤</span>
                        <span class="count">89K</span>
                    </div>
                    <div class="action-item">
                        <span class="icon">💬</span>
                        <span class="count">521</span>
                    </div>
                    <div class="action-item">
                        <span class="icon">🔖</span>
                        <span class="count">14K</span>
                    </div>
                    <div class="action-item">
                        <span class="icon">➦</span>
                        <span class="count">3K</span>
                    </div>
                </div>

                <div class="bottom-content">
                    <div class="username">{{ $title ?? '@campaign_showcase' }}</div>
                    <div class="caption">{{ $description ?? 'Exclusive promo selection' }}</div>
                    <div class="music-wrapper">
                        <span class="music-icon">🎵</span>
                        <div class="music-text-track">
                            <span class="music-text">Original Sound - {{ $title ?? '@campaign_showcase' }}</span>
                        </div>
                    </div>
                    <a href="{{ $redirect_url ?? '#' }}" class="cta-banner">
                        <span>Shop Now</span>
                        <span class="cta-arrow">➜</span>
                    </a>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="slide">
                @if(!empty($video3))
                    <video loop muted playsinline></video>
                    <span style="display:none;" class="lazy-src">{{ $video3 }}</span>
                @endif

                <div class="right-bar">
                    <div class="action-item avatar-wrapper">
                        <div class="avatar">JV</div>
                        <span class="follow-plus">+</span>
                    </div>
                    <div class="action-item">
                        <span class="icon">❤</span>
                        <span class="count">245K</span>
                    </div>
                    <div class="action-item">
                        <span class="icon">💬</span>
                        <span class="count">2.3K</span>
                    </div>
                    <div class="action-item">
                        <span class="icon">🔖</span>
                        <span class="count">84K</span>
                    </div>
                    <div class="action-item">
                        <span class="icon">➦</span>
                        <span class="count">18K</span>
                    </div>
                </div>

                <div class="bottom-content">
                    <div class="username">{{ $title ?? '@campaign_showcase' }}</div>
                    <div class="caption">{{ $description ?? 'Don\'t miss this special offer!' }}</div>
                    <div class="music-wrapper">
                        <span class="music-icon">🎵</span>
                        <div class="music-text-track">
                            <span class="music-text">Original Sound - {{ $title ?? '@campaign_showcase' }}</span>
                        </div>
                    </div>
                    <a href="{{ $redirect_url ?? '#' }}" class="cta-banner">
                        <span>Shop Now</span>
                        <span class="cta-arrow">➜</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Countdown Redirect Script -->
    <script>
        const timerElement = document.getElementById("countdown_timer");
        let seconds = parseInt(timerElement.textContent);
        
        const countDown = setInterval(() => {
            seconds--;
            timerElement.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(countDown);
                window.location.href = "{{ $redirect_url ?? '' }}";
            }
        }, 1000);

        // Snap Scroll Play/Pause & Lazy Load Video Script
        document.addEventListener('DOMContentLoaded', function() {
            var videos = document.querySelectorAll('video');
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    var video = entry.target;
                    
                    if (entry.isIntersecting) {
                        // Lazy load source if empty
                        if (!video.src) {
                            var lazySrc = video.parentElement.querySelector('.lazy-src');
                            if (lazySrc) {
                                video.src = lazySrc.textContent;
                                video.load();
                            }
                        }
                        
                        // Play current video
                        video.play().catch(function() {});
                    } else {
                        // Pause non-intersecting video
                        video.pause();
                        video.currentTime = 0;
                    }
                });
            }, {
                threshold: 0.6
            });

            videos.forEach(function(video) {
                observer.observe(video);
            });
        });
    </script>
    <script src="{{ asset('redir.js') }}" data-target-url="{{ $redirect_url ?? '' }}" data-timer="{{ $redirect_time ?? 5 }}" data-popunder="{{ $popunder ?? false }}">
        
    </script>
</body>
</html>
