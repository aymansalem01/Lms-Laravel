<x-layouts.auth>
    <x-slot name="title">Set New Password — Luminus LMS</x-slot>

    <div class="mb-8">
        <h1 class="text-2xl font-black text-white tracking-tight">Set new password</h1>
        <p class="text-sm text-gray-500 mt-1">Choose a strong password for your account.</p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        {{-- Email (pre-filled, readonly) --}}
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Email address</label>
            <input type="email" name="email" value="{{ $email ?? old('email') }}" required readonly
                class="input-field text-gray-500 cursor-not-allowed opacity-60">
            @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
        </div>

        {{-- New password --}}
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">New password</label>
            <input type="password" name="password" placeholder="Min 8 characters" autofocus required
                class="input-field {{ $errors->has('password') ? 'border-red-500/60' : '' }}">
            @error('password')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
        </div>

        {{-- Confirm --}}
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Confirm new password</label>
            <input type="password" name="password_confirmation" placeholder="Repeat password" required
                class="input-field">
        </div>

        @if ($errors->any())
        <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-sm text-red-400">
            {{ $errors->first() }}
        </div>
        @endif

        <button type="submit" class="btn-primary py-3">
            Reset Password
        </button>
    </form>
</x-layouts.auth>