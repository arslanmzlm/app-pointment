<script lang="ts" setup>
import {Tag} from 'primevue';

defineProps<{
    value?: string | null;
}>();
</script>

<template>
    <Tag :value icon="pi pi-envelope" severity="warn" title="E-posta adresi" />
</template>
