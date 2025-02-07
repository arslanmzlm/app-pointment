<script lang="ts" setup>
defineProps<{
    for?: string;
    label?: string;
    error?: string | null;
    required?: boolean;
}>();
</script>

<template>
    <div class="flex flex-col gap-3">
        <label v-if="label" :for class="block text-sm font-semibold"
            >{{ label }} <span v-if="required" class="text-danger">*</span></label
        >

        <slot></slot>

        <div v-if="error" class="text-sm text-danger">{{ error }}</div>

        <slot name="message"></slot>
    </div>
</template>
