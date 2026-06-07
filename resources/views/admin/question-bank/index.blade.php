<x-layouts.dashboard>
    <x-slot name="title">Question Bank — SAE LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Question Bank</h1>
        <a href="{{ route('admin.question-bank.create') }}"
           class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-4 py-2 text-sm transition-colors duration-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Question
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" class="mb-6">
        <div class="flex items-center gap-3 max-w-md">
            <select name="course_id" class="flex-1 bg-surface-700 border border-white/20 text-white rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500" style="color-scheme:dark" onchange="this.form.submit()">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                @endforeach
            </select>
            @if(request('course_id'))
                <a href="{{ route('admin.question-bank.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Clear</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Question</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Type</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Course</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Points</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Uses</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($items as $item)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3 max-w-xs">
                                <p class="text-white truncate">{{ $item->question }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono uppercase tracking-wider
                                    @switch($item->type)
                                        @case('multiple_choice') bg-blue-500/20 text-blue-300 border border-blue-500/30 @break
                                        @case('true_false') bg-purple-500/20 text-purple-300 border border-purple-500/30 @break
                                        @case('short_answer') bg-amber-500/20 text-amber-300 border border-amber-500/30 @break
                                        @case('long_answer') bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 @break
                                    @endswitch">
                                    {{ ucwords(str_replace('_', ' ', $item->type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $item->course->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $item->points }}</td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $item->usages->count() }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.question-bank.edit', $item) }}"
                                       class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Edit</a>
                                    <form method="POST" action="{{ route('admin.question-bank.destroy', $item) }}" onsubmit="return confirm('Delete this question?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">No questions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $items->links() }}
</x-layouts.dashboard>
