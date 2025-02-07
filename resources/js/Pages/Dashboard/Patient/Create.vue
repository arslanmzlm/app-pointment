<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PatientForm from '@/Forms/PatientForm.vue';
import {Gender} from '@/types/enums';
import {PatientFormType} from '@/types/form';
import {Field, Province} from '@/types/model';
import {EnumResponse} from '@/types/response';

const props = defineProps<{
    provinces: Province[];
    genders: EnumResponse[];
    fields: Field[];
}>();

const breadcrumbs = [{label: 'Hastalar', url: route('dashboard.patient.list')}];

const form = useForm<PatientFormType>({
    old: false,
    province_id: 0,
    name: '',
    surname: '',
    phone: '',
    email: null,
    gender: Gender.MALE,
    birthday: null,
    notification: true,
    fields: [],
});

if (props.fields.length > 0) {
    props.fields.forEach((field) => {
        form.fields.push({
            id: null,
            field_id: field.id,
            value: null,
        });
    });
}

function submit() {
    form.post(route('dashboard.patient.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Hasta Oluştur">
        <PatientForm :fields :form :genders :provinces @submit.prevent="submit" />
    </DashboardLayout>
</template>
