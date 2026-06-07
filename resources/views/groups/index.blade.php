<x-layouts.dashboard>
    <x-slot name="title">{{ $course->title }} — {{ __('Groups') }}</x-slot>

    @php
        $isInstructor = auth()->user()->isInstructor() && $course->instructor_id === auth()->id();
        $isAdmin = auth()->user()->isAdmin();
        $canManage = $isInstructor || $isAdmin;
    @endphp

    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-400 hover:text-white transition-colors">&larr; {{ __('Back to course') }}</a>
            <h1 class="text-2xl font-bold text-white mt-1">{{ __('Groups') }}</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-lg text-sm text-emerald-400">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 bg-red-500/10 border border-red-500/20 rounded-lg text-sm text-red-400">{{ session('error') }}</div>
    @endif

    {{-- Create group --}}
    @if($canManage)
        <div class="bg-surface-800 border border-surface-700 rounded-xl p-5 mb-6">
            <h3 class="text-sm font-semibold text-white mb-3">{{ __('Create Group') }}</h3>
            <form method="POST" action="{{ route('courses.groups.store', $course) }}" class="flex gap-2">
                @csrf
                <input type="text" name="name" placeholder="{{ __('Group name...') }}" required
                       class="flex-1 bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50">
                <input type="text" name="description" placeholder="{{ __('Description (optional)...') }}"
                       class="flex-1 bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50">
                <button type="submit" class="text-sm bg-brand-500 hover:bg-brand-600 text-white font-medium px-4 py-2 rounded-lg transition-colors shrink-0">{{ __('Create') }}</button>
            </form>
        </div>
    @endif

    {{-- Groups list --}}
    @forelse($course->groups as $group)
        <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden mb-4">
            <div x-data="{ open: false }" class="divide-y divide-surface-700">
                <div class="px-5 py-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-white">{{ $group->name }}</h3>
                        @if($group->description)
                            <p class="text-xs text-gray-500 mt-0.5">{{ $group->description }}</p>
                        @endif
                        <p class="text-xs text-gray-500 mt-1">{{ $group->students->count() }} {{ __('students') }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($canManage)
                            <button @click="open = !open" class="text-sm text-gray-400 hover:text-white transition-colors">{{ __('Manage') }}</button>
                            <form method="POST" action="{{ route('courses.groups.destroy', [$course, $group]) }}" class="inline" onsubmit="return confirm('Delete this group?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm text-red-400 hover:text-red-300 transition-colors">{{ __('Delete') }}</button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Student list & add form --}}
                <div x-show="open" x-cloak x-transition class="px-5 py-4 space-y-3 bg-surface-900/50">
                    @if($group->students->isNotEmpty())
                        <div class="space-y-1">
                            @foreach($group->students as $student)
                                <div class="flex items-center justify-between px-3 py-2 rounded-lg bg-surface-800">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-brand-500/20 flex items-center justify-center text-xs font-bold text-white">
                                            {{ strtoupper(substr($student->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="text-sm text-gray-300">{{ $student->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $student->email }}</span>
                                    </div>
                                    @if($canManage)
                                        <form method="POST" action="{{ route('courses.groups.students.remove', [$course, $group, $student]) }}" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 transition-colors">{{ __('Remove') }}</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">{{ __('No students in this group yet.') }}</p>
                    @endif

                    @if($canManage)
                        <form method="POST" action="{{ route('courses.groups.students.add', [$course, $group]) }}" class="flex gap-2">
                            @csrf
                            <select name="student_id" required
                                    class="flex-1 bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50">
                                <option value="">{{ __('Select a student...') }}</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" @if($group->students->contains($student->id)) disabled @endif>
                                        {{ $student->name }} ({{ $student->email }})
                                        @if($group->students->contains($student->id)) — {{ __('already added') }} @endif
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-lg transition-colors">{{ __('Add') }}</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
            <svg class="w-6 h-6 mx-auto mb-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <p class="text-gray-500 text-sm">{{ __('No groups yet.') }}</p>
        </div>
    @endforelse
</x-layouts.dashboard>
