import {useStorage} from '@vueuse/core';
import {defineStore} from 'pinia';
import {ref} from 'vue';

export const useDarkModeStore = defineStore('darkMode', () => {
    const isDark = useStorage('darkMode', ref(false));

    document.documentElement.classList.toggle('dark', isDark.value);

    function toggleDarkMode() {
        isDark.value = !isDark.value;
        document.documentElement.classList.toggle('dark', isDark.value);
    }

    return {isDark, toggleDarkMode};
});
