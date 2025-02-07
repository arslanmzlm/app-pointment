<script lang="ts" setup>
import {isArray} from 'lodash';
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
