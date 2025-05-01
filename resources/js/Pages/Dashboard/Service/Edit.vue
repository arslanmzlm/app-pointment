<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ServiceForm from '@/Forms/ServiceForm.vue';
import {ServiceFormType} from '@/types/form';
import {Service} from '@/types/model';

const props = defineProps<{
    service: Service;
}>();

const service = props.service;
const breadcrumbs = [{label: 'Hizmetler', url: route('dashboard.service.list')}];

const form = useForm<ServiceFormType>({
    active: service.active,
    name: service.name,
    code: service.code,
    price: service.price,
});

function submit() {
    form.post(route('dashboard.service.update', {id: service.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Hizmet Düzenle">
        <ServiceForm :form @submit.prevent="submit" />
    </DashboardLayout>
</template>
