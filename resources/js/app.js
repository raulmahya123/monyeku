import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    Alpine.store('confirm', {
        show: false,
        title: 'Konfirmasi',
        message: 'Apakah Anda yakin?',
        confirmText: 'Ya, lanjutkan',
        confirmClass: 'btn-danger',
        confirmAction: null,

        ask(title, message, options = {}) {
            this.title = title;
            this.message = message;
            this.confirmText = options.confirmText || 'Ya, lanjutkan';
            this.confirmClass = options.confirmClass || 'btn-danger';
            this.confirmAction = options.action || null;
            this.show = true;
        },

        confirm() {
            this.show = false;
            if (typeof this.confirmAction === 'function') {
                this.confirmAction();
            }
            this.confirmAction = null;
        },

        cancel() {
            this.show = false;
            this.confirmAction = null;
        }
    });

    Alpine.store('toast', {
        items: [],

        add(type, message) {
            const id = Date.now() + Math.random();
            this.items.push({ id, type, message });
            setTimeout(() => {
                this.items = this.items.filter(i => i.id !== id);
            }, 4000);
        },

        success(message) { this.add('success', message); },
        error(message) { this.add('error', message); },
        warning(message) { this.add('warning', message); },

        remove(id) {
            this.items = this.items.filter(i => i.id !== id);
        }
    });
});

Alpine.start();
