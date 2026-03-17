import { defineStore } from 'pinia';
import { useStorage } from '@vueuse/core';

export const useGlobalStore = defineStore('global', () => {
    // useStorage automatically syncs with localStorage
    const isVisible = useStorage('fin_family_is_visible', true);

    const toggleVisibility = () => {
        isVisible.value = !isVisible.value;
    };

    return {
        isVisible,
        toggleVisibility
    };
});
