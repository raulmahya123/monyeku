@props([
    'name',
    'title' => 'Konfirmasi',
    'message' => 'Apakah Anda yakin?',
    'confirmText' => 'Ya, lanjutkan',
    'confirmClass' => 'btn-danger',
    'cancelText' => 'Batal',
])

<x-modal :name="$name" :max-width="$maxWidth ?? 'md'">
    <div class="p-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800">{{ $title }}</h3>
                <p class="text-sm text-gray-500 mt-0.5">{{ $message }}</p>
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <button x-on:click="show = false" type="button" class="btn-ghost btn-sm">{{ $cancelText }}</button>
            <button
                x-on:click="$dispatch('confirm-{{ $name }}'); show = false"
                type="button"
                class="{{ $confirmClass }} btn-sm"
            >{{ $confirmText }}</button>
        </div>
    </div>
</x-modal>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('confirmAction', (modalName) => ({
            init() {
                this.$watch('show', (val) => {
                    if (val) this.$dispatch('open-modal', modalName);
                });
            }
        }));
    });
</script>
@endpush
