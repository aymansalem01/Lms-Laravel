<x-layouts.auth>
    <x-slot name="title">Student Sign Up — Luminus LMS</x-slot>

    {{-- Back --}}
    <a href="{{ route('signup') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-300 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back
    </a>

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <div class="w-12 h-12 rounded-2xl bg-brand-500/20 flex items-center justify-center text-2xl flex-shrink-0">🎓</div>
        <div>
            <h1 class="text-2xl font-bold text-white">Create Student Account</h1>
            <p class="text-gray-400 text-sm mt-0.5">Your media education starts here</p>
        </div>
    </div>

    <form method="POST" action="{{ route('signup.student.store') }}" class="space-y-4" novalidate>
        @csrf

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Full name</label>
            <div class="relative">
                <span class="absolute inset-y-0 start-0 ps-3 flex items-center text-gray-500 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </span>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Alex Johnson" required
                    class="input-field ps-10 {{ $errors->has('name') ? 'border-red-500/60' : '' }}">
            </div>
            @error('name')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Email address</label>
            <div class="relative">
                <span class="absolute inset-y-0 start-0 ps-3 flex items-center text-gray-500 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="you@luminus.jo" required
                    class="input-field ps-10 {{ $errors->has('email') ? 'border-red-500/60' : '' }}">
            </div>
            @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
        </div>

        {{-- Program --}}
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Program</label>
            <div class="relative">
                <span class="absolute inset-y-0 start-0 ps-3 flex items-center text-gray-500 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </span>
                <select name="program" class="input-field ps-10 appearance-none">
                    <option value="">Select your program</option>
                    @foreach($programs as $program)
                        <option value="{{ $program }}" {{ old('program') === $program ? 'selected' : '' }}>{{ $program }}</option>
                    @endforeach
                </select>
            </div>
            @error('program')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
        </div>

        {{-- Password --}}
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Password</label>
            <div class="relative">
                <span class="absolute inset-y-0 start-0 ps-3 flex items-center text-gray-500 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </span>
                <input type="password" name="password" placeholder="Min 8 characters" required
                    class="input-field ps-10 {{ $errors->has('password') ? 'border-red-500/60' : '' }}">
            </div>
            @error('password')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Confirm password</label>
            <div class="relative">
                <span class="absolute inset-y-0 start-0 ps-3 flex items-center text-gray-500 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </span>
                <input type="password" name="password_confirmation" placeholder="Repeat password" required
                    class="input-field ps-10">
            </div>
        </div>

        {{-- Errors --}}
        @if ($errors->any())
        <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-sm text-red-400">
            {{ $errors->first() }}
        </div>
        @endif

        <button type="submit" class="btn-primary py-3">
            Create Student Account
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        Already have an account?
        <a href="{{ route('login') }}" class="text-brand-400 hover:text-brand-300 font-medium transition-colors">Sign in</a>
    </p>
</x-layouts.auth>
