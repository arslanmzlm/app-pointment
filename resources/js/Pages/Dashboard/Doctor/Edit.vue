<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import DoctorForm from '@/Forms/DoctorForm.vue';
import {DoctorFormType} from '@/types/form';
import {Doctor} from '@/types/model';

const props = defineProps<{
    doctor: Doctor;
    branches: string[];
}>();

const doctor = props.doctor;
const breadcrumbs = [{label: 'Podologlar', url: route('dashboard.doctor.list')}];

const form = useForm<DoctorFormType>({
    name: doctor.name,
    surname: doctor.surname,
    branch: doctor.branch,
    avatar: null,
    title: doctor.title,
    phone: doctor.phone ?? '',
    email: doctor.email,
    resume: doctor.resume ?? '',
    certificate: doctor.certificate ?? '',
});

function submit() {
    form.post(route('dashboard.doctor.update', {id: doctor.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Podolog Düzenle">
        <DoctorForm :avatar-src="doctor.avatar_src" :branches :form @submit.prevent="submit" />
    </DashboardLayout>
</template>
