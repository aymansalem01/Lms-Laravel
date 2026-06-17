<x-layouts.dashboard>
    <x-slot name="title">Question Banks — Luminus LMS</x-slot>

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Question Banks</h1>
            <p class="text-sm text-gray-400 mt-1">Manage named question banks across courses</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.question-bank.create') }}" class="flex items-center gap-1.5 text-sm bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-4 py-2.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Bank
            </a>
            <button x-data @click="$dispatch('open-modal', 'bulkImportModal')" class="flex items-center gap-1.5 text-sm bg-surface-700 hover:bg-surface-600 text-white rounded-xl px-4 py-2.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Bulk Import
            </button>
        </div>
    </div>

    {{-- Bulk Import Modal --}}
    <div x-data="{ open: false }" x-cloak x-show="open" @open-modal.window="if ($event.detail === 'bulkImportModal') open = true" @keydown.escape="open = false" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/60" @click="open = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-2xl p-6 w-full max-w-lg mx-4 shadow-2xl">
            <h3 class="text-lg font-semibold text-white mb-4">Bulk Import Question Banks</h3>
            <form method="POST" action="{{ route('admin.question-bank.bulk-import') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">CSV File</label>
                    <input type="file" name="csv_file" accept=".csv,.txt" class="input-dashboard w-full text-sm" required>
                    <p class="text-xs text-gray-500 mt-1">Columns: bank_name, type, question, options, correct_answer, points</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Attach banks to courses (optional)</label>
                    <div class="max-h-40 overflow-y-auto space-y-1.5 bg-surface-700 rounded-xl p-3 border border-white/10">
                        @foreach($courses as $course)
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                                       class="w-4 h-4 rounded border-white/20 bg-surface-800 text-brand-500 focus:ring-brand-500">
                                <span class="text-sm text-gray-300">{{ $course->title }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <a href="{{ route('admin.question-bank.bulk-import-example') }}" class="text-xs text-brand-400 hover:text-brand-300">Download example CSV</a>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="open = false" class="text-sm text-gray-400 hover:text-white px-4 py-2 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="text-sm bg-brand-600 hover:bg-brand-500 text-white font-semibold px-4 py-2 rounded-xl transition-colors">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <form method="GET" class="mb-6">
        <div class="flex items-center gap-3">
            <select name="course_id" class="input-dashboard w-64" onchange="this.form.submit()">
                <option value="">All Courses</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                @endforeach
            </select>
            <span class="text-xs text-gray-500">{{ $banks->total() }} banks</span>
        </div>
    </form>

    <div class="bg-surface-800 border border-white/10 rounded-2xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Name</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Courses</th>
                    <th class="text-center px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Questions</th>
                    <th class="text-center px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Visibility</th>
                    <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($banks as $bank)
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.question-bank.show', $bank) }}" class="text-sm font-medium text-white hover:text-brand-400 transition-colors">{{ $bank->name }}</a>
                            <p class="text-xs text-gray-500 mt-0.5">by {{ $bank->user?->name ?? 'Unknown' }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($bank->courses as $c)
                                    <span class="text-[11px] bg-surface-700 text-gray-300 px-2 py-0.5 rounded-full">{{ $c->title }}</span>
                                @endforeach
                                @if($bank->is_visible_to_all)
                                    <span class="text-[11px] bg-brand-500/10 text-brand-400 px-2 py-0.5 rounded-full">All Courses</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 text-center text-sm text-gray-400">{{ $bank->items->count() }}</td>
                        <td class="px-5 py-4 text-center">
                            @if($bank->is_visible_to_all)
                                <span class="text-[11px] font-medium text-brand-400 bg-brand-500/10 px-2 py-0.5 rounded-full">Global</span>
                            @else
                                <span class="text-[11px] font-medium text-gray-500 bg-surface-700 px-2 py-0.5 rounded-full">Course-specific</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.question-bank.show', $bank) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">View</a>
                                <a href="{{ route('admin.question-bank.edit', $bank) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">Edit</a>
                                <form method="POST" action="{{ route('admin.question-bank.destroy', $bank) }}" class="inline" onsubmit="return confirm('Delete this bank and all its questions?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <p class="text-gray-400 text-sm">No question banks yet.</p>
                            <a href="{{ route('admin.question-bank.create') }}" class="text-brand-400 hover:text-brand-300 text-sm mt-1 inline-block">Create one</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $banks->links() }}
    </div>
</x-layouts.dashboard>