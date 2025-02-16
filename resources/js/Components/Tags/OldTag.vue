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
        :icon="value ? 'pi pi-user' : 'pi pi-user-plus'"
        :severity="value ? 'secondary' : 'primary'"
        :value="value ? 'Eski Kayıt' : 'Yeni Kayıt'"
        title="Kayıt durumu"
    />
</template>
