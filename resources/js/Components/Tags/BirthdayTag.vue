<script lang="ts" setup>
import {Tag} from 'primevue';
import {computed} from 'vue';
import {dateFormat} from '@/Utilities/formatters';

const props = defineProps<{
    value?: Date | string | null;
}>();

const label = computed(() => {
    if (props.value) {
        return dateFormat(props.value);
    }

    return props.value;
});
</script>

<template>
    <Tag v-if="label" :value="label" icon="pi pi-gift" severity="primary" title="Doğum tarihi" />
</template>
