@props(['rubric', 'submission', 'gradingRoute'])

@php
    $criteria = $rubric->criteria ?? [];
    $levels = $rubric->levels ?? [];
    $cells = $rubric->cells ?? [];
    $rubricJson = json_encode([
        'criteria' => $criteria,
        'levels' => $levels,
        'cells' => $cells,
    ]);
@endphp

<div x-data="rubricGrading({{ $rubricJson }})" class="inline">
    <button @click="open = true"
            class="text-sm bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-4 py-2 transition-colors duration-200">
        {{ __('Grade with Rubric') }}
    </button>

    <div x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @click.away="open = false">
        <div class="fixed inset-0 bg-black/60"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-2xl p-6 max-w-3xl w-full max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">{{ $rubric->title }}</h3>
                <button @click="open = false" class="text-gray-500 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 text-xs uppercase tracking-wider">
                            <th class="px-3 py-2">{{ __('Criterion') }}</th>
                            @foreach($levels as $level)
                                <th class="px-3 py-2 text-center">{{ is_object($level) ? $level->name : $level['name'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach($criteria as $criterionIndex => $criterion)
                            <tr>
                                <td class="px-3 py-3 text-gray-300 font-medium">{{ is_object($criterion) ? $criterion->name : $criterion['name'] }}</td>
                                @foreach($levels as $levelIndex => $level)
                                    @php
                                        $score = $cells[$criterionIndex][$levelIndex] ?? 0;
                                        $levelName = is_object($level) ? $level->name : $level['name'];
                                    @endphp
                                    <td class="px-3 py-3 text-center">
                                        <button type="button"
                                                @click="select({{ $criterionIndex }}, {{ $levelIndex }}, {{ $score }})"
                                                :class="selectedLevel({{ $criterionIndex }}, {{ $levelIndex }}) ? 'bg-brand-600 text-white' : 'bg-surface-700 text-gray-400 hover:bg-surface-600'"
                                                class="w-full rounded-lg px-3 py-2 text-sm font-medium transition-colors">
                                            {{ $score }}
                                            <span class="block text-[10px] opacity-70">{{ $levelName }}</span>
                                        </button>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between mt-4 pt-4 border-t border-white/10">
                <p class="text-white font-semibold">{{ __('Total') }}: <span class="text-brand-400 text-lg" x-text="totalScore"></span></p>
            </div>

            <form method="POST" :action="gradingRoute" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" name="score" x-model="totalScore">
                <div>
                    <label for="rubric-feedback" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Feedback') }}</label>
                    <textarea name="feedback" id="rubric-feedback" rows="4"
                              class="input-dashboard resize-none"
                              placeholder="{{ __('Write feedback...') }}"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false"
                            class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2">{{ __('Cancel') }}</button>
                    <button type="submit"
                            class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2 text-sm transition-colors duration-200">
                        {{ __('Submit Grade') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function rubricGrading(data) {
        return {
            open: false,
            selected: {},
            select(criterionIndex, levelIndex, score) {
                this.selected[criterionIndex] = { levelIndex, score };
            },
            selectedLevel(criterionIndex, levelIndex) {
                return this.selected[criterionIndex]?.levelIndex === levelIndex;
            },
            get totalScore() {
                let total = 0;
                for (const key in this.selected) {
                    const s = this.selected[key]?.score;
                    if (s !== undefined && s !== null) {
                        total += parseFloat(s) || 0;
                    }
                }
                return total.toFixed(1);
            }
        }
    }
</script>
@endpush
