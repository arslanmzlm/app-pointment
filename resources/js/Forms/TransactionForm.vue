<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import PatientSelector from '@/Forms/Parts/PatientSelector.vue';
import ListBoxField from '@/Components/Form/ListBoxField.vue';
import NumberField from '@/Components/Form/NumberField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import TextareaField from '@/Components/Form/TextareaField.vue';
import {paymentMethod, transactionType} from '@/Utilities/enumHelper';
import {TransactionFormType} from '@/types/form';
import {Hospital} from '@/types/model';
import {EnumResponse} from '@/types/response';

defineProps<{
    form: InertiaForm<TransactionFormType>;
    transactionTypes: EnumResponse[];
    paymentMethods: EnumResponse[];
    hospitals?: Hospital[];
}>();
</script>

<template>
    <form class="space-y-6">
        <Card>
            <template #content>
                <SelectField
                    v-if="hospitals && form.hospital_id !== undefined"
                    v-model="form.hospital_id"
                    :error="form.errors.hospital_id"
                    :options="hospitals"
                    label="Hastane"
                    required
                />

                <ListBoxField
                    v-model="form.type"
                    :error="form.errors.type"
                    :options="transactionTypes"
                    label="İşlem Türü"
                >
                    <template #option="slotProps">
                        <div class="flex items-center gap-2">
                            <span :class="transactionType(slotProps.data.option.value).icon"></span>
                            <div>{{ slotProps.data.option.label }}</div>
                        </div>
                    </template>
                </ListBoxField>

                <PatientSelector
                    v-model="form.patient_id"
                    :error="form.errors.patient_id"
                    show-clear
                />

                <ListBoxField
                    v-model="form.method"
                    :error="form.errors.method"
                    :options="paymentMethods"
                    label="Ödeme Yöntemi"
                >
                    <template #option="slotProps">
                        <div class="flex items-center gap-2">
                            <span :class="paymentMethod(slotProps.data.option.value).icon"></span>
                            <div>{{ slotProps.data.option.label }}</div>
                        </div>
                    </template>
                </ListBoxField>

                <NumberField
                    v-model="form.total"
                    :error="form.errors.total"
                    label="Tutar"
                    mode="currency"
                />

                <TextareaField
                    v-model="form.description"
                    :error="form.errors.description"
                    label="Açıklama"
                />
            </template>
        </Card>

        <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
    </form>
</template>
