<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PassiveDateForm from '@/Forms/PassiveDateForm.vue';
import {PassiveDateFromType} from '@/types/form';
import {Doctor, Hospital} from '@/types/model';

defineProps<{
    hospitals?: Hospital[];
    doctors?: Doctor[];
}>();

const breadcrumbs = [{label: 'İzinler', url: route('dashboard.passive.date.list')}];

const form = useForm<PassiveDateFromType>({
    doctor_id: 0,
    start_date: null,
    due_date: null,
    description: null,
});

function submit() {
    form.post(route('dashboard.passive.date.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="İzin Oluştur">
        <PassiveDateForm :doctors :form :hospitals @submit.prevent="submit" />
    </DashboardLayout>
</template>
