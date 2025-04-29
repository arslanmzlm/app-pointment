<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import {computed} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import FieldForm from '@/Forms/FieldForm.vue';
import {FieldFormType} from '@/types/form';
import {Field} from '@/types/model';
import {FieldInputEnumResponse} from '@/types/response';

const props = defineProps<{
    field: Field;
    inputs: FieldInputEnumResponse[];
}>();

const field = props.field;
const pageTitle = computed(() => `Alanı Düzenle #${field.id}`);
const breadcrumbs = [{label: 'Alanlar', url: route('dashboard.field.list')}];

const form = useForm<FieldFormType>({
    name: field.name,
    input: field.input,
    description: field.description,
    order: field.order,
    printable: field.printable,
    values: field?.values ?? [],
});

function submit() {
    form.post(route('dashboard.field.update', {id: field.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs :title="pageTitle">
        <FieldForm :form :inputs @submit.prevent="submit" />
    </DashboardLayout>
</template>
