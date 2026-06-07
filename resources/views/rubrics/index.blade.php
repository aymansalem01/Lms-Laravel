<x-layouts.dashboard>
    <x-slot name="title">{{ __('Rubrics') }}</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ __('Rubrics') }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ count($rubrics) }} {{ __('rubrics') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button x-data @click="$refs.importModal.showModal()"
                    class="bg-surface-700 hover:bg-surface-600 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200 flex items-center gap-2 border border-white/10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                {{ __('Import from Brightspace') }}
            </button>
            <a href="{{ route('courses.rubrics.create', $course) }}"
               class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('Create Rubric') }}
            </a>
        </div>
    </div>

    <dialog x-ref="importModal" class="bg-transparent backdrop:bg-black/60 p-0 rounded-2xl max-w-2xl w-full">
        <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-white">{{ __('Import Brightspace Rubric') }}</h2>
                <button @click="$refs.importModal.close()" class="text-gray-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <x-rubric-importer :course="$course" />
        </div>
    </dialog>

    @if(count($rubrics) === 0)
        <div class="bg-surface-800 border border-white/10 rounded-2xl p-12 text-center">
            <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-gray-400 text-lg mb-2">{{ __('No rubrics yet') }}</p>
            <p class="text-gray-500 text-sm">{{ __('Create rubrics to standardize grading across assignments.') }}</p>
            <a href="{{ route('courses.rubrics.create', $course) }}" class="inline-block mt-4 bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200">
                {{ __('Create the first rubric') }}
            </a>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($rubrics as $rubric)
                @php
                    $criteriaCount = count($rubric->criteria ?? []);
                    $levelsCount = count($rubric->levels ?? []);
                @endphp
                <div class="bg-surface-800 border border-white/10 rounded-xl p-5 hover:border-brand-500/50 transition-all duration-200 group">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <h3 class="text-white font-semibold group-hover:text-brand-300 transition-colors">{{ $rubric->title }}</h3>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-500 mb-4">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            {{ $criteriaCount }} {{ __('criteria') }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            {{ $levelsCount }} {{ __('levels') }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 pt-3 border-t border-white/10">
                        <a href="{{ route('courses.rubrics.edit', [$course, $rubric]) }}"
                           class="text-xs text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            {{ __('Edit') }}
                        </a>
                        <form method="POST" action="{{ route('courses.rubrics.destroy', [$course, $rubric]) }}" class="inline" onsubmit="return confirm('{{ __('Delete this rubric?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="text-xs text-red-400 hover:text-red-300 transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.dashboard>
