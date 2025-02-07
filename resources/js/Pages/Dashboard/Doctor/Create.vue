<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import DoctorForm from '@/Forms/DoctorForm.vue';
import {DoctorFormType} from '@/types/form';
import {Hospital} from '@/types/model';

defineProps<{
    hospitals?: Hospital[];
    branches: string[];
}>();

const breadcrumbs = [{label: 'Doktorlar', url: route('dashboard.doctor.list')}];

const form = useForm<DoctorFormType>({
    hospital_id: 0,
    name: '',
    surname: '',
    branch: '',
    avatar: null,
    title: '',
    phone: '',
    email: '',
    resume: '',
    certificate: '',
});

function submit() {
    form.post(route('dashboard.doctor.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Doktor Oluştur">
        <DoctorForm :branches :form :hospitals @submit.prevent="submit" />
    </DashboardLayout>
</template>
