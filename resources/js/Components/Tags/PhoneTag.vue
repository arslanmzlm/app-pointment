<script lang="ts" setup>
import {Tag} from 'primevue';

defineProps<{
    value?: string | null;
}>();
</script>

<template>
    <Tag :value icon="pi pi-phone" severity="secondary" title="Telefon numarası" />
</template>
