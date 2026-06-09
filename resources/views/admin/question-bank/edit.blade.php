<x-layouts.dashboard>
    <x-slot name="title">Edit Bank — SAE LMS</x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.question-bank.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-1 mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Banks
        </a>
        <h1 class="text-2xl font-bold text-white">Edit Bank</h1>
    </div>

    <div class="max-w-3xl">
        <form method="POST" action="{{ route('admin.question-bank.update', $questionBank) }}" class="space-y-6">
            @csrf @method('PUT')

            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6 space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-1.5">Bank Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $questionBank->name) }}" required class="input-dashboard">
                </div>

                @php
                    $courseOptions = $courses->map(fn($c) => ['id' => (string)$c->id, 'label' => $c->title])->values();
                    $selectedCourses = old('course_ids', $questionBank->courses->pluck('id')->map(fn($id) => (string)$id)->toArray());
                @endphp
                <div x-data="{
                    search: '',
                    open: false,
                    selected: {{ json_encode($selectedCourses) }},
                    items: {{ json_encode($courseOptions) }},
                    get filtered() {
                        return this.items.filter(c => c.label.toLowerCase().includes(this.search.toLowerCase()));
                    }
                }" class="relative">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Courses</label>
                    <input type="text" x-model="search" @click="open = true" @input="open = true"
                           placeholder="Search courses..."
                           class="w-full bg-surface-700 border border-surface-600 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-500/50 mb-2">
                    <div x-show="open" @click.outside="open = false" x-cloak
                         class="absolute z-10 mt-1 w-full bg-surface-700 border border-surface-600 rounded-lg shadow-xl max-h-48 overflow-y-auto">
                        <template x-for="course in filtered" :key="course.id">
                            <label class="flex items-center gap-3 px-3 py-2 hover:bg-surface-600 cursor-pointer border-b border-surface-600 last:border-0">
                                <input type="checkbox" x-model="selected" :value="course.id"
                                       class="rounded bg-surface-600 border-surface-500 text-brand-500 focus:ring-brand-500/50">
                                <span class="text-sm text-gray-200" x-text="course.label"></span>
                            </label>
                        </template>
                        <p x-show="filtered.length === 0" class="text-sm text-gray-500 px-3 py-4 text-center">No courses found.</p>
                    </div>
                    <div class="flex flex-wrap gap-1 mb-2" x-show="selected.length > 0">
                        <template x-for="id in selected" :key="id">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-brand-500/20 text-brand-400 text-xs">
                                <span x-text="items.find(c => c.id === id)?.label"></span>
                                <button @click="selected = selected.filter(s => s !== id)" type="button" class="hover:text-white">&times;</button>
                            </span>
                        </template>
                    </div>
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="course_ids[]" :value="id">
                    </template>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_visible_to_all" value="1" id="is_visible_to_all" class="accent-brand-500"
                        {{ old('is_visible_to_all', $questionBank->is_visible_to_all) ? 'checked' : '' }}>
                    <label for="is_visible_to_all" class="text-sm text-gray-400">Share with all courses (global bank)</label>
                </div>
            </div>

            <div class="bg-surface-800 border border-white/10 rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-white mb-3">Questions ({{ $questionBank->items->count() }})</h3>
                <div class="space-y-2">
                    @foreach($questionBank->items as $item)
                        <div class="flex items-center justify-between bg-surface-700 rounded-xl px-4 py-3 border border-white/5">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="text-[11px] font-medium px-2 py-0.5 rounded-full shrink-0
                                    {{ $item->type === 'multiple_choice' ? 'bg-brand-500/10 text-brand-400' : '' }}
                                    {{ $item->type === 'true_false' ? 'bg-emerald-500/10 text-emerald-400' : '' }}
                                    {{ $item->type === 'short_answer' ? 'bg-blue-500/10 text-blue-400' : '' }}
                                    {{ $item->type === 'long_answer' ? 'bg-purple-500/10 text-purple-400' : '' }}">
                                    {{ str_replace('_', ' ', ucfirst($item->type)) }}
                                </span>
                                <span class="text-sm text-gray-300 truncate">{{ $item->question }}</span>
                            </div>
                            <span class="text-xs text-gray-500 shrink-0 ml-3">{{ $item->points }} pts</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white font-semibold rounded-xl px-6 py-2.5 transition-colors duration-200">Update Bank</button>
                <a href="{{ route('admin.question-bank.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors">Cancel</a>
            </div>
        </form>
    </div>
</x-layouts.dashboard>