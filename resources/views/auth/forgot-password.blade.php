<x-layouts.auth>
    <x-slot name="title">Reset Password — SAE LMS</x-slot>

    @if (session('status'))
        {{-- ── Sent state — mirrors Next.js "sent" UI ── --}}
        <div class="text-center animate-fade-in">
            <div class="w-16 h-16 bg-brand-500/20 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">✉️</div>
            <h2 class="text-2xl font-bold text-white mb-2">Check your inbox</h2>
            <p class="text-gray-400 text-sm mb-6">
                We sent a password reset link to your email. Click it to set a new password.
            </p>
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 text-sm text-brand-400 hover:text-brand-300 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to sign in
            </a>
        </div>
    @else
        {{-- ── Form state ── --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Reset your password</h1>
            <p class="text-gray-400">Enter your email and we'll send you a reset link.</p>
        </div>

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4" novalidate>
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Email address</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="you@saejordan.com"
                    autofocus required
                    class="input-field {{ $errors->has('email') ? 'border-red-500/60' : '' }}">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="btn-primary py-3">
                Send Reset Link
            </button>
        </form>

        <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 text-sm text-gray-500 hover:text-gray-300 mt-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to sign in
        </a>
    @endif
</x-layouts.auth>
