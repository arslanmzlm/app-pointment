<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import AppointmentTypeForm from '@/Forms/AppointmentTypeForm.vue';
import {AppointmentTypeFormType} from '@/types/form';

defineProps<{
    passiveDates?: string[];
}>();

const breadcrumbs = [{label: 'Randevular', url: route('dashboard.appointment.type.list')}];

const form = useForm<AppointmentTypeFormType>({
    name: '',
});

function submit() {
    form.post(route('dashboard.appointment.type.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Randevu Tipi Oluştur">
        <AppointmentTypeForm :form @submit.prevent="submit" />
    </DashboardLayout>
</template>
