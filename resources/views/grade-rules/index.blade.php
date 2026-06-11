<x-layouts.dashboard>
    <x-slot name="title">{{ $course->title }} — {{ __('Grade Rules') }}</x-slot>

    @php
        $isInstructor = auth()->user()->isInstructor() && $course->instructor_id === auth()->id();
        $isAdmin = auth()->user()->isAdmin();
        $canManage = $isInstructor || $isAdmin;
    @endphp

    <div class="mb-6">
        <a href="{{ route('courses.show', $course) }}" class="text-sm text-gray-400 hover:text-white transition-colors">&larr; {{ __('Back to course') }}</a>
        <div class="flex items-center justify-between mt-1">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ __('Automated Grading Rules') }}</h1>
                <p class="text-sm text-gray-500 mt-1">{{ __('Set weighted percentages for each category. Total should equal 100%.') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('courses.grade-rules.export', $course) }}" class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Export CSV
                </a>
                <a href="{{ route('courses.grade-rules.export-example', $course) }}" class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-white px-3 py-1.5 rounded-lg hover:bg-surface-700 transition-colors border border-white/10">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Example CSV
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-500/10 border border-emerald-500/20 rounded-lg text-sm text-emerald-400">{{ session('success') }}</div>
    @endif

    @if($canManage)
        <form method="POST" action="{{ route('courses.grade-rules.update', $course) }}">
            @csrf
            <div class="bg-surface-800 border border-surface-700 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-surface-700">
                    <h3 class="text-sm font-semibold text-white">{{ __('Category Weights') }}</h3>
                </div>

                <div class="divide-y divide-surface-700">
                    @foreach($categories as $cat)
                        @php
                            $labels = ['quiz' => __('Quizzes'), 'assignment' => __('Assignments'), 'attendance' => __('Attendance')];
                            $icons = [
                                'quiz' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                'assignment' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                                'attendance' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                            ];
                        @endphp
                        <div class="px-5 py-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <svg class="w-5 h-5 text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icons[$cat] }}"/></svg>
                                <span class="text-sm font-medium text-white">{{ $labels[$cat] }}</span>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <input type="number" name="weights[{{ $cat }}]" value="{{ old('weights.' . $cat, $weights[$cat]) }}"
                                       min="0" max="100" step="0.01" required
                                       class="w-24 bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-sm text-gray-200 text-right focus:outline-none focus:ring-2 focus:ring-brand-500/50">
                                <span class="text-sm text-gray-400 w-4">%</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-5 py-4 border-t border-surface-700 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">{{ __('Total') }}</span>
                        <span class="text-sm font-semibold text-white" x-data="{ total: 0 }" x-init="
                            $watch('total', val => {
                                $el.style.color = Math.abs(val - 100) < 0.01 ? '#34d399' : '#f87171';
                            });
                            const inputs = document.querySelectorAll('[name^=\"weights\"]');
                            const update = () => {
                                total = Array.from(inputs).reduce((s, i) => s + (parseFloat(i.value) || 0), 0);
                                $el.textContent = total.toFixed(2) + '%';
                            };
                            inputs.forEach(i => i.addEventListener('input', update));
                            update();
                        ">0%</span>
                    </div>
                    <button type="submit" class="text-sm bg-brand-500 hover:bg-brand-600 text-white font-medium px-5 py-2 rounded-lg transition-colors">
                        {{ __('Save Rules') }}
                    </button>
                </div>
            </div>
        </form>
    @endif
</x-layouts.dashboard>
