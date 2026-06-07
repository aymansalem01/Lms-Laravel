<x-layouts.dashboard>
    <x-slot name="title">{{ __('Create Rubric') }}</x-slot>

    <div class="mb-6">
        <a href="{{ route('courses.rubrics.index', $course) }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to rubrics') }}
        </a>
        <h1 class="text-2xl font-bold text-white">{{ __('Create Rubric') }}</h1>
        <p class="text-sm text-gray-400 mt-1">{{ __('Design a grading rubric with criteria and performance levels') }}</p>
    </div>

    <div class="max-w-5xl">
        <form method="POST" action="{{ route('courses.rubrics.store', $course) }}" class="space-y-6">
            @csrf

            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Rubric Title') }}</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           class="input-dashboard {{ $errors->has('title') ? 'border-red-500' : '' }}"
                           placeholder="{{ __('e.g. Essay Grading Rubric') }}">
                    @error('title') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div x-data="rubricBuilder()" x-init="init()" class="space-y-6">
                {{-- Criteria & Levels Management --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-white">{{ __('Criteria') }}</h2>
                            <button type="button" @click="addCriterion()"
                                    class="text-sm text-brand-400 hover:text-brand-300 transition-colors flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                {{ __('Add Criterion') }}
                            </button>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(criterion, index) in criteria" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="text" x-model="criterion.name" :name="'criteria['+index+'][name]'"
                                           class="flex-1 bg-surface-700 border border-white/10 text-white rounded-xl py-2 px-3 text-sm focus:outline-none focus:border-brand-500"
                                           placeholder="{{ __('Criterion name') }}">
                                    <button type="button" @click="removeCriterion(index)" x-show="criteria.length > 1"
                                            class="text-red-400 hover:text-red-300 p-1 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <p class="text-xs text-gray-500 mt-3">{{ __('Define the criteria you want to evaluate.') }}</p>
                    </div>

                    <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-white">{{ __('Levels') }}</h2>
                            <button type="button" @click="addLevel()"
                                    class="text-sm text-brand-400 hover:text-brand-300 transition-colors flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                {{ __('Add Level') }}
                            </button>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(level, index) in levels" :key="index">
                                <div class="flex items-center gap-2">
                                    <input type="text" x-model="level.name" :name="'levels['+index+'][name]'"
                                           class="flex-1 bg-surface-700 border border-white/10 text-white rounded-xl py-2 px-3 text-sm focus:outline-none focus:border-brand-500"
                                           placeholder="{{ __('Level name') }}">
                                    <input type="number" x-model="level.maxScore" :name="'levels['+index+'][max_score]'"
                                           class="w-20 bg-surface-700 border border-white/10 text-white rounded-xl py-2 px-3 text-sm focus:outline-none focus:border-brand-500 text-center"
                                           placeholder="{{ __('Max') }}">
                                    <button type="button" @click="removeLevel(index)" x-show="levels.length > 1"
                                            class="text-red-400 hover:text-red-300 p-1 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                        <p class="text-xs text-gray-500 mt-3">{{ __('Define performance levels with max scores.') }}</p>
                    </div>
                </div>

                {{-- Rubric Matrix --}}
                <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                    <h2 class="text-lg font-semibold text-white mb-4">{{ __('Rubric Matrix') }}</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left">
                                    <th class="px-3 py-2 text-gray-500 text-xs uppercase tracking-wider min-w-[150px]">{{ __('Criteria / Levels') }}</th>
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
                                                       :name="'scores['+cIdx+']['+lIdx+']'"
                                                       :placeholder="'0-' + level.maxScore"
                                                       class="w-full bg-surface-700 border border-white/10 text-white rounded-lg py-1.5 px-2 text-xs text-center focus:outline-none focus:border-brand-500"
                                                       x-model="scores[cIdx] ? (scores[cIdx][lIdx] || '') : ''">
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-gray-500 mt-3">{{ __('Enter scores for each criterion-level combination. Each score should be between 0 and the level\'s max score.') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200">
                    {{ __('Create Rubric') }}
                </button>
                <a href="{{ route('courses.rubrics.index', $course) }}"
                   class="text-sm text-gray-400 hover:text-white transition-colors">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function rubricBuilder() {
            return {
                criteria: [{ name: '' }],
                levels: [{ name: '', maxScore: 10 }],
                scores: [[]],
                init() {
                    this.updateScores();
                },
                addCriterion() {
                    this.criteria.push({ name: '' });
                    this.updateScores();
                },
                removeCriterion(index) {
                    if (this.criteria.length > 1) {
                        this.criteria.splice(index, 1);
                        this.updateScores();
                    }
                },
                addLevel() {
                    this.levels.push({ name: '', maxScore: 10 });
                    this.updateScores();
                },
                removeLevel(index) {
                    if (this.levels.length > 1) {
                        this.levels.splice(index, 1);
                        this.updateScores();
                    }
                },
                updateScores() {
                    const newScores = [];
                    for (let c = 0; c < this.criteria.length; c++) {
                        newScores[c] = [];
                        for (let l = 0; l < this.levels.length; l++) {
                            newScores[c][l] = (this.scores[c] && this.scores[c][l]) ? this.scores[c][l] : '';
                        }
                    }
                    this.scores = newScores;
                }
            }
        }
    </script>
    @endpush
</x-layouts.dashboard>
