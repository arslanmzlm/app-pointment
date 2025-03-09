<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import dayjs from 'dayjs';
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
    start_work: null,
    end_work: null,
    duration: 60,
    disabled_days: [],
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
    form.transform((data) => ({
        ...data,
        start_work: data.start_work !== null ? dayjs(data.start_work).format('HH:mm') : null,
        end_work: data.end_work !== null ? dayjs(data.end_work).format('HH:mm') : null,
    })).post(route('dashboard.hospital.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Hastane Oluştur">
        <HospitalForm :form :provinces @submit.prevent="submit" />
    </DashboardLayout>
</template>
