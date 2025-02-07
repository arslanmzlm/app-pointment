<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ProductForm from '@/Forms/ProductForm.vue';
import {ProductFormType} from '@/types/form';
import {Hospital} from '@/types/model';

defineProps<{
    categories: string[];
    hospitals?: Hospital[];
}>();

const breadcrumbs = [{label: 'Ürünler', url: route('dashboard.product.list')}];

const form = useForm<ProductFormType>({
    hospital_id: 0,
    active: true,
    category: '',
    name: '',
    code: null,
    stock: null,
    price: null,
});

function submit() {
    form.post(route('dashboard.product.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Ürün Oluştur">
        <ProductForm :categories :form :hospitals @submit.prevent="submit" />
    </DashboardLayout>
</template>
