<script lang="ts" setup>
import {Select} from 'primevue';
import {SelectFilterEvent} from 'primevue/select';
import {computed} from 'vue';
import FormField from '@/Components/Form/FormField.vue';

const props = withDefaults(
    defineProps<{
        options: any[];
        optionLabel?: string | ((data: any) => string) | null;
        optionValue?: string | ((data: any) => string) | null;
        name?: string;
        label?: string;
        error?: string | null;
        placeholder?: string;
        required?: boolean;
        filter?: boolean;
        showClear?: boolean;
        editable?: boolean;
        size?: 'small' | 'large';
        autoFilterFocus?: boolean;
    }>(),
    {
        optionLabel: 'name',
        optionValue: 'id',
    },
);

const model = defineModel<any>({required: true});
const emit = defineEmits<{
    (e: 'filter', event: SelectFilterEvent): void;
}>();

const computedOptionLabel = computed(() => {
    return props.optionLabel === null ? undefined : props.optionLabel;
});

const computedOptionValue = computed(() => {
    return props.optionValue === null ? undefined : props.optionValue;
});
</script>

<template>
    <FormField :error :label :required>
        <Select
            v-model="model"
            :auto-filter-focus
            :editable
            :filter
            :invalid="!!error"
            :name
            :option-label="computedOptionLabel"
            :option-value="computedOptionValue"
            :options
            :placeholder
            :show-clear
            :size
            fluid
            @filter="(event) => emit('filter', event)"
        >
            <template #option="slotProps">
                <slot :data="slotProps" name="option"></slot>
            </template>
        </Select>

        <template v-if="$slots.message" #message>
            <slot name="message"></slot>
        </template>
    </FormField>
</template>
