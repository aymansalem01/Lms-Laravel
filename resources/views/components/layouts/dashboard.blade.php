<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="{{ session('theme', auth()->user()?->theme ?? 'dark') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Luminus LMS' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500&family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: {{ app()->getLocale() === 'ar' ? "'Cairo'" : "'Inter'" }}, sans-serif; }
        [x-cloak] { display: none !important; }
        dialog::backdrop { background: rgba(25, 25, 35, 0.92); }
    </style>
    @stack('styles')
</head>
<body class="bg-surface-900 text-gray-200 antialiased" x-data="{ sidebarOpen: false, userMenuOpen: false, notifOpen: false }">
    <x-layouts.student-view-banner />
    <div class="flex h-screen overflow-hidden {{ $studentView ? 'mt-10' : '' }}">

        {{-- Sidebar (admin only) --}}
        @if(auth()->user()->role === 'admin')
            {{-- Mobile overlay --}}
            <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-30 bg-surface-800/80 lg:hidden" @click="sidebarOpen = false"></div>

            {{-- Sidebar panel --}}
            <aside class="fixed inset-y-0 left-0 z-40 w-60 -translate-x-full transition-transform duration-200 ease-in-out lg:relative lg:translate-x-0 flex flex-col"
                   :class="{ 'translate-x-0': sidebarOpen }">
                <x-sidebar />
                @if(!$studentView)
                <div class="px-3 py-3 border-t border-white/5">
                    <x-mini-calendar />
                </div>
                @endif
            </aside>
        @endif

        {{-- Main area --}}
        <div class="flex-1 flex flex-col min-h-0 min-w-0">
            {{-- Top bar --}}
            <x-topbar />

            {{-- Horizontal nav (student/instructor) --}}
            @if(auth()->user()->role !== 'admin')
                <x-horizontal-nav />
            @endif

            {{-- Main content --}}
            <main class="flex-1 overflow-y-auto p-6 animate-fade-in">
                @if(session('success'))
                    <div class="mb-6 bg-emerald-500/10 border border-emerald-500/20 rounded-xl px-5 py-3 flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm text-emerald-300">{{ session('success') }}</p>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 bg-red-500/10 border border-red-500/20 rounded-xl px-5 py-3 flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-sm text-red-300">{{ session('error') }}</p>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="mb-6 bg-amber-500/10 border border-amber-500/20 rounded-xl px-5 py-3">
                        <div class="flex items-center gap-3 mb-1">
                            <svg class="w-5 h-5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <p class="text-sm text-amber-300">{{ session('warning') }}</p>
                        </div>
                        @if(session('import_errors'))
                            <ul class="mt-2 space-y-0.5">
                                @foreach(session('import_errors') as $err)
                                    <li class="text-xs text-amber-400/80 ml-8">{{ $err }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>