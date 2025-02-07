<script lang="ts" setup>
import {Link} from '@inertiajs/vue3';
import {onClickOutside} from '@vueuse/core';
import {ref} from 'vue';
import {useSidebarStore} from '@/Stores/sidebar';
import SidebarItem from '@/Layouts/Parts/SidebarItem.vue';
import ArrowLeftIcon from '@/Icons/ArrowLeftIcon.vue';
import {SidebarMenuRow} from '@/types/component';

defineProps<{
    menu: SidebarMenuRow[];
}>();

const sidebarStore = useSidebarStore();
const target = ref(null);
onClickOutside(target, () => {
    sidebarStore.open = false;
});
</script>

<template>
    <aside
        ref="target"
        :class="{
            'translate-x-0': sidebarStore.open,
            '-translate-x-full': !sidebarStore.open,
        }"
        class="absolute left-0 top-0 z-50 flex h-screen w-80 flex-col overflow-y-hidden bg-slate-800 duration-300 ease-linear lg:static lg:translate-x-0"
    >
        <!-- SIDEBAR HEADER -->
        <div class="flex items-center justify-between gap-2 px-6 py-5 lg:py-6">
            <Link :href="route('login')">
                <img alt="Logo" src="../../../images/logo/dark.png" />
            </Link>

            <button class="block text-white lg:hidden" @click="sidebarStore.open = false">
                <ArrowLeftIcon class="size-5" />
            </button>
        </div>
        <!-- SIDEBAR HEADER -->

        <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
            <!-- Sidebar Menu -->
            <nav class="mt-5 px-4 py-4 lg:mt-9 lg:px-6">
                <template v-for="row in menu" :key="row.name">
                    <div>
                        <div class="mb-4 ml-4 text-sm font-medium text-surface-400">
                            {{ row.name }}
                        </div>

                        <ul class="mb-6 flex flex-col gap-1.5">
                            <li v-for="(item, index) in row.items" :key="index">
                                <SidebarItem :item />
                            </li>
                        </ul>
                    </div>
                </template>
            </nav>
            <!-- Sidebar Menu -->
        </div>
    </aside>
</template>
