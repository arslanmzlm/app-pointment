<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import {computed} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import AppointmentMultipleForm from '@/Forms/AppointmentMultipleForm.vue';
import TreatmentPayment from '@/Forms/Parts/TreatmentPayment.vue';
import TreatmentProducts from '@/Forms/Parts/TreatmentProducts.vue';
import TreatmentServices from '@/Forms/Parts/TreatmentServices.vue';
import TextareaField from '@/Components/Form/TextareaField.vue';
import AppointmentTable from '@/Components/Tables/AppointmentTable.vue';
import {PaymentMethod} from '@/types/enums';
import {TreatmentFormType} from '@/types/form';
import {Appointment, AppointmentType, Product, Service} from '@/types/model';
import {EnumResponse} from '@/types/response';

const props = defineProps<{
    appointment: Appointment;
    appointmentTypes: AppointmentType[];
    services: Service[];
    products: Product[];
    paymentMethods: EnumResponse[];
    passiveDates: string[];
    appointments: Appointment[];
}>();

const appointment = props.appointment;
const pageTitle = computed(() => `Randevuyu Tamamla #${appointment.id}`);
const breadcrumbs = [{label: 'Randevular', url: route('dashboard.appointment.calendar')}];

const form = useForm<TreatmentFormType>({
    note: '',
    services: [],
    products: [],
    appointments: [],
    payment_method: PaymentMethod.CASH,
});

function submit() {
    form.post(route('dashboard.appointment.complete', {id: appointment.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs :title="pageTitle">
        <form class="space-y-6" @submit.prevent="submit">
            <Card>
                <template #content>
                    <TextareaField v-model="form.note" :error="form.errors.note" label="Not" />
                </template>
            </Card>

            <TreatmentServices :data="form.services" :errors="form.errors" :services />

            <TreatmentProducts :data="form.products" :errors="form.errors" :products />

            <AppointmentMultipleForm
                v-if="form.appointments"
                :appointment-types
                :form
                :passive-dates
            />

            <AppointmentTable :appointments title="Hastanın Aktif Randevuları" />

            <TreatmentPayment :form :payment-methods />

            <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
        </form>
    </DashboardLayout>
</template>
