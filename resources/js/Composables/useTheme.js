import { ref } from 'vue';

const isDark = ref(false);

export function useTheme() {
    const initTheme = () => {
        // Cek localStorage atau preferensi sistem
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            setDark(true);
        } else {
            setDark(false);
        }
    };

    const setDark = (value) => {
        isDark.value = value;
        if (value) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    };

    const toggleTheme = () => {
        setDark(!isDark.value);
    };

    return {
        isDark,
        initTheme,
        toggleTheme
    };
}