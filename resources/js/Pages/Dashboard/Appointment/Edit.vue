<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import {computed} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import AppointmentForm from '@/Forms/AppointmentForm.vue';
import {AppointmentFormType} from '@/types/form';
import {Appointment} from '@/types/model';

const props = defineProps<{
    appointment: Appointment;
    passiveDates: string[];
    appointments?: Appointment[];
}>();

const appointment = props.appointment;
const pageTitle = computed(() => `Randevuyu Düzenle #${appointment.id}`);
const breadcrumbs = [{label: 'Randevular', url: route('dashboard.appointment.list')}];

const form = useForm<AppointmentFormType>({
    start_date: new Date(appointment.start_date),
    duration: appointment.duration,
    title: appointment.title,
    note: appointment.note,
});

function submit() {
    form.post(route('dashboard.appointment.update', {id: appointment.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs :title="pageTitle">
        <AppointmentForm
            :appointments
            :form
            :passive-dates
            :patient="appointment.patient"
            @submit.prevent="submit"
        />
    </DashboardLayout>
</template>
