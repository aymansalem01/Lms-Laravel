<x-layouts.dashboard>
    <x-slot name="title">Lesson Management — Luminus LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Lessons</h1>
        <button @click="$dispatch('open-create-lesson')" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Create Lesson
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Title</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Module</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">Course</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Order</th>
                        <th class="text-center text-gray-400 font-medium px-4 py-3">Type</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($lessons as $lesson)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.lessons.show', $lesson) }}" class="text-white font-medium hover:text-brand-300 transition-colors">{{ $lesson->title }}</a>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $lesson->module->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-400">{{ $lesson->module->course->title ?? '—' }}</td>
                            <td class="px-4 py-3 text-center text-gray-400">{{ $lesson->order_index ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $type = 'text';
                                    if($lesson->video_url) $type = 'video';
                                    elseif($lesson->audio_url) $type = 'audio';
                                    elseif($lesson->file_url) $type = 'file';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($type === 'video') bg-purple-500/20 text-purple-400
                                    @elseif($type === 'audio') bg-blue-500/20 text-blue-400
                                    @elseif($type === 'file') bg-amber-500/20 text-amber-400
                                    @else bg-brand-500/20 text-brand-300
                                    @endif">
                                    {{ ucfirst($type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.lessons.show', $lesson) }}" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">View</a>
                                    <form method="POST" action="{{ route('admin.lessons.destroy', $lesson) }}" onsubmit="return confirm('Delete this lesson permanently?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-gray-500">No lessons found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $lessons->links() }}

    {{-- Create Lesson Modal --}}
    <div x-data="{ open: false }" @open-create-lesson.window="open = true" x-show="open" x-cloak
         x-effect="open && $nextTick(() => $refs.lessonModule.focus())"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-lg shadow-2xl">
            <h3 class="text-lg font-semibold text-white mb-4">Create Lesson</h3>
            <form method="POST" action="{{ route('admin.lessons.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="lesson_module_id" class="block text-sm font-medium text-gray-300 mb-1.5">Module</label>
                    <select id="lesson_module_id" x-ref="lessonModule" name="module_id" class="input-dashboard">
                        <option value="">Select module...</option>
                        @foreach($modules ?? [] as $module)
                            <option value="{{ $module->id }}">{{ $module->title }} ({{ $module->course->title ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="lesson_title" class="block text-sm font-medium text-gray-300 mb-1.5">Title</label>
                    <input id="lesson_title" name="title" type="text" placeholder="Lesson title" class="input-dashboard">
                </div>
                <div class="mb-4">
                    <label for="lesson_content" class="block text-sm font-medium text-gray-300 mb-1.5">Content</label>
                    <textarea id="lesson_content" name="content" rows="4" placeholder="Lesson content..." class="input-dashboard resize-none"></textarea>
                </div>
                <div class="mb-4">
                    <label for="lesson_video_url" class="block text-sm font-medium text-gray-300 mb-1.5">Video URL</label>
                    <input id="lesson_video_url" name="video_url" type="url" placeholder="https://..." class="input-dashboard">
                </div>
                <div class="mb-4">
                    <label for="lesson_audio_url" class="block text-sm font-medium text-gray-300 mb-1.5">Audio URL</label>
                    <input id="lesson_audio_url" name="audio_url" type="url" placeholder="https://..." class="input-dashboard">
                </div>
                <div class="mb-4">
                    <label for="lesson_file_url" class="block text-sm font-medium text-gray-300 mb-1.5">File URL</label>
                    <input id="lesson_file_url" name="file_url" type="url" placeholder="https://..." class="input-dashboard">
                </div>
                <div class="mb-4">
                    <label for="lesson_order_index" class="block text-sm font-medium text-gray-300 mb-1.5">Order Index</label>
                    <input id="lesson_order_index" name="order_index" type="number" placeholder="0" class="input-dashboard">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">Cancel</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Create</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>
