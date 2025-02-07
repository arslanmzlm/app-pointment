<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import AppointmentMultipleForm from '@/Forms/AppointmentMultipleForm.vue';
import PatientSelector from '@/Forms/Parts/PatientSelector.vue';
import {AppointmentMultipleFormType} from '@/types/form';

defineProps<{
    passiveDates?: string[];
}>();

const breadcrumbs = [{label: 'Randevular', url: route('dashboard.appointment.list')}];

const form = useForm<AppointmentMultipleFormType>({
    patient_id: 0,
    appointments: [],
});

function submit() {
    form.post(route('dashboard.appointment.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Randevu Oluştur">
        <form class="space-y-6" @submit.prevent="submit">
            <Card>
                <template #content>
                    <PatientSelector v-model="form.patient_id" :error="form.errors.patient_id" />
                </template>
            </Card>

            <AppointmentMultipleForm v-if="form.appointments" :form :passive-dates />

            <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
        </form>
    </DashboardLayout>
</template>
