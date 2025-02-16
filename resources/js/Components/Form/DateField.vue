<script lang="ts" setup>
import {InputTypeHTMLAttribute} from '@vue/runtime-dom';
import {camelCase} from 'lodash';
import {DatePicker} from 'primevue';
import {computed} from 'vue';
import FormField from '@/Components/Form/FormField.vue';

const props = withDefaults(
    defineProps<{
        name?: string;
        label?: string;
        error?: string | null;
        type?: InputTypeHTMLAttribute;
        placeholder?: string;
        required?: boolean;
        fluid?: boolean;
        dateFormat?: string;
        size?: 'small' | 'large';
        selectionMode?: 'single' | 'multiple' | 'range';
        showTime?: boolean;
        minDate?: Date;
        maxDate?: Date;
        disabledDates?: Date[];
    }>(),
    {
        type: 'text',
        fluid: false,
    },
);

const model = defineModel<Date | Array<Date> | Array<Date | null> | undefined | null>({
    required: true,
});

const id = computed(() => {
    return props.name ? camelCase(`inputDate ${props.name}`) : undefined;
});
</script>

<template>
    <FormField :error :for="id" :label :required>
        <DatePicker
            :id
            v-model="model"
            :date-format
            :disabled-dates
            :fluid
            :invalid="!!error"
            :max-date
            :min-date
            :placeholder
            :selection-mode
            :show-time
            :size
            :type
        />

        <template v-if="$slots.message" #message>
            <slot name="message"></slot>
        </template>
    </FormField>
</template>

<style>
.p-datepicker-panel {
    @apply !w-full !min-w-fit !max-w-lg;
}
</style>
