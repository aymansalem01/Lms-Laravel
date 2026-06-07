<x-layouts.dashboard>
    <x-slot name="title">{{ __('Grading') }} — {{ $submission->student->name }}</x-slot>

    @php
        $assignment = $submission->assignment;
        $grade = $submission->grade;
    @endphp

    <div class="mb-6">
        <a href="{{ route('grading.index') }}" class="text-sm text-gray-400 hover:text-brand-300 transition-colors flex items-center gap-1.5 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            {{ __('Back to grading queue') }}
        </a>
        <h1 class="text-2xl font-bold text-white">{{ $assignment->title }}</h1>
        <p class="text-sm text-gray-400 mt-1">{{ $assignment->course->title }} — {{ $submission->student->name }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left: Submission Content --}}
        <div class="space-y-6">
            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full gb flex items-center justify-center text-white text-sm font-bold">{{ strtoupper(substr($submission->student->name, 0, 1)) }}</div>
                    <div>
                        <h2 class="text-white font-semibold">{{ $submission->student->name }}</h2>
                        <p class="text-xs text-gray-500">{{ __('Submitted') }} {{ $submission->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    @if($submission->file_path)
                        <a href="{{ Storage::url($submission->file_path) }}" target="_blank"
                           class="flex items-center gap-3 bg-surface-700 rounded-xl px-4 py-3 hover:bg-surface-600 transition-colors group">
                            <svg class="w-5 h-5 text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <div class="min-w-0">
                                <p class="text-sm text-gray-300 group-hover:text-white transition-colors truncate">{{ basename($submission->file_path) }}</p>
                                <p class="text-xs text-gray-500">{{ __('Download file') }}</p>
                            </div>
                        </a>
                    @endif

                    @if($submission->link)
                        <a href="{{ $submission->link }}" target="_blank"
                           class="flex items-center gap-3 bg-surface-700 rounded-xl px-4 py-3 hover:bg-surface-600 transition-colors group">
                            <svg class="w-5 h-5 text-brand-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <div class="min-w-0">
                                <p class="text-sm text-gray-300 group-hover:text-white transition-colors truncate">{{ $submission->link }}</p>
                                <p class="text-xs text-gray-500">{{ __('Open link') }}</p>
                            </div>
                        </a>
                    @endif

                    @if($submission->notes)
                        <div class="bg-surface-700 rounded-xl px-4 py-3">
                            <p class="text-xs text-gray-500 mb-1.5">{{ __('Submission Notes') }}</p>
                            <p class="text-sm text-gray-300 whitespace-pre-wrap">{{ $submission->notes }}</p>
                        </div>
                    @endif

                    {{-- Video Embed --}}
                    @if($submission->file_path && preg_match('/\.(mp4|webm|ogg)$/i', $submission->file_path))
                        <div class="bg-surface-700 rounded-xl p-2">
                            <p class="text-xs text-gray-500 mb-2 px-2">{{ __('Submitted Video') }}</p>
                            <video controls class="w-full rounded-lg">
                                <source src="{{ Storage::url($submission->file_path) }}">
                            </video>
                        </div>
                    @endif

                    {{-- Audio Player --}}
                    @if($submission->file_path && preg_match('/\.(mp3|wav|ogg|aac)$/i', $submission->file_path))
                        <div class="bg-surface-700 rounded-xl p-4">
                            <p class="text-xs text-gray-500 mb-2">{{ __('Submitted Audio') }}</p>
                            <audio controls class="w-full">
                                <source src="{{ Storage::url($submission->file_path) }}">
                            </audio>
                        </div>
                    @endif
                </div>
            </div>

            <x-plagiarism-badge :report="$submission->plagiarismReport" />

            {{-- Rubric Grading --}}
            @if($assignment->rubric)
                <div class="bg-surface-800 border border-white/10 rounded-2xl p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-white">{{ __('Rubric Grading') }}</h3>
                        <x-rubric-grading-modal
                            :rubric="$assignment->rubric"
                            :submission="$submission"
                            gradingRoute="{{ route('grading.store', $submission) }}"
                        />
                    </div>
                    <p class="text-xs text-gray-500">{{ $assignment->rubric->title }} — {{ count($assignment->rubric->criteria ?? []) }} {{ __('criteria') }}</p>
                </div>
            @endif

            {{-- Activity Log --}}
            <div class="bg-surface-800 border border-white/10 rounded-2xl p-5">
                <h3 class="text-sm font-semibold text-white mb-4">{{ __('Activity Log') }}</h3>
                <div class="space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-brand-500 mt-1.5 shrink-0"></div>
                        <div>
                            <p class="text-sm text-gray-300">{{ __('Assignment created') }}</p>
                            <p class="text-xs text-gray-500">{{ $assignment->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 rounded-full bg-green-500 mt-1.5 shrink-0"></div>
                        <div>
                            <p class="text-sm text-gray-300">{{ __('Submission received') }}</p>
                            <p class="text-xs text-gray-500">{{ $submission->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                    @if($grade && $grade->is_published)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-yellow-500 mt-1.5 shrink-0"></div>
                            <div>
                                <p class="text-sm text-gray-300">{{ __('Grade published') }}</p>
                                <p class="text-xs text-gray-500">{{ $grade->updated_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right: Grade Form --}}
        <div class="space-y-6">
            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                <h2 class="text-lg font-semibold text-white mb-4">{{ __('Grade Assignment') }}</h2>
                <form method="POST" action="{{ route('grading.store', $submission) }}" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-3">{{ __('Score') }}</label>
                        <div x-data="{ score: {{ old('score', $grade->score ?? 0) }} }">
                            <div class="flex items-center gap-4 mb-2">
                                <input type="range" name="score" x-model.number="score" min="0" max="{{ $assignment->max_score }}" step="0.1"
                                       class="flex-1 accent-brand-500 h-2 bg-surface-700 rounded-full appearance-none cursor-pointer">
                                <div class="w-16 h-16 rounded-xl gb flex items-center justify-center shrink-0">
                                    <span class="text-lg font-bold text-white" x-text="score"></span>
                                </div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>0</span>
                                <span>{{ __('out of') }} {{ $assignment->max_score }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="feedback" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Feedback') }}</label>
                        <textarea name="feedback" id="feedback" rows="6"
                                  class="input-dashboard resize-none"
                                  placeholder="{{ __('Write detailed feedback for the student...') }}">{{ old('feedback', $grade->feedback ?? '') }}</textarea>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" name="action" value="publish"
                                class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ __('Release Grade') }}
                        </button>
                        <button type="submit" name="action" value="draft"
                                class="bg-surface-700 hover:bg-surface-600 text-white font-medium rounded-xl px-6 py-2.5 transition-colors duration-200">
                            {{ __('Save as Draft') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.dashboard>
