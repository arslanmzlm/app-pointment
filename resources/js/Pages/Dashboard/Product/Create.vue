<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ProductForm from '@/Forms/ProductForm.vue';
import {ProductFormType} from '@/types/form';
import {Hospital} from '@/types/model';

const props = defineProps<{
    hospitals: Hospital[];
    categories: string[];
    brands: string[];
}>();

const breadcrumbs = [{label: 'Ürünler', url: route('dashboard.product.list')}];

const form = useForm<ProductFormType>({
    active: true,
    category: '',
    brand: '',
    name: '',
    slug: null,
    code: '',
    price: null,
    description: '',
    stocks: [],
    images: [],
});

props.hospitals.forEach((hospital: Hospital) => {
    form.stocks.push({
        id: null,
        hospital_id: hospital.id,
        hospital_name: hospital.name,
        stock: 0,
    });
});

function submit() {
    form.post(route('dashboard.product.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Ürün Oluştur">
        <ProductForm :brands :categories :form :hospitals @submit.prevent="submit" />
    </DashboardLayout>
</template>
