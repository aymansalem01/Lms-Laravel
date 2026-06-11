<x-layouts.dashboard>
    <x-slot name="title">{{ __('Attendance') }} — {{ $course->title }}</x-slot>

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ __('Attendance') }}</h1>
            <p class="text-gray-400 text-sm mt-1">{{ $course->title }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('courses.attendance.export-example', $course) }}" class="inline-flex items-center gap-1.5 bg-surface-600 hover:bg-surface-500 text-gray-300 rounded-xl px-3 py-2 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Example CSV
            </a>
            <form method="POST" action="{{ route('courses.attendance.import', $course) }}" enctype="multipart/form-data" class="inline">
                @csrf
                <label class="inline-flex items-center gap-1.5 bg-surface-600 hover:bg-surface-500 text-gray-300 rounded-xl px-3 py-2 text-sm font-medium transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Import CSV
                    <input type="file" name="csv_file" accept=".csv,.txt" class="hidden" onchange="this.form.submit()">
                </label>
            </form>
            <a href="{{ route('courses.attendance.export', $course) }}" class="inline-flex items-center gap-1.5 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-3 py-2 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Export CSV
            </a>
            <a href="{{ route('courses.show', $course) }}"
               class="text-sm text-brand-400 hover:text-brand-300 transition-colors">&larr; {{ __('Back to Course') }}</a>
        </div>
    </div>

    <div x-data="attendanceApp()" x-init="init()" class="space-y-6">
        <div class="bg-surface-800 border border-white/10 rounded-xl p-6">
            <div class="flex items-end gap-4 flex-wrap">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-400 mb-1.5">{{ __('Select Date') }}</label>
                    <input type="date" x-model="selectedDate"
                           class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500">{{ __('Total') }}: <span class="text-white font-medium" x-text="students.length"></span></span>
                    <span class="text-xs text-green-400">{{ __('Present') }}: <span x-text="presentCount"></span></span>
                    <span class="text-xs text-red-400">{{ __('Absent') }}: <span x-text="absentCount"></span></span>
                    <span class="text-xs text-yellow-400">{{ __('Late') }}: <span x-text="lateCount"></span></span>
                    <span class="text-xs text-blue-400">{{ __('Excused') }}: <span x-text="excusedCount"></span></span>
                </div>
            </div>
        </div>

        <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-white/10 bg-surface-700/50">
                            <th class="text-left px-5 py-3 text-gray-400 font-medium">{{ __('Student') }}</th>
                            <th class="text-center px-4 py-3 text-gray-400 font-medium">{{ __('Present') }}</th>
                            <th class="text-center px-4 py-3 text-gray-400 font-medium">{{ __('Absent') }}</th>
                            <th class="text-center px-4 py-3 text-gray-400 font-medium">{{ __('Late') }}</th>
                            <th class="text-center px-4 py-3 text-gray-400 font-medium">{{ __('Excused') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(student, index) in students" :key="student.id">
                            <tr class="border-b border-white/5 hover:bg-surface-700/30 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-brand-500/20 flex items-center justify-center text-xs font-bold text-brand-300">
                                            <span x-text="student.initials"></span>
                                        </div>
                                        <span class="text-white font-medium" x-text="student.name"></span>
                                    </div>
                                </td>
                                <td class="text-center px-4 py-3">
                                    <input type="radio" :name="'status_' + student.id" value="present"
                                           x-model="attendance[student.id]"
                                           class="w-4 h-4 text-green-500 bg-surface-700 border-white/10 focus:ring-green-500 focus:ring-offset-0 cursor-pointer">
                                </td>
                                <td class="text-center px-4 py-3">
                                    <input type="radio" :name="'status_' + student.id" value="absent"
                                           x-model="attendance[student.id]"
                                           class="w-4 h-4 text-red-500 bg-surface-700 border-white/10 focus:ring-red-500 focus:ring-offset-0 cursor-pointer">
                                </td>
                                <td class="text-center px-4 py-3">
                                    <input type="radio" :name="'status_' + student.id" value="late"
                                           x-model="attendance[student.id]"
                                           class="w-4 h-4 text-yellow-500 bg-surface-700 border-white/10 focus:ring-yellow-500 focus:ring-offset-0 cursor-pointer">
                                </td>
                                <td class="text-center px-4 py-3">
                                    <input type="radio" :name="'status_' + student.id" value="excused"
                                           x-model="attendance[student.id]"
                                           class="w-4 h-4 text-blue-500 bg-surface-700 border-white/10 focus:ring-blue-500 focus:ring-offset-0 cursor-pointer">
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="border-t border-white/10 px-5 py-4 flex items-center justify-between">
                <button @click="clearAll" class="text-sm text-gray-500 hover:text-white transition-colors">{{ __('Clear All') }}</button>
                <button @click="saveAll"
                        class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-8 py-2.5 text-sm font-medium transition-colors"
                        :disabled="!selectedDate">
                    {{ __('Save All') }}
                </button>
            </div>
        </div>

        <form method="POST" :action="saveUrl" id="attendance-form" class="hidden">
            @csrf
            <input type="hidden" name="date" :value="selectedDate">
            <template x-for="(status, studentId) in attendance" :key="studentId">
                <template x-if="status">
                    <input type="hidden" :name="'attendance[' + studentId + '][student_id]'" :value="studentId">
                    <input type="hidden" :name="'attendance[' + studentId + '][status]'" :value="status">
                </template>
            </template>
        </form>
    </div>

    @push('scripts')
    <script>
        function attendanceApp() {
            return {
                students: {!! json_encode($students->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'initials' => strtoupper(substr($s->name, 0, 1))])) !!},
                records: {!! json_encode($records) !!},
                selectedDate: new Date().toISOString().split('T')[0],
                attendance: {},
                saveUrl: '{{ route('courses.attendance.bulk', $course) }}',

                init() {
                    this.loadAttendance();
                },

                loadAttendance() {
                    this.attendance = {};
                    let dateRecords = this.records[this.selectedDate] || {};
                    this.students.forEach(s => {
                        this.attendance[s.id] = dateRecords[s.id] || null;
                    });
                },

                saveAll() {
                    document.getElementById('attendance-form').submit();
                },

                clearAll() {
                    this.students.forEach(s => {
                        this.attendance[s.id] = null;
                    });
                },

                get presentCount() {
                    return Object.values(this.attendance).filter(v => v === 'present').length;
                },

                get absentCount() {
                    return Object.values(this.attendance).filter(v => v === 'absent').length;
                },

                get lateCount() {
                    return Object.values(this.attendance).filter(v => v === 'late').length;
                },

                get excusedCount() {
                    return Object.values(this.attendance).filter(v => v === 'excused').length;
                },
            }
        }
    </script>
    @endpush
</x-layouts.dashboard>
