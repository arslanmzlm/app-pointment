<script lang="ts" setup>
import {camelCase} from 'lodash';
import {RadioButton} from 'primevue';
import {computed} from 'vue';

const props = defineProps<{
    name: string;
    value: any;
    label?: string;
    error?: string | null;
}>();

const model = defineModel<any>({required: true});

const id = computed(() => {
    return camelCase(`radio ${props.name} ${props.value}`);
});
</script>

<template>
    <div class="flex items-center gap-2">
        <RadioButton v-model="model" :input-id="id" :invalid="!!error" :name :value />
        <label v-if="label" :for="id" class="cursor-pointer">{{ label }}</label>
    </div>
</template>
