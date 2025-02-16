<script lang="ts" setup>
import {Tag} from 'primevue';

defineProps<{
    value: number | null;
}>();
</script>

<template>
    <Tag
        :severity="value ? 'warn' : 'danger'"
        :value="value ? 'Kullanıcı Kaydı' : 'Sistem Kaydı'"
        icon="pi pi-user-plus"
        title="Kayıt tipi"
    />
</template>
