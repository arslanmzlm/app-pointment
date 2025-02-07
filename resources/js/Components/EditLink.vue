<script lang="ts" setup>
import {Button} from 'primevue';

defineProps<{
    url: string;
}>();
</script>

<template>
    <Button :href="url" as="Link" class="size-12" icon="pi pi-pen-to-square" title="Düzenle" />
</template>
