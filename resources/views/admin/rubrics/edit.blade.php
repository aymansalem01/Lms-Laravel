<x-layouts.dashboard>
    <x-slot name="title">Edit Rubric — {{ $rubric->title }}</x-slot>

    @php
        $criteria = $rubric->criteria ?? [];
        $levels = $rubric->levels ?? [];
        $cells = $rubric->cells ?? [];
    @endphp

    <div class="mb-6">
        <a href="{{ route('admin.rubrics.show', $rubric) }}" class="text-sm text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to rubric
        </a>
        <h1 class="text-2xl font-bold text-white mt-2">Edit Rubric</h1>
        <p class="text-sm text-gray-400 mt-1">{{ $rubric->course?->title ?? '—' }} &middot; {{ $rubric->title }}</p>
    </div>

    <div class="max-w-5xl">
        <form method="POST" action="{{ route('admin.rubrics.update', $rubric) }}" class="space-y-6">
            @csrf @method('PUT')

            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">Rubric Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $rubric->title) }}" required
                       class="input-dashboard">
            </div>

            <div x-data="rubricBuilder()" x-init="init()" class="space-y-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-white">Criteria</h2>
                            <button type="button" @click="addCriterion()"
                                    class="text-sm text-brand-400 hover:text-brand-300 transition-colors flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Criterion
                            </button>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(criterion, index) in criteria" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="text" x-model="criterion.name" :name="'criteria['+index+'][name]'"
                                           class="flex-1 bg-surface-700 border border-white/10 text-white rounded-xl py-2 px-3 text-sm focus:outline-none focus:border-brand-500"
                                           placeholder="Criterion name">
                                    <button type="button" @click="removeCriterion(index)" x-show="criteria.length > 1"
                                            class="text-red-400 hover:text-red-300 p-1 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-white">Levels</h2>
                            <button type="button" @click="addLevel()"
                                    class="text-sm text-brand-400 hover:text-brand-300 transition-colors flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Level
                            </button>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(level, index) in levels" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="text" x-model="level.name" :name="'levels['+index+'][name]'"
                                           class="flex-1 bg-surface-700 border border-white/10 text-white rounded-xl py-2 px-3 text-sm focus:outline-none focus:border-brand-500"
                                           placeholder="Level name">
                                    <input type="number" x-model="level.maxScore" :name="'levels['+index+'][max_score]'"
                                           class="w-20 bg-surface-700 border border-white/10 text-white rounded-xl py-2 px-3 text-sm focus:outline-none focus:border-brand-500 text-center"
                                           placeholder="Max">
                                    <button type="button" @click="removeLevel(index)" x-show="levels.length > 1"
                                            class="text-red-400 hover:text-red-300 p-1 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                    <h2 class="text-lg font-semibold text-white mb-4">Rubric Matrix</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left">
                                    <th class="px-3 py-2 text-gray-500 text-xs uppercase tracking-wider min-w-[150px]">Criteria / Levels</th>
                                    <template x-for="(level, lIdx) in levels" :key="lIdx">
                                        <th class="px-3 py-2 text-center min-w-[120px]">
                                            <span class="text-xs text-gray-300 font-semibold" x-text="level.name"></span>
                                            <br>
                                            <span class="text-[10px] text-gray-500" x-text="'(' + level.maxScore + ' pts)'"></span>
                                        </th>
                                    </template>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/10">
                                <template x-for="(criterion, cIdx) in criteria" :key="cIdx">
                                    <tr>
                                        <td class="px-3 py-2 text-gray-300 font-medium text-sm" x-text="criterion.name || 'Criterion ' + (cIdx + 1)"></td>
                                        <template x-for="(level, lIdx) in levels" :key="lIdx">
                                            <td class="px-3 py-2 text-center">
                                                <input type="number" step="0.1"
                                                       :name="'cells['+cIdx+']['+lIdx+']'"
                                                       :placeholder="'0-' + level.maxScore"
                                                       class="w-full bg-surface-700 border border-white/10 text-white rounded-lg py-1.5 px-2 text-xs text-center focus:outline-none focus:border-brand-500"
                                                       x-model="cells[cIdx] ? (cells[cIdx][lIdx] || '') : ''">
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200">
                    Update Rubric
                </button>
                <a href="{{ route('admin.rubrics.show', $rubric) }}"
                   class="text-sm text-gray-400 hover:text-white transition-colors">Cancel</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function rubricBuilder() {
            return {
                criteria: @json($criteria).map(c => ({ name: c.name || '' })),
                levels: @json($levels).map(l => ({ name: l.name || '', maxScore: l.max_score || l.maxScore || 10 })),
                cells: @json($cells),
                init() {
                    this.updateCells();
                },
                addCriterion() {
                    this.criteria.push({ name: '' });
                    this.updateCells();
                },
                removeCriterion(index) {
                    if (this.criteria.length > 1) {
                        this.criteria.splice(index, 1);
                        this.updateCells();
                    }
                },
                addLevel() {
                    this.levels.push({ name: '', maxScore: 10 });
                    this.updateCells();
                },
                removeLevel(index) {
                    if (this.levels.length > 1) {
                        this.levels.splice(index, 1);
                        this.updateCells();
                    }
                },
                updateCells() {
                    const newCells = [];
                    for (let c = 0; c < this.criteria.length; c++) {
                        newCells[c] = [];
                        for (let l = 0; l < this.levels.length; l++) {
                            newCells[c][l] = (this.cells[c] && this.cells[c][l]) ? this.cells[c][l] : '';
                        }
                    }
                    this.cells = newCells;
                }
            }
        }
    </script>
    @endpush
</x-layouts.dashboard>
