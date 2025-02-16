<script lang="ts" setup>
import {Listbox} from 'primevue';
import {computed} from 'vue';
import FormField from '@/Components/Form/FormField.vue';

const props = withDefaults(
    defineProps<{
        options: any[];
        optionLabel?: string | ((data: any) => string) | null;
        optionValue?: string | ((data: any) => string) | null;
        label?: string;
        error?: string | null;
        required?: boolean;
    }>(),
    {
        optionLabel: 'label',
        optionValue: 'value',
    },
);

const model = defineModel<any>({required: true});

const computedOptionLabel = computed(() => {
    return props.optionLabel === null ? undefined : props.optionLabel;
});

const computedOptionValue = computed(() => {
    return props.optionValue === null ? undefined : props.optionValue;
});
</script>

<template>
    <FormField :error :label :required>
        <Listbox
            v-model="model"
            :invalid="!!error"
            :option-label="computedOptionLabel"
            :option-value="computedOptionValue"
            :options="options"
        >
            <template #option="slotProps">
                <slot :data="slotProps" name="option"></slot>
            </template>
        </Listbox>

        <template v-if="$slots.message" #message>
            <slot name="message"></slot>
        </template>
    </FormField>
</template>
