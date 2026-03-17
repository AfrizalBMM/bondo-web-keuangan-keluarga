import { storeToRefs } from 'pinia';
import { useGlobalStore } from '@/Stores/useGlobalStore';

export function useVisibility() {
    const store = useGlobalStore();
    const { isVisible } = storeToRefs(store);

    const toggleVisibility = () => {
        store.toggleVisibility();
    };

    // Helper to mask strings (like balance)
    const maskValue = (value, fallback = 'Rp ****') => {
        return isVisible.value ? value : fallback;
    };

    return { isVisible, toggleVisibility, maskValue };
}
