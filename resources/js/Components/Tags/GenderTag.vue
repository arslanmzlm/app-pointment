<script lang="ts" setup>
import {Tag} from 'primevue';
import {Gender} from '@/types/enums';

defineProps<{
    value: Gender;
    label: string;
}>();
</script>

<template>
    <Tag
        v-if="value"
        :icon="value == 1 ? 'pi pi-mars' : 'pi pi-venus'"
        :severity="value == 1 ? 'info' : 'danger'"
        :value="label"
        title="Cinsiyet"
    />
</template>
