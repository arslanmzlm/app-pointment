<script lang="ts" setup>
import {router, usePage} from '@inertiajs/vue3';
import {PrimeIcons} from '@primevue/core/api';
import {Menu} from 'primevue';
import {MenuItem} from 'primevue/menuitem';
import {computed, ref} from 'vue';
import UserIcon from '@/Icons/UserIcon.vue';

const page = usePage();
const user = computed(() => page.props.auth.user ?? {name: 'Test User'});

const menu = ref();
const items = ref<MenuItem[]>([
    {
        label: 'Çıkış Yap',
        icon: PrimeIcons.SIGN_OUT,
        command: () => {
            router.post(route('logout'));
        },
    },
]);

const toggle = (event: any) => {
    menu.value.toggle(event);
};
</script>

<template>
    <button
        aria-controls="userMenu"
        aria-haspopup="true"
        class="group flex items-center justify-center gap-2"
        type="button"
        @click="toggle"
    >
        <div
            v-if="user"
            class="hidden text-sm font-medium text-surface-700 group-hover:text-surface-900 dark:text-surface-200 dark:group-hover:text-surface-0 lg:block"
        >
            {{ user.name }}
        </div>

        <UserIcon
            :class="{active: menu?.overlayVisible ?? false}"
            class="size-10 rounded-full bg-surface-100 p-2 group-hover:bg-surface-200 dark:bg-surface-700 dark:group-hover:bg-surface-600"
        />
    </button>

    <Menu id="userMenu" ref="menu" :model="items" :popup="true" />
</template>

<style scoped>
.active {
    @apply bg-surface-200 dark:bg-surface-600;
}
</style>
