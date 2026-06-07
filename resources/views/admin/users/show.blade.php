<x-layouts.dashboard>
    <x-slot name="title">{{ $user->name }} — SAE LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('messages.back') }} {{ __('messages.users') }}
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile Card --}}
        <div class="lg:col-span-1">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6 text-center">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-brand-500 to-coral-500 p-[3px] mx-auto mb-4">
                    <div class="w-full h-full rounded-full bg-surface-800 flex items-center justify-center overflow-hidden">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="" class="w-full h-full object-cover">
                        @else
                            <span class="text-2xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                </div>
                <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                <p class="text-gray-400 text-sm mt-1">{{ $user->email }}</p>
                <div class="mt-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($user->role === 'admin') bg-purple-500/20 text-purple-400
                        @elseif($user->role === 'instructor') bg-brand-500/20 text-brand-300
                        @else bg-blue-500/20 text-blue-400
                        @endif">
                        @lang('messages.role_' . $user->role)
                    </span>
                    @if($user->role === 'instructor' && $user->verified_at)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400 ml-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ __('messages.verified') }}
                        </span>
                    @endif
                </div>
                @if($user->program)
                    <p class="text-sm text-gray-500 mt-3">{{ $user->program }}</p>
                @endif
                @if($user->bio)
                    <p class="text-sm text-gray-400 mt-3">{{ $user->bio }}</p>
                @endif
                <p class="text-xs text-gray-600 mt-4">{{ __('messages.joined') }} {{ $user->created_at->format('M d, Y') }}</p>

                {{-- Actions --}}
                <div class="mt-6 space-y-2">
                    {{-- Change Role --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="w-full text-sm text-gray-400 hover:text-white px-4 py-2 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">{{ __('messages.change_role') }}</button>
                        <div x-show="open" x-cloak @click.away="open = false" class="absolute top-full mt-1 left-0 right-0 bg-surface-800 border border-white/20 rounded-xl shadow-xl overflow-hidden z-50 divide-y divide-white/10">
                            <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="role" value="student">
                                <button type="submit" class="block w-full text-left px-4 py-3 text-sm font-medium {{ $user->role === 'student' ? 'bg-blue-600 text-white' : 'bg-blue-600/20 text-blue-300 hover:bg-blue-600/30' }} transition-colors capitalize">{{ __('messages.role_student') }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="role" value="instructor">
                                <button type="submit" class="block w-full text-left px-4 py-3 text-sm font-medium {{ $user->role === 'instructor' ? 'bg-brand-600 text-white' : 'bg-brand-600/20 text-brand-300 hover:bg-brand-600/30' }} transition-colors capitalize">{{ __('messages.role_instructor') }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                @csrf @method('PUT')
                                <input type="hidden" name="role" value="admin">
                                <button type="submit" class="block w-full text-left px-4 py-3 text-sm font-medium {{ $user->role === 'admin' ? 'bg-purple-600 text-white' : 'bg-purple-600/20 text-purple-300 hover:bg-purple-600/30' }} transition-colors capitalize">{{ __('messages.role_admin') }}</button>
                            </form>
                        </div>
                    </div>

                    {{-- Verify Instructor --}}
                    @if($user->role === 'instructor' && !$user->verified_at)
                        <form method="POST" action="{{ route('admin.users.verify', $user) }}">
                            @csrf
                            <button type="submit" class="w-full text-sm text-emerald-400 hover:bg-emerald-500/10 px-4 py-2 rounded-lg transition-colors border border-white/10">{{ __('messages.verify_instructor') }}</button>
                        </form>
                    @endif

                    {{-- Enroll in Course --}}
                    <div x-data="{ open: false }">
                        <button @click="open = !open" class="w-full text-sm text-brand-400 hover:text-brand-300 px-4 py-2 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">{{ __('messages.enroll_course') }}</button>
                        <div x-show="open" x-cloak class="mt-2 p-3 bg-surface-700 rounded-xl">
                            <form method="POST" action="{{ route('admin.users.enroll', $user) }}">
                                @csrf
                                <select name="course_id" class="input-dashboard mb-2">
                                    <option value="">{{ __('messages.select_course') }}</option>
                                    @foreach($courses ?? [] as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="w-full bg-brand-600 hover:bg-brand-500 text-white rounded-lg py-2 text-sm font-medium transition-colors">{{ __('messages.enroll') }}</button>
                            </form>
                        </div>
                    </div>

                    {{-- Delete User --}}
                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('{{ __('messages.confirm_delete_user') }}')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-full text-sm text-red-400 hover:bg-red-500/10 px-4 py-2 rounded-lg transition-colors border border-white/10">{{ __('messages.delete') }} {{ __('messages.users') }}</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit Profile + Enrolled Courses --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Edit Profile --}}
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">{{ __('messages.edit_profile') }}</h3>
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.full_name') }}</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" required>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.email_address') }}</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="program" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.program') }}</label>
                        <input id="program" name="program" type="text" value="{{ old('program', $user->program) }}" placeholder="e.g. Film Production" class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                    </div>
                    <div class="mb-4">
                        <label for="bio" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.bio') }}</label>
                        <textarea id="bio" name="bio" rows="3" class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors">{{ old('bio', $user->bio) }}</textarea>
                    </div>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">{{ __('messages.save_changes') }}</button>
                </form>
            </div>

            {{-- Reset Password --}}
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">{{ __('messages.reset_password') }}</h3>
                <form method="POST" action="{{ route('admin.users.password', $user) }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.new_password') }}</label>
                            <input id="password" name="password" type="password" minlength="8" class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" required>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.confirm_password') }}</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" required>
                        </div>
                    </div>
                    <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">{{ __('messages.update_password') }}</button>
                </form>
            </div>

            {{-- Enrolled Courses --}}
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">{{ __('messages.enrolled_courses') }}</h3>
                @forelse($user->enrolledCourses as $enrollment)
                    <div class="flex items-center justify-between py-3 border-b border-white/5 last:border-0">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-brand-500/20 flex items-center justify-center text-brand-300 text-xs font-bold">{{ strtoupper(substr($enrollment->title ?? 'C', 0, 1)) }}</div>
                            <div>
                                <p class="text-sm text-white font-medium">{{ $enrollment->title }}</p>
                                <p class="text-xs text-gray-500">{{ __('messages.instructor_name') }}: {{ $enrollment->instructor->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.users.unenroll', [$user, $enrollment]) }}" onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 transition-colors">{{ __('messages.remove') }}</button>
                        </form>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">{{ __('messages.no_enrolled_courses') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.dashboard>
