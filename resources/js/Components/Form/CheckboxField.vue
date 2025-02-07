<script lang="ts" setup>
import {camelCase, isBoolean} from 'lodash';
import {Checkbox} from 'primevue';
import {computed} from 'vue';

const props = defineProps<{
    name: string;
    value: any;
    label?: string;
    error?: string | null;
}>();

const model = defineModel<any>({required: true});

const id = computed(() => {
    return camelCase(`checkbox ${props.name} ${props.value}`);
});

const binary = computed(() => {
    return isBoolean(props.value);
});
</script>

<template>
    <div class="flex items-center gap-2">
        <Checkbox v-model="model" :binary :input-id="id" :invalid="!!error" :name :value />
        <label v-if="label" :for="id" class="cursor-pointer">{{ label }}</label>
    </div>
</template>
