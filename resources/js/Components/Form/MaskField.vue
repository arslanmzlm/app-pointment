<script lang="ts" setup>
import {camelCase} from 'lodash';
import {InputMask} from 'primevue';
import {computed, onBeforeMount} from 'vue';
import FormField from '@/Components/Form/FormField.vue';

const props = defineProps<{
    name?: string;
    label?: string;
    error?: string | null;
    placeholder?: string;
    required?: boolean;
    mask?: string | 'phone';
}>();

const model = defineModel<string | undefined>({required: true});

const id = computed(() => {
    return props.name ? camelCase(`inputMask ${props.name}`) : undefined;
});

const getMask = computed(() => {
    switch (props.mask) {
        case 'phone':
        default:
            return '0 999 999 99 99';
    }
});

onBeforeMount(() => {
    if (props.mask === 'phone') {
        model.value = model.value?.replace('+90', '');
    }
});
</script>

<template>
    <FormField :error :for="id" :label :required>
        <InputMask :id v-model="model" :invalid="!!error" :mask="getMask" :placeholder fluid />

        <template #message>
            <slot name="message"></slot>
        </template>
    </FormField>
</template>
