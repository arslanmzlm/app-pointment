<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import dayjs from 'dayjs';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import HospitalForm from '@/Forms/HospitalForm.vue';
import {getInt} from '@/Utilities/parser';
import {HospitalFormType} from '@/types/form';
import {Hospital, Province} from '@/types/model';

const props = defineProps<{
    hospital: Hospital;
    provinces: Province[];
}>();

const hospital = props.hospital;
const breadcrumbs = [{label: 'Hastaneler', url: route('dashboard.hospital.list')}];

const startWork = dayjs()
    .set('hours', <number>getInt(props.hospital.start_work.split(':')[0]))
    .set('minutes', <number>getInt(props.hospital.start_work.split(':')[1]))
    .toDate();

const endWork = dayjs()
    .set('hours', <number>getInt(props.hospital.end_work.split(':')[0]))
    .set('minutes', <number>getInt(props.hospital.end_work.split(':')[1]))
    .toDate();

const form = useForm<HospitalFormType>({
    province_id: hospital.province_id,
    name: hospital.name,
    start_work: startWork,
    end_work: endWork,
    duration: hospital.duration,
    disabled_days: hospital.disabled_days,
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
    form.transform((data) => ({
        ...data,
        start_work: data.start_work !== null ? dayjs(data.start_work).format('HH:mm') : null,
        end_work: data.end_work !== null ? dayjs(data.end_work).format('HH:mm') : null,
    })).post(route('dashboard.hospital.update', {id: hospital.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Hastane Düzenle">
        <HospitalForm :form :logo-src="hospital.logo_src" :provinces @submit.prevent="submit" />
    </DashboardLayout>
</template>
