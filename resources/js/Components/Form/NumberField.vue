<script lang="ts" setup>
import {InputTypeHTMLAttribute} from '@vue/runtime-dom';
import {camelCase} from 'lodash';
import {InputNumber} from 'primevue';
import {computed} from 'vue';
import FormField from '@/Components/Form/FormField.vue';

const props = defineProps<{
    name?: string;
    label?: string;
    error?: string | null;
    type?: InputTypeHTMLAttribute;
    placeholder?: string;
    required?: boolean;
    step?: number;
    mode?: 'currency' | 'decimal';
    showButtons?: boolean;
    buttonLayout?: 'stacked' | 'horizontal' | 'vertical';
    readonly?: boolean;
    min?: number;
    max?: number;
}>();

const model = defineModel<number | null>({required: true});

const id = computed(() => {
    return props.name ? camelCase(`numberInput ${props.name}`) : undefined;
});
</script>

<template>
    <FormField :error :for="id" :label :required>
        <InputNumber
            v-model="model"
            :button-layout
            :currency="mode === 'currency' ? 'TRY' : undefined"
            :input-id="id"
            :invalid="!!error"
            :locale="mode === 'currency' ? 'tr' : undefined"
            :max
            :min
            :mode
            :name
            :placeholder
            :readonly
            :show-buttons
            :step
            fluid
        >
            <template v-if="buttonLayout === 'horizontal'" #incrementbuttonicon>
                <span class="pi pi-plus" />
            </template>
            <template v-if="buttonLayout === 'horizontal'" #decrementbuttonicon>
                <span class="pi pi-minus" />
            </template>
        </InputNumber>

        <template #message>
            <slot name="message"></slot>
        </template>
    </FormField>
</template>
