import { reactive } from 'vue';

const state = reactive({
    isOpen: false,
    title: 'Konfirmasi Penghapusan',
    message: 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
    confirmText: 'Ya, Hapus',
    cancelText: 'Batal',
    onConfirm: null
});

export function useConfirm() {
    const confirm = (options) => {
        state.title = options.title || 'Konfirmasi';
        state.message = options.message || 'Apakah Anda yakin?';
        state.confirmText = options.confirmText || 'Ya, Lanjutkan';
        state.cancelText = options.cancelText || 'Batal';
        state.onConfirm = options.onConfirm;
        state.isOpen = true;
    };

    const close = () => {
        state.isOpen = false;
    };

    return {
        state,
        confirm,
        close
    };
}
