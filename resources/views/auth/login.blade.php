<x-layouts.auth>
    <x-slot name="title">{{ __('messages.sign_in') }} — Luminus Digital Creative Technology</x-slot>

    {{-- Heading --}}
    <div class="mb-8 text-center">
        <div class="w-12 h-12 rounded-xl gb flex items-center justify-center mx-auto mb-4">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
        </div>
        <h1 class="text-2xl font-black text-white tracking-tight">{{ __('messages.welcome_back') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('messages.sign_in_account') }}</p>
    </div>

    {{-- Success / Status message --}}
    @if (session('status'))
        <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm">
            {{ session('status') }}
        </div>
    @endif
    @if (session('success'))
        <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Login Form --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-4" novalidate>
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-300 mb-1.5">
                {{ __('messages.email_address') }}
            </label>
            <input
                id="email"
                name="email"
                type="email"
                value="{{ old('email') }}"
                placeholder="{{ __('messages.email_placeholder') }}"
                autocomplete="email"
                required
                class="input-field {{ $errors->has('email') ? 'border-red-500/60' : '' }}"
            >
            @error('email')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-medium text-gray-300">{{ __('messages.password') }}</label>
                <a href="{{ route('password.request') }}" class="text-xs text-brand-400 hover:text-brand-300 transition-colors">
                    {{ __('messages.forgot_password') }}
                </a>
            </div>
            <div class="relative">
                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    required
                    class="input-field pr-10 {{ $errors->has('password') ? 'border-red-500/60' : '' }}"
                >
                <button type="button" onclick="togglePasswordVisibility()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors">
                    <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg id="eye-off-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        @push('scripts')
        <script>
            function togglePasswordVisibility() {
                const input = document.getElementById('password');
                const eye = document.getElementById('eye-icon');
                const eyeOff = document.getElementById('eye-off-icon');
                if (input.type === 'password') {
                    input.type = 'text';
                    eye.classList.add('hidden');
                    eyeOff.classList.remove('hidden');
                } else {
                    input.type = 'password';
                    eye.classList.remove('hidden');
                    eyeOff.classList.add('hidden');
                }
            }
        </script>
        @endpush

        {{-- Remember me --}}
        <div class="flex items-center gap-2">
            <input
                id="remember"
                name="remember"
                type="checkbox"
                class="w-4 h-4 rounded border-white/20 bg-surface-800 text-brand-500 focus:ring-brand-500"
            >
            <label for="remember" class="text-sm text-gray-400">{{ __('messages.remember_me') }}</label>
        </div>

        {{-- General error --}}
        @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
            <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-sm text-red-400">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Submit --}}
        <button type="submit" class="btn-primary w-full py-3 text-sm font-semibold">
            {{ __('messages.sign_in') }}
        </button>
    </form>

    {{-- Divider --}}
    <div class="flex items-center gap-3 my-6">
        <div class="flex-1 h-px bg-white/5"></div>
        <span class="text-xs text-gray-600">{{ __('messages.or') }}</span>
        <div class="flex-1 h-px bg-white/5"></div>
    </div>

    {{-- Google Sign In --}}
    <a href="{{ route('auth.google') }}"
       class="flex items-center justify-center gap-3 w-full py-3 px-4 bg-white hover:bg-gray-100 text-gray-900 font-semibold rounded-xl transition-colors">
        <svg class="w-5 h-5" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
        {{ __('messages.sign_in_with_google') }}
    </a>

    {{-- Sign up link --}}
    <p class="text-center text-sm text-gray-500 mt-6">
        {{ __('messages.no_account') }}
        <a href="{{ route('signup') }}" class="text-brand-400 hover:text-brand-300 font-medium transition-colors">
            {{ __('messages.sign_up') }}
        </a>
    </p>
</x-layouts.auth>