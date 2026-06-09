<x-layouts.dashboard>
    <x-slot name="title">{{ __('Course Catalog') }}</x-slot>

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">{{ __('Course Catalog') }}</h1>
        <p class="text-gray-400 text-sm mt-1">{{ __('Browse all available courses and enroll.') }}</p>
    </div>

    {{-- Filters --}}
    <div
        x-data="{
            search: '{{ request('search') }}',
            program: '{{ request('program') }}',
            loading: false,
            fetchResults() {
                if (this.loading) return;
                this.loading = true;
                const params = new URLSearchParams({ search: this.search, program: this.program, live: '1' });
                fetch('{{ route('courses.catalog') }}?' + params.toString())
                    .then(r => r.text())
                    .then(html => {
                        document.getElementById('catalog-results').innerHTML = html;
                    })
                    .finally(() => { this.loading = false; });
            },
            clearFilters() {
                this.search = '';
                this.program = '';
                this.fetchResults();
            }
        }"
        class="bg-surface-800 border border-surface-700 rounded-xl p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" x-model="search"
                       @input.debounce.300ms="fetchResults()"
                       placeholder="{{ __('Search courses...') }}"
                       class="w-full bg-surface-700 border border-surface-600 rounded-lg pl-9 pr-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors">
            </div>

            {{-- Program filter --}}
            <select name="program" x-model="program" @change="fetchResults()"
                    class="bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-brand-500/50 focus:border-brand-500/50 transition-colors">
                <option value="">{{ __('All Programs') }}</option>
                @foreach(['Film Production', 'Digital Media', 'Game Design', 'Audio Engineering'] as $program)
                    <option value="{{ $program }}">{{ $program }}</option>
                @endforeach
            </select>

            {{-- Loading indicator --}}
            <template x-if="loading">
                <div class="flex items-center justify-center px-3">
                    <svg class="animate-spin h-5 w-5 text-brand-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </template>

            <button @click="clearFilters()" type="button"
                    class="text-sm text-gray-400 hover:text-white px-3 py-2 transition-colors">{{ __('Clear') }}</button>
        </div>
    </div>

    {{-- Results --}}
    <div id="catalog-results">
        @include('courses._catalog_results')
    </div>
</x-layouts.dashboard>
