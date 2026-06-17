<x-layouts.auth>
    <x-slot name="title">Create Account — Luminus LMS</x-slot>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Join Luminus Digital Creative Technology</h1>
        <p class="text-gray-400">Choose how you want to get started</p>
    </div>

    {{-- Role Chooser Cards --}}
    <div class="space-y-4">

        {{-- Student card --}}
        <a href="{{ route('signup.student') }}"
           class="group flex items-center gap-5 p-5 bg-surface-800 border border-white/10 hover:border-brand-500/50 rounded-2xl transition-all hover:bg-surface-700">
            <div class="w-14 h-14 rounded-2xl bg-brand-500/20 flex items-center justify-center text-2xl flex-shrink-0 group-hover:bg-brand-500/30 transition-colors">
                🎓
            </div>
            <div class="flex-1">
                <p class="font-semibold text-white group-hover:text-brand-300 transition-colors">I'm a Student</p>
                <p class="text-sm text-gray-400 mt-0.5">Enroll in courses, submit assignments, build your portfolio</p>
            </div>
            <svg class="w-5 h-5 text-gray-600 group-hover:text-brand-400 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

        {{-- Instructor card --}}
        <a href="{{ route('signup.instructor') }}"
           class="group flex items-center gap-5 p-5 bg-surface-800 border border-white/10 hover:border-coral-500/50 rounded-2xl transition-all hover:bg-surface-700">
            <div class="w-14 h-14 rounded-2xl bg-coral-500/20 flex items-center justify-center text-2xl flex-shrink-0 group-hover:bg-coral-500/30 transition-colors">
                🎬
            </div>
            <div class="flex-1">
                <p class="font-semibold text-white group-hover:text-coral-300 transition-colors">I'm an Instructor</p>
                <p class="text-sm text-gray-400 mt-0.5">Create courses, grade students, schedule live sessions</p>
            </div>
            <svg class="w-5 h-5 text-gray-600 group-hover:text-coral-400 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

    {{-- Programs preview --}}
    <div class="mt-8 p-4 bg-white/3 border border-white/5 rounded-2xl">
        <p class="text-xs text-gray-500 mb-3 font-medium uppercase tracking-wider">Available Programs</p>
        <div class="grid grid-cols-2 gap-2">
            @foreach([['🎬','Film Production'],['🖥️','Digital Media'],['🎮','Game Design'],['🎧','Audio Engineering']] as [$icon,$label])
            <div class="flex items-center gap-2 text-sm text-gray-400">
                <span>{{ $icon }}</span><span>{{ $label }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <p class="text-center text-sm text-gray-500 mt-8">
        Already have an account?
        <a href="{{ route('login') }}" class="text-brand-400 hover:text-brand-300 font-medium transition-colors">Sign in</a>
    </p>
</x-layouts.auth>
