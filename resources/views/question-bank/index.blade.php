<x-layouts.dashboard>
    <x-slot name="title">{{ __('Question Banks') }} — {{ $course->name }}</x-slot>

    <div x-data="{ importBankId: null, importOpen: false }" class="contents">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                {{ __('Back to course') }}
            </a>
            <h1 class="text-2xl font-bold text-white">{{ __('Question Banks') }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ $course->name }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button x-data @click="$dispatch('open-modal', 'bulkImportModal')" class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Bulk Import
            </button>
            <a href="{{ route('question-bank.import-example') }}" class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Example CSV
            </a>
            <a href="{{ route('courses.question-bank.create', $course) }}" class="flex items-center gap-1.5 text-sm bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-4 py-2.5 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('New Bank') }}
            </a>
        </div>
    </div>

    @forelse($banks as $bank)
        <div class="bg-surface-800 border border-white/10 rounded-2xl overflow-hidden mb-6">
            <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-white">{{ $bank->name }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $bank->items->count() }} {{ __('questions') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($bank->is_visible_to_all)
                        <span class="text-[11px] font-medium text-brand-400 bg-brand-500/10 px-2 py-0.5 rounded-full">{{ __('Global') }}</span>
                    @endif
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
                                <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Type') }}</th>
                                <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Question') }}</th>
                                <th class="text-center px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Points') }}</th>
                                <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">{{ __('Actions') }}</th>
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
                                            <a href="{{ route('courses.question-bank.edit', [$course, $item]) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded hover:bg-surface-600 transition-colors">{{ __('Edit') }}</a>
                                            <form method="POST" action="{{ route('courses.question-bank.destroy', [$course, $item]) }}" class="inline" onsubmit="return confirm('Delete this question?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded hover:bg-red-500/10 transition-colors">{{ __('Delete') }}</button>
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
                    <p class="text-gray-500 text-sm">{{ __('No questions in this bank.') }}</p>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-surface-800 border border-white/10 rounded-2xl px-5 py-12 text-center">
            <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-surface-700 flex items-center justify-center">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-gray-400 text-sm">{{ __('No question banks yet.') }}</p>
            <p class="text-gray-500 text-xs mt-1">{{ __('Create a bank to add reusable questions for quizzes.') }}</p>
            <a href="{{ route('courses.question-bank.create', $course) }}" class="text-brand-400 hover:text-brand-300 text-sm mt-3 inline-block">{{ __('Create Bank') }}</a>
        </div>
    @endforelse

    {{-- Import Modal --}}
    <div x-show="importOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="importOpen = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-lg shadow-2xl" @click.away="importOpen = false">
            <h3 class="text-lg font-semibold text-white mb-4">Import Questions from CSV</h3>
            <p class="text-sm text-gray-400 mb-4">
                Upload a CSV file to bulk-add questions to this bank.
                <a href="{{ route('question-bank.import-example') }}" class="text-brand-400 hover:text-brand-300">Download example</a>
            </p>
            <form method="POST" :action="importBankId ? `{{ url('question-bank') }}/${importBankId}/import` : '#'" enctype="multipart/form-data">
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

    {{-- Bulk Import Modal --}}
    <div x-data="{ open: false }" x-cloak x-show="open" @open-modal.window="if ($event.detail === 'bulkImportModal') open = true" @keydown.escape="open = false" class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/60" @click="open = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-2xl p-6 w-full max-w-lg mx-4 shadow-2xl">
            <h3 class="text-lg font-semibold text-white mb-4">Bulk Import Question Banks</h3>
            <form method="POST" action="{{ route('question-bank.bulk-import') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">CSV File</label>
                    <input type="file" name="csv_file" accept=".csv,.txt" class="input-dashboard w-full text-sm" required>
                    <p class="text-xs text-gray-500 mt-1">Columns: bank_name, type, question, options, correct_answer, points, course_ids</p>
                </div>
                <div class="flex items-center justify-between">
                    <a href="{{ route('question-bank.bulk-import-example') }}" class="text-xs text-brand-400 hover:text-brand-300">Download example CSV</a>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="open = false" class="text-sm text-gray-400 hover:text-white px-4 py-2 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="text-sm bg-brand-600 hover:bg-brand-500 text-white font-semibold px-4 py-2 rounded-xl transition-colors">Import</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>