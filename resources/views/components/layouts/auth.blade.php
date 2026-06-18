<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Luminus LMS' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = { darkMode:'class', theme:{ extend:{
        colors:{
            brand:  {50:'#fff0f5',100:'#ffd6e3',200:'#ffadc5',300:'#ff7da3',400:'#ff5085',500:'#FF3B77',600:'#e02560',700:'#b81a4f',800:'#8c1340',900:'#600d2d'},
            coral:  {400:'#dfff5a',500:'#CDFF00',600:'#a8d100'},
            surface:{400:'rgb(109 130 138)',500:'rgb(82 101 107)',600:'rgb(56 69 76)',700:'rgb(40 52 60)',800:'rgb(30 40 47)',900:'rgb(22 30 36)'},
        },
        animation:{'fade-in':'fadeIn .35s ease','slide-up':'slideUp .35s ease'},
        keyframes:{
            fadeIn: {'0%':{opacity:'0'},'100%':{opacity:'1'}},
            slideUp:{'0%':{transform:'translateY(12px)',opacity:'0'},'100%':{transform:'translateY(0)',opacity:'1'}},
        },
    }}}
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body{font-family:{{ app()->getLocale()==='ar'?"'Cairo'":"'Inter'" }},sans-serif;background:#0f0f14;color:#e2e8f0;}
        .gb{background:linear-gradient(135deg,#FF3B77 0%,#FF3B77 40%,#CDFF00 110%);}
        .gt{background:linear-gradient(135deg,#FF3B77,#CDFF00);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
        .input-field{background:rgb(40 52 60);border:1px solid rgba(255,255,255,.08);border-radius:.75rem;padding:.625rem 1rem;color:#e2e8f0;width:100%;transition:border-color .2s ease;outline:none;}
        .input-field:focus{border-color:#FF3B77!important;}
        .input-field::placeholder{color:#64748b;}
        textarea.input-field{resize:vertical;min-height:80px;}
        .btn-primary{background:linear-gradient(135deg,#FF3B77 0%,#FF3B77 40%,#CDFF00 110%);color:#fff;padding:.625rem 1rem;border-radius:.75rem;font-weight:600;transition:all .15s ease;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;gap:.5rem;width:100%;}
        .btn-primary:hover{box-shadow:0 4px 14px rgba(255,59,119,.3);}
        input:-webkit-autofill{-webkit-box-shadow:0 0 0 30px rgb(40 52 60) inset!important;-webkit-text-fill-color:#e2e8f0!important;}
    </style>
</head>
<body class="min-h-screen bg-surface-900">
<div class="min-h-screen flex">
    {{-- Left decorative panel --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-surface-800">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-600/30 via-transparent to-coral-500/20"></div>
        <div class="absolute -top-32 -left-32 w-[500px] h-[500px] rounded-full bg-brand-500/10 blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-[400px] h-[400px] rounded-full bg-coral-500/10 blur-3xl"></div>
        <div class="relative z-10 flex flex-col justify-between p-12 w-full">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl gb flex items-center justify-center text-white font-bold text-lg">S</div>
                    <div>
                        <p class="font-bold text-white">{{ __('messages.app_name') }}</p>
                        <p class="text-xs text-gray-400">{{ __('messages.school_of_creative_media') }}</p>
                    </div>
                </div>
                <div>
                    <blockquote class="text-4xl font-bold text-white leading-tight mb-4">
                        {{ __('messages.auth_hero_heading_1') }}<br><span class="gt">{{ __('messages.auth_hero_heading_2') }}</span>
                    </blockquote>
                    <p class="text-gray-400 text-sm max-w-xs leading-relaxed">
                        {{ __('messages.auth_tagline') }}
                    </p>
                    <div class="mt-8 grid grid-cols-2 gap-3">
                        @foreach([['🎬',__('messages.film_production')],['🖥️',__('messages.digital_media')],['🎮',__('messages.game_design')],['🎧',__('messages.audio_engineering')]] as [$i,$l])
                        <div class="flex items-center gap-2 text-sm text-gray-300 bg-white/5 rounded-xl px-3 py-2">
                            <span>{{$i}}</span><span>{{$l}}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                <p class="text-xs text-gray-600">{{ __('messages.footer_copyright', ['year' => date('Y')]) }}</p>
        </div>
    </div>
    {{-- Right form panel --}}
    <div class="flex-1 flex items-center justify-center p-6 lg:p-12">
        <div class="w-full max-w-md animate-fade-in">
            <div class="flex items-center gap-3 mb-8 lg:hidden">
                <div class="w-9 h-9 rounded-xl gb flex items-center justify-center text-white font-bold">S</div>
                <span class="font-semibold text-white">{{ __('messages.app_name') }}</span>
            </div>
            {{ $slot }}
        </div>
    </div>
</div>
@stack('scripts')
</body>
</html>
