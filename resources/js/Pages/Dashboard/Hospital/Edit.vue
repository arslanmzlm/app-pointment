<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import {computed} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import HospitalForm from '@/Forms/HospitalForm.vue';
import {HospitalFormType} from '@/types/form';
import {Hospital, Province} from '@/types/model';

const props = defineProps<{
    hospital: Hospital;
    provinces: Province[];
}>();

const hospital = props.hospital;
const pageTitle = computed(() => `Hastane Düzenle #${hospital.id}`);
const breadcrumbs = [{label: 'Hastaneler', url: route('dashboard.hospital.list')}];

const form = useForm<HospitalFormType>({
    province_id: hospital.province_id,
    name: hospital.name,
    start_work: hospital.start_work,
    end_work: hospital.end_work,
    duration: hospital.duration,
    title: hospital.title,
    logo: null,
    description: hospital.description ?? '',
    owner: hospital.owner,
    phone: hospital.phone ?? '',
    email: hospital.email,
    address: hospital.address,
    address_link: hospital.address_link,
});

function submit() {
    form.post(route('dashboard.hospital.update', {id: hospital.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs :title="pageTitle">
        <HospitalForm :form :logo-src="hospital.logo_src" :provinces @submit.prevent="submit" />
    </DashboardLayout>
</template>
