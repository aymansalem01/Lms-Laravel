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
