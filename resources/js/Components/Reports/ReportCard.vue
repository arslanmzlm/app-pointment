<script lang="ts" setup>
import {Tag} from 'primevue';

defineProps<{
    title?: string;
    value?: string | number;
    icon?: string;
    severity?: string;
    typeIcon?: string;
    typeSeverity?: string;
    typeLabel?: string;
}>();
</script>

<template>
    <div class="col-span-12 md:col-span-6 lg:col-span-4">
        <div class="rounded border p-4 shadow-sm dark:border-surface-700">
            <div class="mb-4 flex justify-between">
                <div>
                    <span class="mb-4 block font-medium text-surface-500 dark:text-surface-300">{{
                        title
                    }}</span>
                    <div class="text-xl font-medium text-surface-900 dark:text-surface-0">
                        {{ value }}
                    </div>
                </div>
                <Tag
                    :icon="icon"
                    :severity="severity"
                    class="method-icon flex size-12 items-center justify-center rounded !p-0"
                >
                    <template v-if="$slots.icon" #icon>
                        <slot name="icon"></slot>
                    </template>
                </Tag>
            </div>
            <Tag :icon="typeIcon" :severity="typeSeverity" :value="typeLabel" />
        </div>
    </div>
</template>

<style>
.method-icon .p-tag-icon,
.method-icon svg,
.method-icon img {
    @apply size-5;
    font-size: 20px !important;
    line-height: 1 !important;
}
</style>
