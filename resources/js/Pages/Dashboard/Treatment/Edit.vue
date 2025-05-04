<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import TreatmentPayment from '@/Forms/Parts/TreatmentPayment.vue';
import TreatmentProducts from '@/Forms/Parts/TreatmentProducts.vue';
import TreatmentServices from '@/Forms/Parts/TreatmentServices.vue';
import TextareaField from '@/Components/Form/TextareaField.vue';
import {productLabel, serviceLabel} from '@/Utilities/labels';
import {TreatmentFormType} from '@/types/form';
import {Product, Service, Transaction, Treatment} from '@/types/model';
import {EnumResponse} from '@/types/response';

const props = defineProps<{
    treatment: Treatment;
    services: Service[];
    products: Product[];
    transactions: Transaction[];
    paymentMethods: EnumResponse[];
}>();

const treatment = props.treatment;
const breadcrumbs = [{label: 'İşlemler', url: route('dashboard.treatment.list')}];

const form = useForm<TreatmentFormType>({
    note: treatment.note,
    services: [],
    products: [],
    payments: [],
});

props.treatment.services.forEach((item) => {
    form.services.push({
        id: item.id,
        service_id: item.service_id,
        label: serviceLabel(<Service>item.service),
        price: item.price,
    });
});

props.treatment.products.forEach((item) => {
    form.products.push({
        id: item.id,
        product_id: item.product_id,
        label: productLabel(<Product>item.product),
        count: item.count,
        price: item.price,
    });
});

props.paymentMethods.forEach((method) => {
    const transaction = props.transactions.find((transaction) => {
        return transaction.method === method.value;
    });

    form.payments.push({
        method: method.value,
        amount: transaction ? transaction.total : 0,
        label: method.label,
    });
});

function submit() {
    form.post(route('dashboard.treatment.update', {id: treatment.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="İşlem Düzenle">
        <form class="space-y-6" @submit.prevent="submit">
            <Card>
                <template #content>
                    <TextareaField v-model="form.note" :error="form.errors.note" label="Not" />
                </template>
            </Card>

            <TreatmentServices :data="form.services" :errors="form.errors" :services />

            <TreatmentProducts :data="form.products" :errors="form.errors" :products />

            <TreatmentPayment :form :payment-methods />

            <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
        </form>
    </DashboardLayout>
</template>
