<x-layouts.dashboard>
    <x-slot name="title">{{ $rubric->title }} — Luminus LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.rubrics.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to rubrics
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Rubric Info --}}
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $rubric->title }}</h2>
                        <p class="text-sm text-gray-400 mt-1">{{ $rubric->course?->title ?? '—' }}</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('admin.rubrics.edit', $rubric) }}" class="text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Edit</a>
                        <form method="POST" action="{{ route('admin.rubrics.destroy', $rubric) }}" onsubmit="return confirm('Delete this rubric?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">Delete</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Criteria Matrix --}}
            @if($rubric->criteria && $rubric->levels)
                <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Rubric Matrix</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-white/10">
                                    <th class="text-left text-gray-400 font-medium px-3 py-2">Criteria</th>
                                    @foreach($rubric->levels as $level)
                                        <th class="text-center text-gray-400 font-medium px-3 py-2">{{ $level['name'] ?? $level }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($rubric->criteria as $criterion)
                                    <tr class="hover:bg-surface-700/50 transition-colors">
                                        <td class="px-3 py-2 text-white">{{ $criterion['name'] ?? $criterion }}</td>
                                        @foreach($rubric->levels as $levelIndex => $level)
                                            <td class="px-3 py-2 text-center text-gray-400 text-xs">
                                                {{ $rubric->cells[$loop->parent->index][$levelIndex] ?? $rubric->cells[$criterion['name']][$level['name']] ?? '—' }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-4">
            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Course</h4>
                <p class="text-white text-sm">{{ $rubric->course?->title ?? '—' }}</p>
                <p class="text-gray-500 text-xs mt-1">{{ $rubric->course?->instructor?->name ?? '—' }}</p>
            </div>

            <div class="bg-surface-800 border border-white/10 rounded-xl p-5">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Assignments ({{ $rubric->assignments->count() }})</h4>
                @forelse($rubric->assignments as $assignment)
                    <div class="py-1.5 border-b border-white/5 last:border-0">
                        <a href="{{ route('admin.assignments.show', $assignment) }}" class="text-sm text-brand-400 hover:text-brand-300">{{ $assignment->title }}</a>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No assignments using this rubric.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.dashboard>
