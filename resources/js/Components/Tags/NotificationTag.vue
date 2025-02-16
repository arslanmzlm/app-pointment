<script lang="ts" setup>
import {Tag} from 'primevue';

withDefaults(
    defineProps<{
        value: boolean;
    }>(),
    {
        value: false,
    },
);
</script>

<template>
    <Tag
        v-if="value !== undefined"
        :icon="value ? 'pi pi-bell' : 'pi pi-bell-slash'"
        :severity="value ? 'info' : 'warn'"
        :value="value ? 'Bildirimler Aktif' : 'Bildirimler Pasif'"
        title="Bildirim durumu"
    />
</template>
