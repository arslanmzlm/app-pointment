<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import TransactionForm from '@/Forms/TransactionForm.vue';
import {TransactionFormType} from '@/types/form';
import {Transaction} from '@/types/model';
import {EnumResponse} from '@/types/response';

const props = defineProps<{
    transaction: Transaction;
    transactionTypes: EnumResponse[];
    paymentMethods: EnumResponse[];
}>();

const transaction = props.transaction;
const breadcrumbs = [{label: 'Kasa İşlemleri', url: route('dashboard.transaction.list')}];

const form = useForm<TransactionFormType>({
    type: transaction.type,
    method: transaction.method,
    total: transaction.total,
    patient_id: transaction.patient_id,
    description: transaction.description,
});

function submit() {
    form.post(route('dashboard.transaction.update', {id: transaction.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Kasa İşlemi Düzenle">
        <TransactionForm :form :payment-methods :transaction-types @submit.prevent="submit" />
    </DashboardLayout>
</template>
