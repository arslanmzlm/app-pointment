<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import AppointmentMultipleForm from '@/Forms/AppointmentMultipleForm.vue';
import PatientSelector from '@/Forms/Parts/PatientSelector.vue';
import TreatmentPayment from '@/Forms/Parts/TreatmentPayment.vue';
import TreatmentProducts from '@/Forms/Parts/TreatmentProducts.vue';
import TreatmentServices from '@/Forms/Parts/TreatmentServices.vue';
import TextareaField from '@/Components/Form/TextareaField.vue';
import {PaymentMethod} from '@/types/enums';
import {TreatmentFormType} from '@/types/form';
import {Product, Service} from '@/types/model';
import {EnumResponse} from '@/types/response';

defineProps<{
    services: Service[];
    products: Product[];
    paymentMethods: EnumResponse[];
    passiveDates: string[];
}>();

const breadcrumbs = [{label: 'İşlemler', url: route('dashboard.treatment.list')}];

const form = useForm<TreatmentFormType>({
    patient_id: 0,
    note: null,
    services: [],
    products: [],
    appointments: [],
    payment_method: PaymentMethod.CASH,
});

function submit() {
    form.post(route('dashboard.treatment.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="İşlem Oluştur">
        <form class="space-y-6" @submit.prevent="submit">
            <Card>
                <template #content>
                    <PatientSelector v-model="form.patient_id" :error="form.errors.patient_id" />

                    <TextareaField v-model="form.note" :error="form.errors.note" label="Not" />
                </template>
            </Card>

            <TreatmentServices :data="form.services" :errors="form.errors" :services />

            <TreatmentProducts :data="form.products" :errors="form.errors" :products />

            <AppointmentMultipleForm v-if="form.appointments" :form :passive-dates />

            <TreatmentPayment :form :payment-methods />

            <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
        </form>
    </DashboardLayout>
</template>
