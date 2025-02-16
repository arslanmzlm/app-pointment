<script lang="ts" setup>
import {Tag} from 'primevue';

defineProps<{
    value?: string | null;
}>();
</script>

<template>
    <Tag :value icon="pi pi-map-marker" severity="primary" title="Şehir" />
</template>
