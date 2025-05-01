<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import AppointmentTypeForm from '@/Forms/AppointmentTypeForm.vue';
import {AppointmentTypeFormType} from '@/types/form';
import {Appointment, AppointmentType} from '@/types/model';

const props = defineProps<{
    appointmentType: AppointmentType;
    passiveDates: string[];
    appointments?: Appointment[];
}>();

const appointmentType = props.appointmentType;
const breadcrumbs = [{label: 'Randevu Tipleri', url: route('dashboard.appointment.type.list')}];

const form = useForm<AppointmentTypeFormType>({
    name: appointmentType.name,
});

function submit() {
    form.post(route('dashboard.appointment.type.update', {id: appointmentType.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Randevu Tipi Düzenle">
        <AppointmentTypeForm :form @submit.prevent="submit" />
    </DashboardLayout>
</template>
