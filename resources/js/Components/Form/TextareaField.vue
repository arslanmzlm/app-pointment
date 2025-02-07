<script lang="ts" setup>
import {camelCase} from 'lodash';
import {Textarea} from 'primevue';
import {computed} from 'vue';
import FormField from '@/Components/Form/FormField.vue';

const props = defineProps<{
    name?: string;
    label?: string;
    error?: string | null;
    placeholder?: string;
    required?: boolean;
}>();

const model = defineModel<string | null>({required: true});

const id = computed(() => {
    return props.name ? camelCase('textarea ' + props.name) : undefined;
});
</script>

<template>
    <FormField :error :for="id" :label :required>
        <Textarea :id v-model="model" :invalid="!!error" :placeholder auto-resize fluid />

        <template #message>
            <slot name="message"></slot>
        </template>
    </FormField>
</template>
