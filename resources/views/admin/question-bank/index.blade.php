<x-layouts.dashboard>
    <x-slot name="title">Question Banks — Luminus LMS</x-slot>

    <div x-data="{ importBankId: null, importOpen: false }" class="contents">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Question Banks</h1>
            <p class="text-sm text-gray-400 mt-1">Browse all question banks on the platform</p>
        </div>
        <div class="flex items-center gap-2">
            <button x-data @click="$dispatch('open-modal', 'bulkImportModal')" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Bulk Import
            </button>
            <a href="{{ route('admin.question-bank.import-example') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Example CSV
            </a>
            <a href="{{ route('admin.question-bank.create') }}" class="flex items-center gap-1.5 text-sm bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-4 py-2.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Bank
            </a>
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

    <div class="space-y-6">
        @forelse($banks as $bank)
            <div class="bg-surface-800 border border-white/10 rounded-2xl overflow-hidden" x-data="{ showForm: false, itemType: 'multiple_choice' }">
                <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <h3 class="text-sm font-semibold text-white">{{ $bank->name }}</h3>
                        @if($bank->is_visible_to_all)
                            <span class="text-[11px] font-medium text-brand-400 bg-brand-500/10 px-2 py-0.5 rounded-full">Global</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        @foreach($bank->courses as $c)
                            <span class="text-[11px] text-gray-500 bg-surface-700 px-2 py-0.5 rounded-full">{{ $c->title }}</span>
                        @endforeach
                        <button @click="importBankId = {{ $bank->id }}; importOpen = true"
                                class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">
                            Import CSV
                        </button>
                    </div>
                </div>

                @if($bank->items->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-white/5">
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Type</th>
                                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Question</th>
                                    <th class="text-center px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Points</th>
                                    <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach($bank->items as $item)
                                    <tr class="hover:bg-white/5 transition-colors">
                                        <td class="px-5 py-3.5">
                                            <span class="text-[11px] font-medium px-2 py-0.5 rounded-full
                                                {{ $item->type === 'multiple_choice' ? 'bg-brand-500/10 text-brand-400' : '' }}
                                                {{ $item->type === 'true_false' ? 'bg-emerald-500/10 text-emerald-400' : '' }}
                                                {{ $item->type === 'short_answer' ? 'bg-blue-500/10 text-blue-400' : '' }}
                                                {{ $item->type === 'long_answer' ? 'bg-purple-500/10 text-purple-400' : '' }}">
                                                {{ str_replace('_', ' ', ucfirst($item->type)) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3.5 text-sm text-gray-300 max-w-md truncate">{{ $item->question }}</td>
                                        <td class="px-5 py-3.5 text-center text-sm text-gray-400">{{ $item->points }}</td>
                                        <td class="px-5 py-3.5 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <form method="POST" action="{{ route('admin.question-bank.destroy', $bank) }}" class="inline" onsubmit="return confirm('Delete this bank and all its questions?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">Delete Bank</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="px-5 py-8 text-center">
                        <p class="text-gray-500 text-sm">No questions in this bank.</p>
                    </div>
                @endif

                <div class="border-t border-white/10">
                    <button @click="showForm = !showForm" class="w-full flex items-center justify-between px-5 py-3 text-sm text-gray-400 hover:text-white hover:bg-white/5 transition-colors">
                        <span>Add Question</span>
                        <svg x-show="!showForm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <svg x-show="showForm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    </button>
                    <form x-show="showForm" method="POST" action="{{ route('admin.question-bank.add-item', $bank) }}" class="px-5 py-4 space-y-3 border-t border-white/10">
                        @csrf
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Type</label>
                                <select name="type" x-model="itemType" class="input-dashboard w-full text-sm" required>
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True / False</option>
                                    <option value="short_answer">Short Answer</option>
                                    <option value="long_answer">Long Answer</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-400 mb-1">Points</label>
                                <input type="number" name="points" class="input-dashboard w-full text-sm" value="1" min="1" required>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Question</label>
                            <textarea name="question" rows="2" class="input-dashboard w-full text-sm" required></textarea>
                        </div>
                        <div x-show="itemType === 'multiple_choice'">
                            <label class="block text-xs font-medium text-gray-400 mb-1">Options</label>
                            <div class="space-y-1">
                                @for($i = 0; $i < 4; $i++)
                                    <input type="text" name="options[]" class="input-dashboard w-full text-sm" placeholder="Option {{ $i + 1 }}">
                                @endfor
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-1">Correct Answer</label>
                            <input type="text" name="correct_answer" class="input-dashboard w-full text-sm">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="text-sm bg-brand-500 hover:bg-brand-600 text-white px-4 py-2 rounded-xl transition-colors">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="bg-surface-800 border border-white/10 rounded-2xl px-5 py-12 text-center">
                <p class="text-gray-400 text-sm">No question banks found.</p>
                <a href="{{ route('admin.question-bank.create') }}" class="text-brand-400 hover:text-brand-300 text-sm mt-2 inline-block">Create one</a>
            </div>
        @endforelse
    </div>

    {{-- Import Modal --}}
    <div x-show="importOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="importOpen = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-lg shadow-2xl" @click.away="importOpen = false">
            <h3 class="text-lg font-semibold text-white mb-4">Import Questions from CSV</h3>
            <p class="text-sm text-gray-400 mb-4">
                Upload a CSV file to bulk-add questions to this bank.
                <a href="{{ route('admin.question-bank.import-example') }}" class="text-brand-400 hover:text-brand-300">Download example</a>
            </p>
            <form method="POST" :action="importBankId ? `{{ url('/admin/question-bank') }}/${importBankId}/import` : '#'" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">CSV File</label>
                    <input type="file" name="csv_file" accept=".csv,.txt" required
                           class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-brand-500/20 file:text-brand-400 hover:file:bg-brand-500/30">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="importOpen = false" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">Cancel</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Upload &amp; Import</button>
                </div>
            </form>
        </div>
    </div>
    </div>

    <div class="mt-6">
        {{ $banks->links() }}
    </div>
</x-layouts.dashboard>