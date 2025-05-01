<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import AppointmentForm from '@/Forms/AppointmentForm.vue';
import {AppointmentFormType} from '@/types/form';
import {Appointment, AppointmentType, Service} from '@/types/model';

const props = defineProps<{
    appointment: Appointment;
    appointmentTypes: AppointmentType[];
    services: Service[];
    passiveDates: string[];
    appointments?: Appointment[];
}>();

const appointment = props.appointment;
const breadcrumbs = [{label: 'Randevular', url: route('dashboard.appointment.list')}];

const form = useForm<AppointmentFormType>({
    start_date: new Date(appointment.start_date),
    appointment_type_id: appointment.appointment_type_id,
    duration: appointment.duration,
    note: appointment.note,
    service_id: appointment.service_id,
});

function submit() {
    form.post(route('dashboard.appointment.update', {id: appointment.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Randevu Düzenle">
        <AppointmentForm
            :appointment-types
            :appointments
            :doctor-id="appointment.doctor_id"
            :form
            :passive-dates
            :patient="appointment.patient"
            :services
            @submit.prevent="submit"
        />
    </DashboardLayout>
</template>
