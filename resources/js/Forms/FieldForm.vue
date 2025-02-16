<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import {computed, watch} from 'vue';
import InputField from '@/Components/Form/InputField.vue';
import NumberField from '@/Components/Form/NumberField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import TextareaField from '@/Components/Form/TextareaField.vue';
import {FieldFormType} from '@/types/form';
import {FieldInputEnumResponse} from '@/types/response';

const props = defineProps<{
    form: InertiaForm<FieldFormType>;
    inputs: FieldInputEnumResponse[];
}>();

const input = computed(() => {
    return props.inputs.find((row) => {
        return row.value === props.form.input;
    });
});

function addValue() {
    props.form.values.push({id: null, value: ''});
}

function removeValue(index: number) {
    props.form.values.splice(index, 1);
}

watch(
    () => props.form.input,
    () => {
        if (!input.value?.hasValues) {
            props.form.values = [];
        }
    },
);
</script>

<template>
    <form class="space-y-6">
        <Card>
            <template #content>
                <InputField
                    v-model="form.name"
                    :error="form.errors.name"
                    label="Alan"
                    name="name"
                    required
                />

                <SelectField
                    v-model="form.input"
                    :options="inputs"
                    label="Değer Tipi"
                    option-label="label"
                    option-value="value"
                    required
                />

                <div
                    v-if="input && input.hasValues"
                    class="space-y-3 rounded border p-3 dark:border-surface-600"
                >
                    <div class="text-lg">Değerler</div>
                    <template v-for="(_, index) in form.values" :key="index">
                        <div class="flex gap-2">
                            <InputField
                                v-model="form.values[index]['value']"
                                :error="form.errors[`values.${index}.value`]"
                                class="flex-grow"
                            />

                            <Button
                                icon="pi pi-minus"
                                severity="danger"
                                title="Değeri Çıkar"
                                @click="removeValue(index)"
                            />
                        </div>
                    </template>

                    <Button
                        class="btn-block"
                        icon="pi pi-plus"
                        label="Değer Ekle"
                        severity="info"
                        @click="addValue"
                    />
                </div>

                <TextareaField
                    v-model="form.description"
                    :error="form.errors.description"
                    label="Açıklama"
                    name="description"
                />

                <NumberField
                    v-model.number="form.order"
                    :error="form.errors.order"
                    label="Sıra"
                    name="order"
                    type="number"
                />
            </template>
        </Card>

        <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
    </form>
</template>
