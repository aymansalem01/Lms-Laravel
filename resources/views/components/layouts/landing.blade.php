<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" data-theme="{{ session('theme', auth()->user()?->theme ?? 'dark') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Luminus Digital Creative Technology — School of Creative Media' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = { darkMode:'class', theme:{ extend:{
        colors:{
            brand:  {50:'#fff0f5',100:'#ffd6e3',200:'#ffadc5',300:'#ff7da3',400:'#ff5085',500:'#FF3B77',600:'#e02560',700:'#b81a4f',800:'#8c1340',900:'#600d2d'},
            coral:  {400:'#dfff5a',500:'#CDFF00',600:'#a8d100'},
            surface:{400:'rgb(109 130 138)',500:'rgb(82 101 107)',600:'rgb(56 69 76)',700:'rgb(40 52 60)',800:'rgb(30 40 47)',900:'rgb(22 30 36)'},
        },
        animation:{
            'fade-in':'fadeIn .35s ease',
            'slide-up':'slideUp .35s ease',
            'marquee':'marquee 28s linear infinite',
            'spin-slow':'spin 18s linear infinite',
            'ping-slow':'ping 2.5s cubic-bezier(0,0,.2,1) infinite',
        },
        keyframes:{
            fadeIn: {'0%':{opacity:'0'},'100%':{opacity:'1'}},
            slideUp:{'0%':{transform:'translateY(12px)',opacity:'0'},'100%':{transform:'translateY(0)',opacity:'1'}},
            marquee:{'0%':{transform:'translateX(0)'},'100%':{transform:'translateX(-50%)'}},
        },
    }}}
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500&family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body{font-family:{{ app()->getLocale()==='ar'?"'IBM Plex Sans Arabic','Cairo'":"'Inter'" }},system-ui,sans-serif;}
        .gb{background:linear-gradient(135deg,#FF3B77 0%,#FF3B77 40%,#CDFF00 110%);}
        .gt{background:linear-gradient(135deg,#FF3B77,#CDFF00);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
        .bg-gradient-brand{background:linear-gradient(135deg,#FF3B77 0%,#FF3B77 40%,#CDFF00 110%);}
        .ph{position:relative;overflow:hidden;background:rgb(40 52 60);background-image:repeating-linear-gradient(135deg,rgba(255,255,255,.04) 0 8px,transparent 8px 16px);}
        .ph-lbl{position:absolute;left:12px;top:12px;font-family:'JetBrains Mono',monospace;font-size:10px;letter-spacing:.08em;text-transform:uppercase;padding:4px 8px;background:rgba(0,0,0,.6);border:1px solid rgba(255,255,255,.1);color:#cbd5e1;border-radius:4px;}
        .marquee-mask{-webkit-mask-image:linear-gradient(90deg,transparent,#000 6%,#000 94%,transparent);mask-image:linear-gradient(90deg,transparent,#000 6%,#000 94%,transparent);}
        .hero-grid-bg{background-image:linear-gradient(rgba(255,255,255,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.04) 1px,transparent 1px);background-size:60px 60px;-webkit-mask-image:radial-gradient(circle at 50% 30%,#000 0%,#000 50%,transparent 80%);mask-image:radial-gradient(circle at 50% 30%,#000 0%,#000 50%,transparent 80%);}
        .card-hover{transition:all .2s ease;}
        .card-hover:hover{border-color:rgba(255,59,119,.35);box-shadow:0 8px 30px -10px rgba(255,59,119,.2);transform:translateY(-2px);}
        @media (prefers-reduced-motion:reduce){.animate-marquee{animation:none!important;}}
        section{padding-top:clamp(64px,7vw,110px);padding-bottom:clamp(64px,7vw,110px);}
        .gradient-text{background:linear-gradient(90deg,#FF3B77,#CDFF00);-webkit-background-clip:text;background-clip:text;color:transparent;}
        .icon-flip svg{transform:scaleX(-1);}
        html[lang="ar"] body{font-family:'IBM Plex Sans Arabic','Inter',system-ui,sans-serif;}
        html[lang="ar"] h1,html[lang="ar"] h2,html[lang="ar"] h3{font-family:'Cairo','IBM Plex Sans Arabic',sans-serif;letter-spacing:0!important;}
        html[lang="ar"] .latin,html[lang="ar"] kbd,html[lang="ar"] code{font-family:'JetBrains Mono',monospace;direction:ltr;unicode-bidi:isolate;display:inline-block;}
        html[dir="rtl"] .icon-flip svg{transform:scaleX(-1);}
        html[dir="rtl"] .animate-marquee{animation-direction:reverse!important;}
        ::-webkit-scrollbar{width:8px;height:8px;}
        ::-webkit-scrollbar-track{background:#161e24;}
        ::-webkit-scrollbar-thumb{background:#52656b;border-radius:4px;}
        ::-webkit-scrollbar-thumb:hover{background:#FF3B77;}
        ::selection{background:rgba(255,59,119,.4);color:#fff;}
        [data-theme="light"] .theme-icon-sun{display:none;}
        :not([data-theme="light"]) .theme-icon-moon{display:none;}

        /* ── Dark theme (default) ─────────────────────────────── */
        :root{color-scheme:dark;--fg:255 255 255;--fg-muted:184 184 159;--fg-subtle:130 137 130;--border-op:.05;}
        body{background:#161e24;color:#e2e8f0;}

        /* ── Light theme ──────────────────────────────────────── */
        [data-theme="light"]{color-scheme:light;--fg:22 30 36;--fg-muted:82 101 107;--fg-subtle:130 138 130;--border-op:.07;}
        [data-theme="light"] body{background:#fbfbf7;color:rgb(var(--fg));}
        [data-theme="light"] .text-white{color:rgb(var(--fg))!important;}
        [data-theme="light"] .text-gray-200{color:rgb(38 46 53)!important;}
        [data-theme="light"] .text-gray-300{color:rgb(80 75 100)!important;}
        [data-theme="light"] .text-gray-400{color:rgb(var(--fg-muted))!important;}
        [data-theme="light"] .text-gray-500{color:rgb(var(--fg-subtle))!important;}
        [data-theme="light"] .text-gray-600{color:rgb(160 156 175)!important;}
        [data-theme="light"] .text-white\/80,[data-theme="light"] .text-white\/70,[data-theme="light"] .text-white\/60{color:rgb(60 70 80)!important;}
        [data-theme="light"] [class*="bg-brand-"] .text-white,[data-theme="light"] [class*="bg-brand-"].text-white,[data-theme="light"] [class*="bg-coral-"] .text-white,[data-theme="light"] [class*="bg-coral-"].text-white,[data-theme="light"] .bg-gradient-brand .text-white,[data-theme="light"] .bg-gradient-brand.text-white{color:white!important;}
        [data-theme="light"] .bg-gradient-brand .text-white\/80,[data-theme="light"] .bg-gradient-brand .text-white\/70,[data-theme="light"] .bg-gradient-brand .text-white\/60{color:rgba(255,255,255,.8)!important;}
        [data-theme="light"] .blur-3xl{opacity:.35;}
        [data-theme="light"] .border-white\/5{border-color:rgba(0,0,0,0.07)!important;}
        [data-theme="light"] .border-white\/10{border-color:rgba(0,0,0,0.10)!important;}
        [data-theme="light"] .border-white\/20{border-color:rgba(0,0,0,0.15)!important;}
        [data-theme="light"] .bg-white\/5{background-color:rgba(0,0,0,0.03)!important;}
        [data-theme="light"] .bg-white\/10{background-color:rgba(0,0,0,0.06)!important;}
        [data-theme="light"] .bg-black\/10{background-color:rgba(0,0,0,0.06)!important;}
        [data-theme="light"] .bg-black\/20{background-color:rgba(0,0,0,0.10)!important;}
        [data-theme="light"] .ph{background-image:repeating-linear-gradient(135deg,rgba(82,101,107,.08) 0 8px,transparent 8px 16px);}
        [data-theme="light"] .ph-lbl{background:rgba(255,255,255,.9);border-color:rgba(0,0,0,.08);color:#52656B;}
        [data-theme="light"] .hero-grid-bg{background-image:linear-gradient(rgba(0,0,0,.05) 1px,transparent 1px),linear-gradient(90deg,rgba(0,0,0,.05) 1px,transparent 1px);}
        [data-theme="light"] ::-webkit-scrollbar-track{background:#f0f0e8;}
        [data-theme="light"] ::-webkit-scrollbar-thumb{background:#b8b89f;}
    </style>
</head>
<body class="min-h-screen bg-surface-900 antialiased">
    {{ $slot }}
</body>
</html>
