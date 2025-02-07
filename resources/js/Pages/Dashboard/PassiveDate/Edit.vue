<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import {computed} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PassiveDateForm from '@/Forms/PassiveDateForm.vue';
import {PassiveDateFromType} from '@/types/form';
import {PassiveDate} from '@/types/model';

const props = defineProps<{
    passiveDate: PassiveDate;
}>();

const passiveDate = props.passiveDate;
const pageTitle = computed(() => `İzni Düzenle #${passiveDate.id}`);
const breadcrumbs = [{label: 'İzinler', url: route('dashboard.passive.date.list')}];

const form = useForm<PassiveDateFromType>({
    start_date: new Date(passiveDate.start_date),
    due_date: new Date(passiveDate.due_date),
    description: passiveDate.description,
});

function submit() {
    form.post(route('dashboard.passive.date.update', {id: passiveDate.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs :title="pageTitle">
        <PassiveDateForm :form @submit.prevent="submit" />
    </DashboardLayout>
</template>
