@extends('layouts.main')

@section('title', 'Roles & Pengguna')
@section('subtitle', 'Kelola pengguna dan hak akses dalam perusahaan.')

@section('content')
<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="card-title">Daftar Pengguna — {{ $company->name }}</h3>
    </div>

    @if($users->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50">
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $u)
                <tr class="hover:bg-orange-50/30 transition-colors">
                    <form method="POST" action="{{ route('roles.update', $u) }}">
                        @csrf @method('PUT')
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                                    <span class="text-orange-700 font-bold text-xs">{{ substr($u->name, 0, 1) }}</span>
                                </div>
                                <span class="text-sm font-medium text-gray-800">{{ $u->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-gray-600">{{ $u->email }}</td>
                        <td class="px-5 py-3.5">
                            <select name="role" class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-orange-200 focus:border-orange-400 outline-none">
                                <option value="owner" {{ $u->pivot->role === 'owner' ? 'selected' : '' }}>Owner</option>
                                <option value="admin" {{ $u->pivot->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="staff" {{ $u->pivot->role === 'staff' ? 'selected' : '' }}>Staff</option>
                            </select>
                        </td>
                        <td class="px-5 py-3.5">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ $u->pivot->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                                <span class="text-sm {{ $u->pivot->is_active ? 'text-emerald-600 font-medium' : 'text-gray-400' }}">
                                    {{ $u->pivot->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </label>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button type="submit"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition-colors"
                                    title="Simpan">
                                    Simpan
                                </button>
                                @if($u->id !== Auth::id())
                                <a href="{{ route('roles.remove', $u) }}"
                                    @click.prevent="$store.confirm.ask('Hapus Pengguna', 'Hapus pengguna ini dari perusahaan?', { confirmText: 'Ya, hapus', confirmClass: 'btn-danger', action: () => window.location = '{{ route('roles.remove', $u) }}' })"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors"
                                    title="Hapus">
                                    Hapus
                                </a>
                                @endif
                            </div>
                        </td>
                    </form>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="py-12 text-center">
        <div class="w-14 h-14 mx-auto mb-4 rounded-xl bg-orange-100 flex items-center justify-center">
            <svg class="w-7 h-7 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
        </div>
        <p class="text-sm font-semibold text-gray-900 mb-1">Belum Ada Pengguna</p>
        <p class="text-sm text-gray-500 mb-5">Tambahkan pengguna untuk mulai berkolaborasi dalam tim.</p>
    </div>
    @endif
</div>
@endsection
