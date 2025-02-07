<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import {computed} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ProductForm from '@/Forms/ProductForm.vue';
import {ProductFormType} from '@/types/form';
import {Product} from '@/types/model';

const props = defineProps<{
    product: Product;
    categories: string[];
}>();

const product = props.product;
const pageTitle = computed(() => `Ürün Düzenle #${product.id}`);
const breadcrumbs = [{label: 'Ürünler', url: route('dashboard.product.list')}];

const form = useForm<ProductFormType>({
    active: product.active,
    category: product.category,
    name: product.name,
    code: product.code,
    stock: product.stock,
    price: product.price,
});

function submit() {
    form.post(route('dashboard.product.update', {id: product.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs :title="pageTitle">
        <ProductForm :categories :form @submit.prevent="submit" />
    </DashboardLayout>
</template>
