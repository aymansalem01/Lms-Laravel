<x-layouts.dashboard>
    <x-slot name="title">Edit Profile — SAE LMS</x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        {{-- Section: Basic Info --}}
        <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-white mb-6">Basic Information</h2>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300 mb-1.5">Full Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="input-dashboard">
                        @error('name')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                        <input id="email" type="email" value="{{ $user->email }}" readonly class="w-full bg-surface-800/50 border border-white/5 text-gray-500 rounded-xl py-3 px-4 text-sm cursor-not-allowed">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="bio" class="block text-sm font-medium text-gray-300 mb-1.5">Bio</label>
                    <textarea id="bio" name="bio" rows="3" placeholder="Tell us about yourself..." class="input-dashboard resize-none">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label for="program" class="block text-sm font-medium text-gray-300 mb-1.5">Program</label>
                    <select id="program" name="program" class="input-dashboard">
                        <option value="">Select a program</option>
                        @foreach(['Film Production', 'Digital Media', 'Game Design', 'Audio Engineering'] as $prog)
                            <option value="{{ $prog }}" {{ old('program', $user->program) === $prog ? 'selected' : '' }}>{{ $prog }}</option>
                        @endforeach
                    </select>
                    @error('program')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
                </div>

                {{-- Verification Status --}}
                @if($user->role === 'instructor' && $user->verified_at)
                    <div class="flex items-center gap-2 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-sm text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Verified Instructor
                    </div>
                @endif

                {{-- Section: Avatar --}}
                <div class="pt-6 mt-6 border-t border-white/10">
                    <h3 class="text-white font-semibold mb-4">Avatar</h3>
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-brand-500 to-coral-500 p-[2px] shrink-0">
                            <div class="w-full h-full rounded-full bg-surface-800 flex items-center justify-center overflow-hidden">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1">
                            <label for="avatar_url" class="block text-sm font-medium text-gray-300 mb-1.5">Avatar URL</label>
                            <input id="avatar_url" name="avatar_url" type="url" value="{{ old('avatar_url', $user->avatar_url) }}" placeholder="https://example.com/avatar.jpg" class="input-dashboard">
                            @error('avatar_url')<p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Section: Instructor Fields --}}
                @if($user->role === 'instructor')
                    <div class="pt-6 mt-6 border-t border-white/10">
                        <h3 class="text-white font-semibold mb-4">Instructor Details</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="qualifications" class="block text-sm font-medium text-gray-300 mb-1.5">Qualifications</label>
                                <input id="qualifications" name="qualifications" type="text" value="{{ old('qualifications', $user->qualifications) }}" placeholder="e.g. MA Film Studies" class="input-dashboard">
                            </div>
                            <div>
                                <label for="years_experience" class="block text-sm font-medium text-gray-300 mb-1.5">Years of Experience</label>
                                <input id="years_experience" name="years_experience" type="number" value="{{ old('years_experience', $user->years_experience) }}" min="0" class="input-dashboard">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                            <div>
                                <label for="linkedin_url" class="block text-sm font-medium text-gray-300 mb-1.5">LinkedIn URL</label>
                                <input id="linkedin_url" name="linkedin_url" type="url" value="{{ old('linkedin_url', $user->linkedin_url) }}" placeholder="https://linkedin.com/in/..." class="input-dashboard">
                            </div>
                            <div>
                                <label for="website_url" class="block text-sm font-medium text-gray-300 mb-1.5">Website URL</label>
                                <input id="website_url" name="website_url" type="url" value="{{ old('website_url', $user->website_url) }}" placeholder="https://yoursite.com" class="input-dashboard">
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Section: Preferences --}}
                <div class="pt-6 mt-6 border-t border-white/10">
                    <h3 class="text-white font-semibold mb-4">Preferences</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="locale" class="block text-sm font-medium text-gray-300 mb-1.5">Language</label>
                            <select id="locale" name="locale" class="input-dashboard">
                                <option value="en" {{ old('locale', $user->locale) === 'en' ? 'selected' : '' }}>English</option>
                                <option value="ar" {{ old('locale', $user->locale) === 'ar' ? 'selected' : '' }}>العربية</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">Theme</label>
                            <div class="flex gap-2">
                                <label class="flex items-center gap-2 px-4 py-2.5 bg-surface-800 border border-white/10 rounded-xl cursor-pointer has-[:checked]:border-brand-500 has-[:checked]:bg-brand-500/10 transition-colors">
                                    <input type="radio" name="theme" value="dark" {{ old('theme', $user->theme ?? 'dark') === 'dark' ? 'checked' : '' }} class="text-brand-500 focus:ring-brand-500">
                                    <span class="text-sm text-gray-300">Dark</span>
                                </label>
                                <label class="flex items-center gap-2 px-4 py-2.5 bg-surface-800 border border-white/10 rounded-xl cursor-pointer has-[:checked]:border-brand-500 has-[:checked]:bg-brand-500/10 transition-colors">
                                    <input type="radio" name="theme" value="light" {{ old('theme', $user->theme ?? '') === 'light' ? 'checked' : '' }} class="text-brand-500 focus:ring-brand-500">
                                    <span class="text-sm text-gray-300">Light</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-6">
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>
