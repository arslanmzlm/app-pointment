<script lang="ts" setup>
import {camelCase} from 'lodash';
import Editor from 'primevue/editor';
import {computed} from 'vue';
import FormField from '@/Components/Form/FormField.vue';

const props = defineProps<{
    name?: string;
    label?: string;
    error?: string | null;
    placeholder?: string;
    required?: boolean;
}>();

const model = defineModel<string>({required: true});

const id = computed(() => {
    return props.name ? camelCase('textarea ' + props.name) : undefined;
});
</script>

<template>
    <FormField :error :for="id" :label :required>
        <Editor v-model="model" :invalid="!!error" :name />

        <template v-if="$slots.message" #message>
            <slot name="message"></slot>
        </template>
    </FormField>
</template>
