<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import TransactionForm from '@/Forms/TransactionForm.vue';
import {PaymentMethod, TransactionType} from '@/types/enums';
import {TransactionFormType} from '@/types/form';
import {Hospital} from '@/types/model';
import {EnumResponse} from '@/types/response';

defineProps<{
    transactionTypes: EnumResponse[];
    paymentMethods: EnumResponse[];
    hospitals?: Hospital[];
}>();

const breadcrumbs = [{label: 'İşlemler', url: route('dashboard.transaction.list')}];

const form = useForm<TransactionFormType>({
    hospital_id: 0,
    type: TransactionType.INCOME,
    method: PaymentMethod.CASH,
    total: null,
    patient_id: null,
    description: null,
});

function submit() {
    form.post(route('dashboard.transaction.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="İşlem Oluştur">
        <TransactionForm
            :form
            :hospitals
            :payment-methods
            :transaction-types
            @submit.prevent="submit"
        />
    </DashboardLayout>
</template>
