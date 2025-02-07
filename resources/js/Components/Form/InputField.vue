<script lang="ts" setup>
import {InputTypeHTMLAttribute} from '@vue/runtime-dom';
import {camelCase} from 'lodash';
import {InputText} from 'primevue';
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
        size?: 'small' | 'large';
        readonly?: boolean;
    }>(),
    {
        type: 'text',
    },
);

const model = defineModel<string | null>({required: true});

const id = computed(() => {
    return props.name ? camelCase(`input ${props.name}`) : undefined;
});
</script>

<template>
    <FormField :error :for="id" :label :required>
        <InputText
            :id
            v-model="model"
            :invalid="!!error"
            :placeholder
            :readonly
            :size
            :type
            fluid
        />

        <template #message>
            <slot name="message"></slot>
        </template>
    </FormField>
</template>
