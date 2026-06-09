<x-layouts.dashboard>
    <x-slot name="title">{{ __('Students') }} - {{ $course->title }}</x-slot>

    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $course->title }}</h1>
                <p class="text-gray-400 text-sm mt-1">{{ __('Students') }} &mdash; {{ $course->students->count() }} {{ __('enrolled') }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('courses.show', $course) }}" class="text-sm text-brand-400 hover:text-brand-300 transition-colors">{{ __('Back to Course') }} &rarr;</a>
            </div>
        </div>
    </div>

    {{-- Primary Instructor --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl p-5 mb-6">
        <h3 class="text-sm font-semibold text-white mb-3">{{ __('Primary Instructor') }}</h3>
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-brand-500/20 flex items-center justify-center text-white text-sm font-bold shrink-0">
                {{ strtoupper(substr($course->instructor->name ?? '?', 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-medium text-white">{{ $course->instructor->name ?? __('Unknown') }}</p>
                <p class="text-xs text-gray-500">{{ $course->instructor->email ?? '' }}</p>
            </div>
        </div>
    </div>

    {{-- Add Student --}}
    @if(auth()->user()->isAdmin())
    @php $studentOptions = $availableStudents->map(fn($s) => ['id' => (string)$s->id, 'label' => $s->name . ' (' . $s->email . ')'])->values(); @endphp
    <div x-data="{
        search: '',
        open: false,
        selected: [],
        items: {{ json_encode($studentOptions) }},
        get filtered() {
            return this.items.filter(s => s.label.toLowerCase().includes(this.search.toLowerCase()));
        }
    }" class="bg-surface-800 border border-surface-700 rounded-xl p-5 mb-6">
        <h3 class="text-sm font-semibold text-white mb-3">{{ __('Enroll Students') }}</h3>
        <form method="POST" action="{{ route('courses.roster.add', $course) }}">
            @csrf
            <div class="relative mb-3">
                <input type="text" x-model="search" @click="open = true" @input="open = true"
                       placeholder="{{ __('Search students...') }}"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50">
                <div x-show="open" @click.outside="open = false" x-cloak
                     class="absolute z-10 mt-1 w-full bg-surface-700 border border-surface-600 rounded-lg shadow-xl max-h-48 overflow-y-auto">
                    <template x-for="student in filtered" :key="student.id">
                        <label class="flex items-center gap-3 px-3 py-2 hover:bg-surface-600 cursor-pointer border-b border-surface-600 last:border-0">
                            <input type="checkbox" x-model="selected" :value="student.id"
                                   class="rounded bg-surface-600 border-surface-500 text-brand-500 focus:ring-brand-500/50">
                            <span class="text-sm text-gray-200" x-text="student.label"></span>
                        </label>
                    </template>
                    <p x-show="filtered.length === 0" class="text-sm text-gray-500 px-3 py-4 text-center">{{ __('No students found.') }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-1 mb-3" x-show="selected.length > 0">
                <template x-for="id in selected" :key="id">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-brand-500/20 text-brand-400 text-xs">
                        <span x-text="items.find(s => s.id === id)?.label.split(' (')[0]"></span>
                        <button @click="selected = selected.filter(s => s !== id)" type="button" class="hover:text-white">&times;</button>
                    </span>
                </template>
            </div>
            <template x-for="id in selected" :key="id">
                <input type="hidden" name="student_ids[]" :value="id">
            </template>
            <div class="flex items-center gap-2">
                <button type="submit" :disabled="selected.length === 0"
                        class="text-sm bg-brand-500 hover:bg-brand-600 disabled:bg-surface-600 disabled:text-gray-500 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                    <span x-text="`{{ __('Enroll') }} (${selected.length})`"></span>
                </button>
                <button type="button" @click="selected = []; search = ''" x-show="selected.length > 0" class="text-xs text-gray-400 hover:text-white transition-colors">{{ __('Clear') }}</button>
            </div>
        </form>
    </div>
    @endif

    {{-- Enrolled Students --}}
    <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-surface-700">
            <h3 class="text-sm font-semibold text-white">{{ __('Enrolled Students') }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-surface-700">
                        <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Name') }}</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Email') }}</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Program') }}</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Enrolled') }}</th>
                        @if(auth()->user()->isAdmin())
                            <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Actions') }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-700">
                    @forelse($course->students as $student)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-500/20 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                        {{ strtoupper(substr($student->name ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-white">{{ $student->name ?? __('Unknown') }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-400">{{ $student->email ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                @if($student->program ?? false)
                                    <span class="text-[11px] font-medium px-2 py-0.5 rounded-full bg-brand-500/10 text-brand-400">{{ $student->program }}</span>
                                @else
                                    <span class="text-sm text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-400">
                                {{ $student->pivot->enrolled_at ? \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('M d, Y') : '—' }}
                            </td>
                            @if(auth()->user()->isAdmin())
                                <td class="px-5 py-3.5 text-right">
                                    <form method="POST" action="{{ route('courses.roster.remove', [$course, $student]) }}" onsubmit="return confirm('Remove {{ $student->name }} from this course?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">{{ __('Remove') }}</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 5 : 4 }}" class="px-5 py-12 text-center">
                                <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-surface-700 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                                </div>
                                <p class="text-gray-400 text-sm">{{ __('No students enrolled yet.') }}</p>
                                <p class="text-gray-500 text-xs mt-1">{{ __('Students will appear here once they enroll.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.dashboard>
