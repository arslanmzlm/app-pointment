import {defineStore} from 'pinia';
import {ref} from 'vue';

export const useSidebarStore = defineStore('sidebar', () => {
    const open = ref(false);

    function toggleSidebar() {
        open.value = !open.value;
    }

    return {open, toggleSidebar};
});
