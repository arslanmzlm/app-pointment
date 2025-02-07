<script lang="ts" setup>
import {clone} from 'lodash';
import {computed, watch} from 'vue';
import DateField from '@/Components/Form/DateField.vue';

defineProps<{
    size?: 'small' | 'large';
    showTime?: boolean;
}>();

const startDate = defineModel<Date | null>('startDate', {required: true});
const dueDate = defineModel<Date | null>('dueDate', {required: true});

const minDateForEnd = computed(() => {
    if (startDate.value) {
        const date = clone(startDate.value);
        date.setHours(23, 59, 59);

        return date;
    }

    return undefined;
});

const maxDateForStart = computed(() => {
    if (dueDate.value) {
        const date = clone(dueDate.value);
        date.setHours(0, 0, 0);

        return date;
    }

    return undefined;
});

watch(
    () => startDate.value,
    () => {
        if (startDate.value && dueDate.value && startDate.value > dueDate.value) {
            dueDate.value = null;
        }
    },
);

watch(
    () => dueDate.value,
    () => {
        if (startDate.value && dueDate.value && startDate.value > dueDate.value) {
            startDate.value = null;
        }
    },
);
</script>

<template>
    <div class="grid grid-cols-2 gap-4">
        <DateField
            v-model="startDate"
            :max-date="maxDateForStart"
            :show-time
            :size
            class="col-span-1"
            date-format="d MM yy"
            label="Başlangıç tarihi"
            required
        />

        <DateField
            v-model="dueDate"
            :min-date="minDateForEnd"
            :show-time
            :size
            class="col-span-1"
            date-format="d MM yy"
            label="Bitiş tarihi"
            required
        />
    </div>
</template>
