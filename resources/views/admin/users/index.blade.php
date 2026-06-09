<x-layouts.dashboard>
    <x-slot name="title">{{ __('messages.user_management') }} — SAE LMS</x-slot>

    <div x-data="{
            addOpen: false, inviteOpen: false,
            search: '{{ request('search') }}',
            role: '{{ request('role') }}',
            program: '{{ request('program') }}',
            submit() {
                const params = new URLSearchParams();
                if (this.search) params.set('search', this.search);
                if (this.role) params.set('role', this.role);
                if (this.program) params.set('program', this.program);
                window.location = '{{ route('admin.users.index') }}?' + params.toString();
            },
            clearFilters() {
                this.search = '';
                this.role = '';
                this.program = '';
                this.submit();
            }
         }"
         x-effect="addOpen && $nextTick(() => $refs.addName.focus()); inviteOpen && $nextTick(() => $refs.inviteEmail.focus())"
         class="contents">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-white">{{ __('messages.users') }}</h1>
        <div class="flex items-center gap-2">
            <button @click="addOpen = true" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                {{ __('messages.add_user') }}
            </button>
            <button @click="inviteOpen = true" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                {{ __('messages.invite_user') }}
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 mb-6">
        {{-- Search --}}
        <div class="relative flex-1 max-w-md w-full">
            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 pointer-events-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" x-model="search" @input.debounce.500ms="submit()" placeholder="{{ __('messages.search_users') }}" class="w-full bg-surface-800 border border-white/10 text-white placeholder-gray-600 rounded-xl py-3 pl-12 pr-4 text-sm focus:outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500 transition-colors" autocomplete="off">
        </div>

        {{-- Role filter --}}
        <select x-model="role" @change="submit()" class="w-full sm:w-44 bg-surface-800 border border-white/20 text-white rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" style="color-scheme:dark">
            <option value="">{{ __('All Roles') }}</option>
            <option value="student">{{ __('messages.role_student') }}</option>
            <option value="instructor">{{ __('messages.role_instructor') }}</option>
            <option value="admin">{{ __('messages.role_admin') }}</option>
        </select>

        {{-- Program filter --}}
        <select x-model="program" @change="submit()" class="w-full sm:w-44 bg-surface-800 border border-white/20 text-white rounded-xl py-3 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" style="color-scheme:dark">
            <option value="">{{ __('All Programs') }}</option>
            @foreach($programs ?? [] as $prog)
                <option value="{{ $prog }}">{{ $prog }}</option>
            @endforeach
        </select>

        {{-- Clear filters --}}
        <button x-show="search || role || program" @click="clearFilters()" class="text-sm text-gray-400 hover:text-white transition-colors whitespace-nowrap px-3 py-2">
            {{ __('Clear Filters') }}
        </button>
    </div>

    {{-- Table --}}
    <div class="bg-surface-800 border border-white/10 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left text-gray-400 font-medium px-4 py-3">{{ __('messages.users') }}</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">{{ __('messages.role') }}</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">{{ __('messages.program') }}</th>
                        <th class="text-left text-gray-400 font-medium px-4 py-3">{{ __('messages.status') }}</th>
                        <th class="text-right text-gray-400 font-medium px-4 py-3">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($users as $user)
                        <tr class="hover:bg-surface-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.users.show', $user) }}" class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-brand-500/30 flex items-center justify-center text-xs font-bold text-brand-300 shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-white font-medium truncate">{{ $user->name }}</p>
                                        <p class="text-gray-500 text-xs truncate">{{ $user->email }}</p>
                                    </div>
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($user->role === 'admin') bg-purple-500/20 text-purple-400
                                    @elseif($user->role === 'instructor') bg-brand-500/20 text-brand-300
                                    @else bg-blue-500/20 text-blue-400
                                    @endif">
                                    @lang('messages.role_' . $user->role)
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-400">{{ $user->program ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($user->role === 'instructor')
                                    @if($user->verified_at)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/20 text-emerald-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ __('messages.verified') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-500/20 text-amber-400">{{ __('messages.pending') }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-600">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2" x-data="{ roleOpen: false }">
                                    {{-- Role dropdown --}}
                                    <div class="relative">
                                        <button @click="roleOpen = !roleOpen" class="text-xs text-gray-400 hover:text-white px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">{{ __('messages.change_role') }}</button>
                                        <div x-show="roleOpen" x-cloak @click.away="roleOpen = false" class="absolute right-0 mt-1 w-36 bg-surface-800 border border-white/20 rounded-xl shadow-xl overflow-hidden z-50 divide-y divide-white/10">
                                            <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="role" value="student">
                                                <button type="submit" class="block w-full text-left px-3 py-2.5 text-sm font-medium {{ $user->role === 'student' ? 'bg-blue-600 text-white' : 'bg-blue-600/20 text-blue-300 hover:bg-blue-600/30' }} transition-colors capitalize">{{ __('messages.role_student') }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="role" value="instructor">
                                                <button type="submit" class="block w-full text-left px-3 py-2.5 text-sm font-medium {{ $user->role === 'instructor' ? 'bg-brand-600 text-white' : 'bg-brand-600/20 text-brand-300 hover:bg-brand-600/30' }} transition-colors capitalize">{{ __('messages.role_instructor') }}</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.users.role', $user) }}">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="role" value="admin">
                                                <button type="submit" class="block w-full text-left px-3 py-2.5 text-sm font-medium {{ $user->role === 'admin' ? 'bg-purple-600 text-white' : 'bg-purple-600/20 text-purple-300 hover:bg-purple-600/30' }} transition-colors capitalize">{{ __('messages.role_admin') }}</button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Verify/Revoke --}}
                                    @if($user->role === 'instructor')
                                        @if(!$user->verified_at)
                                            <form method="POST" action="{{ route('admin.users.verify', $user) }}">
                                                @csrf
                                                <button type="submit" class="text-xs text-emerald-400 hover:text-emerald-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">{{ __('messages.verify_instructor') }}</button>
                                            </form>
                                        @endif
                                    @endif

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('{{ __('messages.confirm_delete_user') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 px-2 py-1 rounded-lg hover:bg-surface-600 transition-colors">{{ __('messages.delete') }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500">{{ __('messages.no_users_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $users->links() }}

    {{-- Add User Modal --}}
    <div x-show="addOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="addOpen = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-lg shadow-2xl" @click.away="addOpen = false">
            <h3 class="text-lg font-semibold text-white mb-4">{{ __('messages.add_user') }}</h3>
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="add_name" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.full_name') }}</label>
                        <input id="add_name" x-ref="addName" name="name" type="text" placeholder="{{ __('messages.full_name') }}" class="w-full bg-surface-700 border border-white/10 text-white placeholder-gray-600 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" required>
                    </div>
                    <div>
                        <label for="add_email" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.email_address') }}</label>
                        <input id="add_email" name="email" type="email" placeholder="user@example.com" class="w-full bg-surface-700 border border-white/10 text-white placeholder-gray-600 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="add_password" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.password') }}</label>
                        <input id="add_password" name="password" type="password" minlength="8" class="w-full bg-surface-700 border border-white/10 text-white rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" required>
                    </div>
                    <div>
                        <label for="add_role" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.role') }}</label>
                        <select id="add_role" name="role" class="w-full bg-surface-700 border border-white/20 text-white rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors" style="color-scheme:dark">
                            <option value="student">{{ __('messages.role_student') }}</option>
                            <option value="instructor">{{ __('messages.role_instructor') }}</option>
                            <option value="admin">{{ __('messages.role_admin') }}</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="add_program" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.program') }}</label>
                    <input id="add_program" name="program" type="text" placeholder="e.g. Film Production" class="w-full bg-surface-700 border border-white/10 text-white placeholder-gray-600 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors">
                </div>
                <div class="mb-4">
                    <label for="add_bio" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.bio') }}</label>
                    <textarea id="add_bio" name="bio" rows="2" placeholder="{{ __('messages.bio') }}" class="w-full bg-surface-700 border border-white/10 text-white placeholder-gray-600 rounded-xl py-2.5 px-4 text-sm focus:outline-none focus:border-brand-500 transition-colors"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="addOpen = false" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">{{ __('messages.create_user') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Invite User Modal --}}
    <div x-show="inviteOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="inviteOpen = false"></div>
        <div class="relative bg-surface-800 border border-white/10 rounded-xl p-6 w-full max-w-md shadow-2xl" @click.away="inviteOpen = false">
            <h3 class="text-lg font-semibold text-white mb-4">{{ __('messages.invite_user') }}</h3>
            <form method="POST" action="{{ route('admin.users.invite') }}">
                @csrf
                <div class="mb-4">
                    <label for="invite_email" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.email_address') }}</label>
                    <input id="invite_email" x-ref="inviteEmail" name="email" type="email" placeholder="user@example.com" class="input-dashboard">
                </div>
                <div class="mb-4">
                    <label for="invite_role" class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('messages.role') }}</label>
                    <select id="invite_role" name="role" class="input-dashboard">
                        <option value="student">{{ __('messages.role_student') }}</option>
                        <option value="instructor">{{ __('messages.role_instructor') }}</option>
                        <option value="admin">{{ __('messages.role_admin') }}</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="inviteOpen = false" class="text-sm text-gray-400 hover:text-white transition-colors px-4 py-2.5">{{ __('messages.cancel') }}</button>
                    <button type="submit" class="bg-brand-600 hover:bg-brand-500 text-white rounded-xl px-6 py-2.5 text-sm font-medium transition-colors">{{ __('messages.send_invite') }}</button>
                </div>
            </form>
        </div>
    </div>
    </div>
</x-layouts.dashboard>
