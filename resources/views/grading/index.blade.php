<x-layouts.dashboard>
    <x-slot name="title">{{ __('Grading') }}</x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">{{ __('Grading Queue') }}</h1>
        <p class="text-sm text-gray-400 mt-1">{{ __('Review and grade student submissions') }}</p>
    </div>

    @php
        $pendingSubmissions = $submissions->filter(fn($s) => !$s->grade);
        $gradedSubmissions = $submissions->filter(fn($s) => $s->grade);
    @endphp

    <div x-data="{ tab: 'pending' }">
        <div class="flex items-center gap-1 bg-surface-800 rounded-xl p-1 mb-6 inline-flex border border-white/10">
            <button @click="tab = 'pending'"
                    :class="tab === 'pending' ? 'bg-brand-600 text-white' : 'text-gray-400 hover:text-white'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150 flex items-center gap-2">
                {{ __('Pending') }}
                <span :class="tab === 'pending' ? 'bg-white/20' : 'bg-surface-700'"
                      class="text-[11px] px-1.5 py-0.5 rounded-md font-semibold">{{ $pendingSubmissions->count() }}</span>
            </button>
            <button @click="tab = 'graded'"
                    :class="tab === 'graded' ? 'bg-brand-600 text-white' : 'text-gray-400 hover:text-white'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-150 flex items-center gap-2">
                {{ __('Graded') }}
                <span :class="tab === 'graded' ? 'bg-white/20' : 'bg-surface-700'"
                      class="text-[11px] px-1.5 py-0.5 rounded-md font-semibold">{{ $gradedSubmissions->count() }}</span>
            </button>
        </div>

        {{-- Pending Tab --}}
        <div x-show="tab === 'pending'" x-cloak>
            @if($pendingSubmissions->count() === 0)
                <div class="bg-surface-800 border border-white/10 rounded-2xl p-12 text-center">
                    <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-gray-400 text-lg mb-2">{{ __('All caught up!') }}</p>
                    <p class="text-gray-500 text-sm">{{ __('No pending submissions to grade.') }}</p>
                </div>
            @else
                <div class="bg-surface-800 border border-white/10 rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-3">{{ __('Student') }}</th>
                                    <th class="px-6 py-3">{{ __('Assignment') }}</th>
                                    <th class="px-6 py-3">{{ __('Course') }}</th>
                                    <th class="px-6 py-3">{{ __('Submitted') }}</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach($pendingSubmissions as $sub)
                                    <tr class="hover:bg-surface-700/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full gb flex items-center justify-center text-white text-xs font-bold">{{ strtoupper(substr($sub->student->name, 0, 1)) }}</div>
                                                <span class="text-gray-300 font-medium">{{ $sub->student->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-300">{{ $sub->assignment->title }}</td>
                                        <td class="px-6 py-4 text-gray-400">{{ $sub->assignment->course->title }}</td>
                                        <td class="px-6 py-4 text-gray-400">{{ $sub->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('grading.show', $sub) }}"
                                               class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-400 hover:text-brand-300 transition-colors">
                                                {{ __('Grade Now') }}
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        {{-- Graded Tab --}}
        <div x-show="tab === 'graded'" x-cloak>
            @if($gradedSubmissions->count() === 0)
                <div class="bg-surface-800 border border-white/10 rounded-2xl p-12 text-center">
                    <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-gray-400 text-lg mb-2">{{ __('No graded submissions') }}</p>
                    <p class="text-gray-500 text-sm">{{ __('Graded submissions will appear here.') }}</p>
                </div>
            @else
                <div class="bg-surface-800 border border-white/10 rounded-2xl overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-3">{{ __('Student') }}</th>
                                    <th class="px-6 py-3">{{ __('Assignment') }}</th>
                                    <th class="px-6 py-3">{{ __('Course') }}</th>
                                    <th class="px-6 py-3">{{ __('Grade') }}</th>
                                    <th class="px-6 py-3">{{ __('Graded') }}</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                @foreach($gradedSubmissions as $sub)
                                    <tr class="hover:bg-surface-700/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full gb flex items-center justify-center text-white text-xs font-bold">{{ strtoupper(substr($sub->student->name, 0, 1)) }}</div>
                                                <span class="text-gray-300 font-medium">{{ $sub->student->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-300">{{ $sub->assignment->title }}</td>
                                        <td class="px-6 py-4 text-gray-400">{{ $sub->assignment->course->title }}</td>
                                        <td class="px-6 py-4">
                                            <span class="text-green-400 font-medium">{{ number_format($sub->grade->score, 1) }}/{{ $sub->assignment->max_score }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-400">{{ $sub->grade->updated_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('grading.show', $sub) }}"
                                               class="text-sm text-brand-400 hover:text-brand-300 transition-colors">
                                                {{ __('View') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.dashboard>
