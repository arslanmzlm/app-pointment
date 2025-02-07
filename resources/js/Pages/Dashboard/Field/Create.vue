<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import FieldForm from '@/Forms/FieldForm.vue';
import {FieldInput} from '@/types/enums';
import {FieldFormType} from '@/types/form';
import {FieldInputEnumResponse} from '@/types/response';

defineProps<{
    inputs: FieldInputEnumResponse[];
}>();

const breadcrumbs = [{label: 'Alanlar', url: route('dashboard.field.list')}];

const form = useForm<FieldFormType>({
    name: '',
    input: FieldInput.TEXT,
    description: null,
    order: null,
    values: [],
});

function submit() {
    form.post(route('dashboard.field.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Alan Oluştur">
        <FieldForm :form :inputs @submit.prevent="submit" />
    </DashboardLayout>
</template>
