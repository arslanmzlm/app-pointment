<script lang="ts" setup>
import {Accordion, AccordionContent, AccordionHeader, AccordionPanel} from 'primevue';
import CheckboxField from '@/Components/Form/CheckboxField.vue';
import DateField from '@/Components/Form/DateField.vue';
import InputField from '@/Components/Form/InputField.vue';
import NumberField from '@/Components/Form/NumberField.vue';
import RadioField from '@/Components/Form/RadioField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {FieldInput} from '@/types/enums';
import {Field} from '@/types/model';

const props = defineProps<{
    field: Field;
    error?: string | null;
}>();

const model = defineModel<any>();

if (props.field.input === FieldInput.CHECKBOX && !Array.isArray(model.value)) {
    model.value = [];
}

if (props.field.input === FieldInput.DATE && model.value) {
    model.value = new Date(model.value);
}

function generateName(attribute_id: number | string, value_id: number | string = 0) {
    return `attribute${attribute_id}Value${value_id}`;
}
</script>

<template>
    <div v-if="field.input === FieldInput.CHECKBOX" class="col-span-full">
        <Accordion>
            <AccordionPanel value="0">
                <AccordionHeader>{{ field.name }}</AccordionHeader>
                <AccordionContent>
                    <div class="grid grid-cols-1 gap-4 pt-5 md:grid-cols-2 xl:grid-cols-3">
                        <CheckboxField
                            v-for="value in field.values"
                            v-model="model"
                            :label="value.value"
                            :name="generateName(field.id, value.id)"
                            :value="value.id"
                            class="col-span-1"
                        />
                    </div>
                </AccordionContent>
            </AccordionPanel>
        </Accordion>
    </div>
    <div v-else class="border-stroke dark:border-form-strokedark col-span-1 space-y-3">
        <div class="mb-3 block text-sm font-medium dark:text-white">
            {{ field.name }}
        </div>
        <div v-if="field.input === FieldInput.TEXT">
            <InputField v-model="model" :error />
        </div>
        <div v-else-if="field.input === FieldInput.NUMBER">
            <NumberField v-model.number="model" :error />
        </div>
        <div v-else-if="field.input === FieldInput.DATE">
            <DateField v-model="model" :error date-format="d MM yy" />
        </div>
        <div
            v-else-if="field.input === FieldInput.SELECT && field.values && field.values.length > 0"
        >
            <SelectField
                v-model="model"
                :error
                :options="field.values"
                option-label="value"
                placeholder="Belirtilmemiş"
                show-clear
            />
        </div>
        <div
            v-else-if="field.input === FieldInput.RADIO"
            class="border-stroke dark:border-form-strokedark dark:bg-form-input flex w-full flex-col flex-wrap gap-4 rounded border-[1.5px] bg-transparent p-3 font-normal outline-none transition dark:text-white"
        >
            <RadioField
                v-model="model"
                :name="generateName(field.id)"
                :value="0"
                label="Belirtilmemiş"
            />
            <RadioField
                v-for="value in field.values"
                v-model="model"
                :label="value.value"
                :name="generateName(field.id, value.id)"
                :value="value.id"
            />
        </div>
    </div>
</template>

<style>
.p-accordionpanel:last-child {
    @apply border-b-0;
}

.p-accordionheader {
    @apply border hover:bg-primary/30;
}

.p-accordioncontent-content {
    @apply border border-t-0;
}
</style>
