<script lang="ts" setup>
import {Link} from '@inertiajs/vue3';
import {Breadcrumb} from 'primevue';
import {MenuItem} from 'primevue/menuitem';
import {computed} from 'vue';

const props = defineProps<{
    title: string;
    homeUrl: string;
    items?: MenuItem[];
}>();

const home = {
    icon: 'pi pi-home',
    url: props.homeUrl,
};

const breadcrumbs = computed(() => {
    if (props.items) {
        return [...props.items, {label: props.title}];
    }

    return [{label: props.title}];
});
</script>

<template>
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="!text-3xl font-bold text-black dark:text-white">
            {{ title }}
        </h1>

        <Breadcrumb :home :model="breadcrumbs">
            <template #item="{item}">
                <Link v-if="item.url" :href="item.url" class="font-medium hover:text-primary">
                    <span v-if="item.icon" :class="item.icon"></span> {{ item.label }}
                </Link>
                <div v-else class="font-medium text-primary">
                    <span v-if="item.icon" :class="item.icon"></span> <span>{{ item.label }}</span>
                </div>
            </template>
        </Breadcrumb>
    </div>
</template>

<style>
.p-breadcrumb {
    @apply bg-transparent p-0;
}
</style>
