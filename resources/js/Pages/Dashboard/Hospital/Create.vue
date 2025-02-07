<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import HospitalForm from '@/Forms/HospitalForm.vue';
import {HospitalFormType} from '@/types/form';
import {Province} from '@/types/model';

defineProps<{
    provinces: Province[];
}>();

const breadcrumbs = [{label: 'Hastaneler', url: route('dashboard.hospital.list')}];

const form = useForm<HospitalFormType>({
    province_id: 0,
    name: '',
    start_work: 0,
    end_work: 23,
    duration: 60,
    title: '',
    logo: null,
    description: '',
    owner: '',
    phone: '',
    email: '',
    address: '',
    address_link: '',
});

function submit() {
    form.post(route('dashboard.hospital.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Hastane Oluştur">
        <HospitalForm :form :provinces @submit.prevent="submit" />
    </DashboardLayout>
</template>
