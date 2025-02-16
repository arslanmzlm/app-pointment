<script lang="ts" setup>
import {useSidebarStore} from '@/Stores/sidebar';
import DarkModeSwitcher from '@/Layouts/Parts/DarkModeSwitcher.vue';
import DropdownUser from '@/Layouts/Parts/DropdownUser.vue';
import HeaderActiveAppointment from '@/Layouts/Parts/HeaderActiveAppointment.vue';
import HeaderSearch from '@/Layouts/Parts/HeaderSearch.vue';

const {toggleSidebar} = useSidebarStore();
const sidebarStore = useSidebarStore();
</script>

<template>
    <header
        class="sticky top-0 z-40 flex w-full border-b border-surface-200 bg-white shadow-sm dark:border-transparent dark:bg-surface-800"
    >
        <div class="flex flex-grow items-center justify-between px-4 py-4 md:px-6 2xl:px-11">
            <div class="flex items-center gap-2 sm:gap-4 lg:hidden">
                <!-- Hamburger Toggle BTN -->
                <button
                    class="z-50 block size-[36px] rounded-sm border border-surface-200 bg-white p-[6px] shadow-sm dark:border-surface-700 dark:bg-surface-800 lg:hidden"
                    @click="toggleSidebar()"
                >
                    <span class="relative block size-6 cursor-pointer">
                        <span class="absolute right-0 block h-full w-full">
                            <span
                                :class="{'!w-full delay-300': !sidebarStore.open}"
                                class="relative left-0 top-0 my-1 block h-[2px] w-0 rounded-sm bg-black delay-[0] duration-200 ease-in-out dark:bg-white"
                            ></span>
                            <span
                                :class="{'delay-400 !w-full': !sidebarStore.open}"
                                class="relative left-0 top-0 my-1 block h-[2px] w-0 rounded-sm bg-black delay-150 duration-200 ease-in-out dark:bg-white"
                            ></span>
                            <span
                                :class="{'!w-full delay-500': !sidebarStore.open}"
                                class="relative left-0 top-0 my-1 block h-[2px] w-0 rounded-sm bg-black delay-200 duration-200 ease-in-out dark:bg-white"
                            ></span>
                        </span>
                        <span class="absolute right-0 block h-full w-full rotate-45">
                            <span
                                :class="{'!h-0 delay-[0]': !sidebarStore.open}"
                                class="absolute left-2.5 top-0 block h-full w-1 rounded-sm bg-black delay-300 duration-200 ease-in-out dark:bg-white"
                            ></span>
                            <span
                                :class="{'!h-0 delay-200': !sidebarStore.open}"
                                class="delay-400 absolute left-0 top-3 block h-[2px] w-full rounded-sm bg-black duration-200 ease-in-out dark:bg-white"
                            ></span>
                        </span>
                    </span>
                </button>
                <!-- Hamburger Toggle BTN -->
            </div>
            <HeaderSearch />

            <div class="flex items-center gap-3 sm:gap-7">
                <HeaderActiveAppointment />

                <!-- Dark Mode Toggler -->
                <DarkModeSwitcher />
                <!-- Dark Mode Toggler -->

                <!-- User Area -->
                <DropdownUser />
                <!-- User Area -->
            </div>
        </div>
    </header>
</template>
