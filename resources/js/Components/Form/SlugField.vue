<script lang="ts" setup>
import {camelCase, kebabCase} from 'lodash';
import {InputText} from 'primevue';
import {computed, watch} from 'vue';
import FormField from '@/Components/Form/FormField.vue';

const props = defineProps<{
    name?: string;
    label?: string;
    error?: string | null;
    placeholder?: string;
    required?: boolean;
    size?: 'small' | 'large';
    readonly?: boolean;
}>();

const model = defineModel<string | null>({required: true});

const id = computed(() => {
    return props.name ? camelCase(`input ${props.name}`) : undefined;
});

watch(
    () => model.value,
    (value) => {
        if (value) {
            model.value = kebabCase(value);
        }
    },
);
</script>

<template>
    <FormField :error :for="id" :label :required>
        <InputText
            :id
            v-model.trim="model"
            :invalid="!!error"
            :placeholder
            :readonly
            :size
            fluid
            type="text"
        />

        <template v-if="$slots.message" #message>
            <slot name="message"></slot>
        </template>
    </FormField>
</template>
