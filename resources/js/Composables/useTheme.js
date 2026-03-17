import { ref, watchEffect } from 'vue';

const isDark = ref(false);
let initialized = false;

export function useTheme() {
    const initTheme = () => {
        if (initialized) return;
        
        if (typeof window !== 'undefined') {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                isDark.value = true;
            } else {
                isDark.value = false;
            }

            watchEffect(() => {
                if (isDark.value) {
                    document.documentElement.classList.add('dark');
                    localStorage.theme = 'dark';
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.theme = 'light';
                }
            });
        }
        initialized = true;
    };

    const toggleTheme = () => {
        isDark.value = !isDark.value;
    };

    return { isDark, initTheme, toggleTheme };
}
