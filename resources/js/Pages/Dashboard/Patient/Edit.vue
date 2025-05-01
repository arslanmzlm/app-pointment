<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PatientForm from '@/Forms/PatientForm.vue';
import {PatientFormType} from '@/types/form';
import {Field, Patient, Province} from '@/types/model';
import {EnumResponse, FieldValueResponse} from '@/types/response';

const props = defineProps<{
    patient: Patient;
    provinces: Province[];
    genders: EnumResponse[];
    fields: Field[];
    fieldValues: FieldValueResponse[];
}>();

const patient = props.patient;
const breadcrumbs = [{label: 'Hastalar', url: route('dashboard.patient.list')}];

const form = useForm<PatientFormType>({
    old: patient.old,
    province_id: patient.province_id,
    name: patient.name,
    surname: patient.surname,
    phone: patient.phone,
    email: patient.email,
    gender: patient.gender,
    birthday: patient.birthday ? new Date(patient.birthday) : null,
    notification: patient.notification,
    fields: props.fieldValues,
});

function submit() {
    form.post(route('dashboard.patient.update', {id: patient.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Hasta Düzenle">
        <PatientForm :fields :form :genders :provinces @submit.prevent="submit" />
    </DashboardLayout>
</template>
