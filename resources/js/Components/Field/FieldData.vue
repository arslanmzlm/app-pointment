<script lang="ts" setup>
import {has, isArray, isObject} from 'lodash';
import {computed} from 'vue';
import {dateFormat} from '@/Utilities/formatters';
import {FieldInput} from '@/types/enums';
import {PatientFieldResponse} from '@/types/response';

const props = defineProps<{
    field: PatientFieldResponse;
}>();

const value = computed<any>(() => {
    if (isArray(props.field.value)) {
        return props.field.value.join(', ');
    } else if (props.field.input === FieldInput.DATE) {
        return dateFormat(props.field.value);
    } else if (props.field.input === FieldInput.RADIO_TEXT) {
        if (isObject(props.field.value)) {
            let value = '';

            if (has(props.field.value, 'selection') && props.field.value.selection) {
                value += props.field.value.selection;
            }
            if (has(props.field.value, 'description') && props.field.value.description) {
                value += (value ? ', ' : '') + props.field.value.description;
            }
            return value;
        }

        return null;
    }

    return props.field.value;
});
</script>

<template>
    <div class="flex gap-1">
        <span class="font-semibold">{{ field.name }}:</span>
        <span>{{ value }}</span>
    </div>
</template>
