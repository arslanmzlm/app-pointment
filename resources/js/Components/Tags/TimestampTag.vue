<script lang="ts" setup>
import {Tag} from 'primevue';
import {computed} from 'vue';
import {dateTimeFormat} from '@/Utilities/formatters';

const props = withDefaults(
    defineProps<{
        value?: Date | string | null;
        type?: 'created_at' | 'updated_at';
    }>(),
    {
        type: 'created_at',
    },
);

const label = computed(() => {
    if (props.value) {
        return dateTimeFormat(props.value);
    }

    return props.value;
});
</script>

<template>
    <Tag
        v-if="value"
        :severity="type === 'created_at' ? 'danger' : 'info'"
        :title="type === 'created_at' ? 'Kayıt tarihi' : 'Son düzenlenme tarihi'"
        :value="label"
        icon="pi pi-history"
    />
</template>
