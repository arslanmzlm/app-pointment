<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ServiceForm from '@/Forms/ServiceForm.vue';
import {ServiceFormType} from '@/types/form';
import {Hospital} from '@/types/model';

defineProps<{
    hospitals?: Hospital[];
}>();

const breadcrumbs = [{label: 'Hizmetler', url: route('dashboard.service.list')}];

const form = useForm<ServiceFormType>({
    hospital_id: 0,
    active: true,
    name: '',
    code: null,
    price: null,
});

function submit() {
    form.post(route('dashboard.service.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Hizmet Oluştur">
        <ServiceForm :form :hospitals @submit.prevent="submit" />
    </DashboardLayout>
</template>
