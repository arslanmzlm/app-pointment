<script lang="ts" setup>
import {computed} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ReportDateFilter from '@/Components/ReportDateFilter.vue';
import TransactionCard from '@/Components/TransactionCard.vue';
import {Hospital} from '@/types/model';
import {EnumResponse, TransactionReport} from '@/types/response';

const props = defineProps<{
    reports: {[key: number]: TransactionReport};
    hospitals?: Hospital[];
    transactionTypes: EnumResponse[];
    paymentMethods: EnumResponse[];
}>();

const hospitalNames = computed(() => {
    const data: {[key: number]: string} = {};

    if (props.hospitals) {
        props.hospitals.forEach((hospital) => {
            data[hospital.id] = hospital.name;
        });
    }

    return data;
});
</script>

<template>
    <DashboardLayout title="Kasa Raporu">
        <ReportDateFilter />

        <div v-if="reports" class="space-y-6">
            <TransactionCard
                v-for="(report, hospitalId) in reports"
                :key="hospitalId"
                :hospital-name="hospitalNames[hospitalId]"
                :report
            />
        </div>
    </DashboardLayout>
</template>

<style>
.method-icon .p-tag-icon {
    @apply size-5;
    font-size: 20px !important;
    line-height: 1 !important;
}
</style>
