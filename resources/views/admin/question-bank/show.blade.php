<x-layouts.dashboard>
    <x-slot name="title">{{ $questionBank->name }} — SAE LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.question-bank.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Banks
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $questionBank->name }}</h1>
                <p class="text-sm text-gray-400 mt-1">
                    {{ $questionBank->items->count() }} questions
                    &middot;
                    @if($questionBank->is_visible_to_all)
                        <span class="text-brand-400">Visible to all courses</span>
                    @else
                        @foreach($questionBank->courses as $c)
                            <span class="text-gray-300">{{ $c->title }}</span>@if(!$loop->last), @endif
                        @endforeach
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.question-bank.edit', $questionBank) }}" class="text-sm bg-surface-700 hover:bg-surface-600 text-white px-4 py-2 rounded-xl transition-colors">Edit Bank</a>
            </div>
        </div>
    </div>

    <div class="bg-surface-800 border border-white/10 rounded-2xl overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-white/10">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Type</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Question</th>
                    <th class="text-center px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Points</th>
                    <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-gray-500">Added By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/5">
                @forelse($questionBank->items as $item)
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
                        <td class="px-5 py-3.5 text-right text-sm text-gray-500">{{ $item->user?->name ?? 'Unknown' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-12 text-center">
                            <p class="text-gray-400 text-sm">No questions in this bank.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.dashboard>