<x-layouts.auth>
    <x-slot name="title">Instructor Sign Up — SAE LMS</x-slot>

    {{-- Back --}}
    <a href="{{ route('signup') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-300 mb-6 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Back
    </a>

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <div class="w-12 h-12 rounded-2xl bg-coral-500/20 flex items-center justify-center text-2xl flex-shrink-0">🎬</div>
        <div>
            <h1 class="text-2xl font-bold text-white">Join as Instructor</h1>
            <p class="text-gray-400 text-sm mt-0.5">Credentials are reviewed by admin before activation</p>
        </div>
    </div>

    {{-- Pending verification notice --}}
    <div class="mb-6 p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl flex items-start gap-2.5">
        <span class="text-amber-400 text-base mt-0.5 flex-shrink-0">⚠️</span>
        <p class="text-xs text-amber-300 leading-relaxed">
            Instructor accounts require admin verification. You can log in immediately, but full access is granted after verification.
        </p>
    </div>

    <form method="POST" action="{{ route('signup.instructor.store') }}" class="space-y-4" novalidate>
        @csrf

        {{-- Section: Basic Info --}}
        <p class="text-xs text-gray-500 uppercase tracking-wider font-medium pt-1">Basic Information</p>

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Full name</label>
            <div class="relative">
                <span class="absolute inset-y-0 start-0 ps-3 flex items-center text-gray-500 pointer-events-none">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </span>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Dr. Ahmad Khalidi" required
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
                <input type="email" name="email" value="{{ old('email') }}" placeholder="you@saejordan.com" required
                    class="input-field ps-10 {{ $errors->has('email') ? 'border-red-500/60' : '' }}">
            </div>
            @error('email')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
        </div>

        {{-- Program --}}
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Primary program</label>
            <select name="program" class="input-field appearance-none">
                <option value="">Select your program</option>
                @foreach($programs as $program)
                    <option value="{{ $program }}" {{ old('program') === $program ? 'selected' : '' }}>{{ $program }}</option>
                @endforeach
            </select>
        </div>

        {{-- Section: Credentials --}}
        <p class="text-xs text-gray-500 uppercase tracking-wider font-medium pt-3">Professional Credentials</p>

        {{-- Bio --}}
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Professional bio</label>
            <textarea name="bio" rows="3" placeholder="Brief description of your experience and expertise..."
                class="input-field resize-none {{ $errors->has('bio') ? 'border-red-500/60' : '' }}">{{ old('bio') }}</textarea>
            @error('bio')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
        </div>

        {{-- Years experience + LinkedIn --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Years of experience</label>
                <input type="number" name="years_experience" value="{{ old('years_experience') }}" min="0" max="60" placeholder="e.g. 8"
                    class="input-field {{ $errors->has('years_experience') ? 'border-red-500/60' : '' }}">
                @error('years_experience')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">LinkedIn URL</label>
                <input type="url" name="linkedin_url" value="{{ old('linkedin_url') }}" placeholder="linkedin.com/in/…"
                    class="input-field {{ $errors->has('linkedin_url') ? 'border-red-500/60' : '' }}">
                @error('linkedin_url')<p class="mt-1 text-xs text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Website --}}
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Portfolio / Website <span class="text-gray-600">(optional)</span></label>
            <input type="url" name="website_url" value="{{ old('website_url') }}" placeholder="https://yourportfolio.com"
                class="input-field">
        </div>

        {{-- Qualifications --}}
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-1.5">Qualifications <span class="text-gray-600">(one per line)</span></label>
            <textarea name="qualifications" rows="3" placeholder="BSc Film Production — RMIT University&#10;Adobe Certified Expert&#10;10 years documentary experience"
                class="input-field resize-none">{{ old('qualifications') }}</textarea>
        </div>

        {{-- Section: Password --}}
        <p class="text-xs text-gray-500 uppercase tracking-wider font-medium pt-3">Set Password</p>

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

        @if ($errors->any())
        <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-sm text-red-400">
            {{ $errors->first() }}
        </div>
        @endif

        <button type="submit" class="btn-primary py-3">
            Create Instructor Account
        </button>
    </form>

    <p class="text-center text-sm text-gray-500 mt-6">
        Already have an account?
        <a href="{{ route('login') }}" class="text-brand-400 hover:text-brand-300 font-medium transition-colors">Sign in</a>
    </p>
</x-layouts.auth>