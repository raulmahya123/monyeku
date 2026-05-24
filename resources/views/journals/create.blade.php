@extends('layouts.main')

@section('title', 'Buat Jurnal Manual')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('journals.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 text-gray-400 hover:text-orange-500 hover:border-orange-200 hover:bg-orange-50 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
    </a>
    <div>
        <h2 class="text-lg font-bold text-gray-800">Buat Jurnal Manual</h2>
        <p class="text-xs text-gray-400">Entri jurnal penyesuaian atau koreksi manual</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
            </div>
            <h3 class="card-title">Entri Jurnal</h3>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('journals.store') }}" x-data="{
            lines: [{ coa_id: '', debit: '', credit: '', description: '' }],
            addLine() { this.lines.push({ coa_id: '', debit: '', credit: '', description: '' }); },
            removeLine(i) { if (this.lines.length > 1) this.lines.splice(i, 1); },
            totalDebit() { return this.lines.reduce((s, l) => s + (parseFloat(l.debit) || 0), 0); },
            totalCredit() { return this.lines.reduce((s, l) => s + (parseFloat(l.credit) || 0), 0); },
            isBalanced() { return Math.abs(this.totalDebit() - this.totalCredit()) < 0.01; }
        }">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tanggal</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Deskripsi</label>
                    <input type="text" name="description" required maxlength="500" placeholder="Contoh: Jurnal Penyesuaian" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Catatan (opsional)</label>
                <input type="text" name="notes" maxlength="1000" placeholder="Keterangan tambahan" class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
            </div>

            <div class="mt-6">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Baris Jurnal</label>
                    <button type="button" @@click="addLine()" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-orange-600 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Tambah Baris
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50/50">
                                <th class="text-left px-3 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Akun</th>
                                <th class="text-right px-3 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">Debit</th>
                                <th class="text-right px-3 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider w-40">Credit</th>
                                <th class="text-left px-3 py-2.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                <th class="px-3 py-2.5 w-10"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(line, i) in lines" :key="i">
                                <tr class="border-b border-gray-50">
                                    <td class="px-3 py-2">
                                        <select x-model="line.coa_id" :name="'lines[' + i + '][coa_id]'" required class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                                            <option value="">Pilih Akun</option>
                                            @foreach($accounts as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->code }} - {{ $acc->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" min="0" x-model="line.debit" :name="'lines[' + i + '][debit]'" placeholder="0" @@input="line.credit = ''" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-right focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="number" step="0.01" min="0" x-model="line.credit" :name="'lines[' + i + '][credit]'" placeholder="0" @@input="line.debit = ''" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm text-right focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                                    </td>
                                    <td class="px-3 py-2">
                                        <input type="text" x-model="line.description" :name="'lines[' + i + '][description]'" placeholder="Deskripsi baris" maxlength="500" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-300 transition-all">
                                    </td>
                                    <td class="px-3 py-2 text-center">
                                        <button type="button" @@click="removeLine(i)" x-show="lines.length > 1" class="text-red-400 hover:text-red-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr class="bg-orange-50/50 font-semibold">
                                <td class="px-3 py-2.5 text-xs text-gray-600 uppercase tracking-wider">Total</td>
                                <td class="px-3 py-2.5 text-right text-sm font-bold text-gray-800">
                                    Rp <span x-text="totalDebit().toLocaleString('id-ID')">0</span>
                                </td>
                                <td class="px-3 py-2.5 text-right text-sm font-bold text-gray-800">
                                    Rp <span x-text="totalCredit().toLocaleString('id-ID')">0</span>
                                </td>
                                <td colspan="2" class="px-3 py-2.5">
                                    <template x-if="!isBalanced() && (totalDebit() > 0 || totalCredit() > 0)">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-red-50 text-red-600 rounded-full">Belum balance</span>
                                    </template>
                                    <template x-if="isBalanced() && totalDebit() > 0">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-600 rounded-full">Balance ✓</span>
                                    </template>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="flex gap-3 mt-6 pt-5 border-t border-gray-100">
                <button type="submit" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Simpan Jurnal
                </button>
                <a href="{{ route('journals.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
