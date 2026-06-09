<header class="bg-surface-800/80 backdrop-blur-md border-b border-white/5 px-4 lg:px-6 py-3 flex items-center justify-between gap-4">
    {{-- Left: hamburger + search --}}
    <div class="flex items-center gap-3 flex-1 min-w-0">
        {{-- Hamburger for mobile (admin only) --}}
        @if(auth()->user()->role === 'admin')
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-gray-400 hover:text-white hover:bg-surface-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        @endif

        {{-- Search --}}
        <div class="relative hidden sm:block max-w-xs w-full">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" placeholder="{{ __('messages.search') }}"
                   class="input-field pl-9 py-1.5 text-sm">
        </div>
    </div>

    {{-- Right: actions --}}
    <div class="flex items-center gap-1 sm:gap-2">
        {{-- View toggle --}}
        @if(in_array(auth()->user()->role, ['instructor', 'admin']) && !$studentView)
            <form method="POST" action="{{ route('view.student') }}" class="inline">
                @csrf
                <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-surface-700 transition-colors" title="{{ __('messages.student_view') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </form>
        @elseif(auth()->user()->role === 'admin' && session()->has('student_view'))
            <form method="POST" action="{{ route('view.instructor') }}" class="inline">
                @csrf
                <button type="submit" class="p-2 rounded-lg text-gray-400 hover:text-white hover:bg-surface-700 transition-colors" title="{{ __('messages.instructor_view') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </button>
            </form>
        @endif

        {{-- Theme toggle --}}
        <x-theme-toggle />

        {{-- Locale switcher --}}
        <x-locale-switcher />

        {{-- Notifications --}}
        <div class="relative">
            <div x-show="notifOpen" x-cloak class="fixed inset-0 z-[99]" @click="notifOpen = false"></div>
            <button @click="notifOpen = !notifOpen" :class="notifOpen ? 'bg-surface-700' : ''" class="relative p-2 rounded-lg text-gray-400 hover:text-white hover:bg-surface-700 transition-colors" title="{{ __('messages.notifications') }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
                @if($unreadCount > 0)
                    <span class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full bg-coral-500 text-white text-[10px] font-bold flex items-center justify-center">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </button>
            {{-- Dropdown --}}
            <div x-show="notifOpen" x-cloak
                 @click.away="notifOpen = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute right-0 mt-2 w-80 bg-surface-800/95 backdrop-blur-md border border-white/10 rounded-xl shadow-2xl overflow-hidden z-[100]"
                 style="max-height: 400px; overflow-y: auto;">
                <div class="px-4 py-3 border-b border-white/5 flex items-center justify-between">
                    <span class="text-sm font-semibold text-white">{{ __('messages.notifications') }}</span>
                    @if($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            <button type="submit" class="text-xs text-brand-400 hover:text-brand-300 transition-colors">{{ __('messages.mark_all_read') }}</button>
                        </form>
                    @endif
                </div>
                @forelse(auth()->user()->notifications->take(5) as $notification)
                    <a href="{{ $notification->link ?? '#' }}" class="flex items-start gap-3 px-4 py-3 hover:bg-surface-700 transition-colors {{ $loop->first ? '' : 'border-t border-white/5' }}">
                        <div class="w-2 h-2 rounded-full {{ $notification->is_read ? 'bg-surface-600' : 'bg-brand-500' }} mt-1.5 shrink-0"></div>
                        <div class="min-w-0">
                            <p class="text-sm text-gray-200 truncate">{{ $notification->title ?? __('messages.notification') }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-8 text-center text-gray-500 text-sm">{{ __('messages.no_notifications') }}</div>
                @endforelse
                <a href="{{ route('notifications.index') }}" class="block px-4 py-3 text-center text-sm text-brand-400 hover:text-brand-300 border-t border-white/5 transition-colors">{{ __('messages.view_all') }}</a>
            </div>
        </div>

        {{-- User dropdown --}}
        <div class="relative">
            <div x-show="userMenuOpen" x-cloak class="fixed inset-0 z-[99]" @click="userMenuOpen = false"></div>
            <button @click="userMenuOpen = !userMenuOpen" :class="userMenuOpen ? 'bg-surface-700' : ''" class="flex items-center gap-2 pl-2 pr-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors">
                <div class="w-7 h-7 rounded-full bg-brand-500/30 flex items-center justify-center text-white text-xs font-bold shrink-0 overflow-hidden">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    @endif
                </div>
                <span class="hidden sm:block text-sm font-medium text-gray-200 max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                <svg class="w-3 h-3 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            {{-- Dropdown menu --}}
            <div x-show="userMenuOpen" x-cloak
                 @click.away="userMenuOpen = false"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="absolute right-0 mt-2 w-56 bg-surface-800/95 backdrop-blur-md border border-white/10 rounded-xl shadow-2xl overflow-hidden z-[100]">
                <div class="px-4 py-3 border-b border-white/5">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                    <p class="text-[11px] text-brand-400 capitalize mt-0.5">{{ auth()->user()->role }}</p>
                </div>
                <div class="py-1">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-surface-700 transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ __('messages.profile') }}
                    </a>
                    <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-surface-700 transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        {{ __('messages.notifications') }}
                    </a>
                    <a href="{{ route('portfolio.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-surface-700 transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        {{ __('messages.portfolio') }}
                    </a>
                </div>
                <div class="border-t border-white/5 py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-surface-700 transition-colors">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            {{ __('messages.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>