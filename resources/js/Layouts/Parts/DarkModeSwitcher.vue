<script lang="ts" setup>
import {useDarkModeStore} from '@/Stores/darkMode';
import MoonIcon from '@/Icons/MoonIcon.vue';
import SunIcon from '@/Icons/SunIcon.vue';

const darkMode = useDarkModeStore();
</script>

<template>
    <button
        class="rounded-full bg-surface-100 p-2 hover:bg-surface-200 dark:bg-surface-700 dark:hover:bg-surface-600"
        type="button"
        @click="darkMode.toggleDarkMode"
    >
        <MoonIcon v-if="darkMode.isDark" class="size-6" />
        <SunIcon v-else class="size-6" />
    </button>
</template>
