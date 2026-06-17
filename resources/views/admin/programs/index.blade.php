<x-layouts.dashboard>
    <x-slot name="title">Program Management — Luminus LMS</x-slot>

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">Programs</h1>
        <button @click="$dispatch('open-add-program')" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Program
        </button>
    </div>

    {{-- Program Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($programs as $program)
            <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-500/20 flex items-center justify-center text-brand-300 font-bold">{{ strtoupper(substr($program->name, 0, 1)) }}</div>
                    <div class="flex gap-1">
                        <a href="{{ route('admin.programs.edit', $program) }}" class="p-1.5 rounded-lg text-gray-400 hover:text-white hover:bg-surface-700 transition-colors" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form method="POST" action="{{ route('admin.programs.destroy', $program) }}" onsubmit="return confirm('Delete this program?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-400 hover:bg-surface-700 transition-colors" title="Delete">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
                <h3 class="text-white font-semibold">{{ $program->name }}</h3>
                @if($program->description)
                    <p class="text-gray-400 text-sm mt-1 line-clamp-2">{{ $program->description }}</p>
                @endif

                {{-- Assigned Courses --}}
                <div class="mt-4 pt-4 border-t border-white/10">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-gray-500 font-medium uppercase tracking-wider">Courses</span>
                        <button @click="$dispatch('assign-course', { programId: {{ $program->id }} })" class="text-xs text-brand-400 hover:text-brand-300 transition-colors">Assign</button>
                    </div>
                    @forelse($program->courses->take(3) as $course)
                        <div class="flex items-center justify-between py-1.5">
                            <span class="text-sm text-gray-300 truncate">{{ $course->title }}</span>
                            <form method="POST" action="{{ route('admin.programs.courses.unassign', [$program, $course]) }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-gray-500 hover:text-red-400 transition-colors">&times;</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-600">No courses assigned.</p>
                    @endforelse
                    @if($program->courses->count() > 3)
                        <p class="text-xs text-gray-500 mt-1">+{{ $program->courses->count() - 3 }} more</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-surface-800 border border-white/10 rounded-xl p-10 text-center">
                <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <p class="text-gray-400 font-medium">No programs yet</p>
                <p class="text-gray-600 text-sm mt-1">Create your first program to organize courses.</p>
            </div>
        @endforelse
    </div>

    {{-- Add Program Modal --}}
    <div x-data="{ open: false }" @open-add-program.window="open = true" x-show="open" x-cloak
         x-effect="open && $nextTick(() => $refs.programName.focus())"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-md shadow-2xl">
            <h3 class="text-lg font-semibold text-white mb-4">Add Program</h3>
            <form method="POST" action="{{ route('admin.programs.store') }}">
                @csrf
                <div class="mb-4">
                    <label for="program_name" class="block text-sm font-medium text-gray-300 mb-1.5">Name</label>
                    <input id="program_name" x-ref="programName" name="name" type="text" placeholder="e.g. Film Production" class="w-full bg-surface-700 border border-white/20 text-white placeholder-gray-500 rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors">
                </div>
                <div class="mb-4">
                    <label for="program_description" class="block text-sm font-medium text-gray-300 mb-1.5">Description</label>
                    <textarea id="program_description" name="description" rows="3" placeholder="Brief description of the program..." class="w-full bg-surface-700 border border-white/20 text-white placeholder-gray-500 rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors resize-none"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">Cancel</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">Create Program</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.dashboard>
