@extends('layouts.main')

@section('title', 'Edit Pengguna')

@section('subtitle', 'Ubah informasi pengguna')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('users.index') }}" class="w-9 h-9 rounded-lg border border-gray-200 flex items-center justify-center text-gray-400 hover:text-orange-500 hover:border-orange-200 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h1 class="text-lg font-semibold text-gray-900">Edit Pengguna</h1>
        <p class="text-sm text-gray-500">Ubah informasi pengguna</p>
    </div>
</div>

<div class="max-w-2xl">
    <div class="card">
        <div class="card-header">
            <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
                <span class="text-orange-700 font-bold text-sm">{{ substr($user->name, 0, 1) }}</span>
            </div>
            <div>
                <h3 class="card-title">{{ $user->name }}</h3>
                <p class="text-xs text-gray-500">{{ $user->email }} · Member of {{ $user->companies()->count() }} companies</p>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user) }}" x-data="{ selectedRole: '{{ old('role', $pivot->role ?? 'staff') }}' }">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-5 mb-5">
                    <div class="form-group">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input" placeholder="Nama lengkap">
                        @error('name')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input" placeholder="email@example.com">
                        @error('email')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5 mb-5">
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" min="8" class="form-input" placeholder="Kosongkan jika tidak diubah">
                        @error('password')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-input" placeholder="Ulangi password">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5 mb-5">
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <div class="grid grid-cols-2 gap-3 mt-1.5">
                            <label @click="selectedRole = 'admin'" :class="selectedRole === 'admin' ? 'border-orange-400 bg-orange-50 ring-2 ring-orange-200' : 'border-gray-200 hover:border-orange-300'" class="flex items-center p-4 border rounded-xl cursor-pointer transition-all">
                                <input type="radio" name="role" value="admin" x-model="selectedRole" class="sr-only">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0" :class="selectedRole === 'admin' ? 'border-orange-500' : 'border-gray-300'">
                                    <div x-show="selectedRole === 'admin'" class="w-2.5 h-2.5 rounded-full bg-orange-500"></div>
                                </div>
                                <div class="ms-3">
                                    <div class="text-sm font-semibold text-gray-800">Admin</div>
                                    <div class="text-xs text-gray-500">Dapat mengelola data & pengguna</div>
                                </div>
                            </label>
                            <label @click="selectedRole = 'staff'" :class="selectedRole === 'staff' ? 'border-orange-400 bg-orange-50 ring-2 ring-orange-200' : 'border-gray-200 hover:border-orange-300'" class="flex items-center p-4 border rounded-xl cursor-pointer transition-all">
                                <input type="radio" name="role" value="staff" x-model="selectedRole" class="sr-only">
                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0" :class="selectedRole === 'staff' ? 'border-orange-500' : 'border-gray-300'">
                                    <div x-show="selectedRole === 'staff'" class="w-2.5 h-2.5 rounded-full bg-orange-500"></div>
                                </div>
                                <div class="ms-3">
                                    <div class="text-sm font-semibold text-gray-800">Staff</div>
                                    <div class="text-xs text-gray-500">Hanya dapat mencatat transaksi</div>
                                </div>
                            </label>
                        </div>
                        @error('role')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select mt-1.5">
                            <option value="1" {{ old('is_active', $pivot->is_active ?? true) ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !old('is_active', $pivot->is_active ?? true) ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('is_active')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Multi Company Assignment --}}
                <div class="mb-5">
                    <label class="form-label">Akses Perusahaan</label>
                    <p class="form-hint mb-3">Atur perusahaan mana saja yang dapat diakses oleh pengguna ini.</p>
                    @if($companies->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        @foreach($companies as $cmp)
                        @php
                            $isMember = in_array($cmp->id, $userCompanyIds);
                        @endphp
                        <label class="flex items-center gap-3 p-3.5 border rounded-xl cursor-pointer transition-all
                            {{ $isMember ? 'border-orange-200 bg-orange-50/50' : 'border-gray-200 hover:border-orange-300 hover:bg-orange-50/30' }}
                            has-[:checked]:border-orange-400 has-[:checked]:bg-orange-50 has-[:checked]:ring-2 has-[:checked]:ring-orange-200">
                            <input type="checkbox" name="company_ids[]" value="{{ $cmp->id }}"
                                {{ $isMember ? 'checked' : '' }}
                                class="w-4 h-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400 shrink-0">
                            <div class="w-8 h-8 rounded-lg {{ $isMember ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600' }} flex items-center justify-center text-xs font-bold shrink-0">
                                {{ substr($cmp->name, 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $cmp->name }}</p>
                                <p class="text-xs {{ $isMember ? 'text-orange-600' : 'text-gray-400' }}">
                                    @if($isMember)
                                    {{ $user->companies()->where('company_id', $cmp->id)->first()?->pivot->role ?? $pivot->role }}
                                    @else
                                    Belum tergabung
                                    @endif
                                </p>
                            </div>
                            @if($isMember)
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0" title="Tergabung"></span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                    @else
                    <p class="text-sm text-gray-400 italic">Tidak ada perusahaan yang tersedia.</p>
                    @endif
                    @error('company_ids')<p class="form-error">{{ $message }}</p>@enderror
                    @error('company_ids.*')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="flex gap-3 pt-3 border-t border-gray-100">
                    <button type="submit" class="btn-primary">Simpan</button>
                    <a href="{{ route('users.index') }}" class="btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
