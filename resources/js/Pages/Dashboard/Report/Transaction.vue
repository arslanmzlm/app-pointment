<script lang="ts" setup>
import {computed} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ReportDateFilter from '@/Components/Reports/ReportDateFilter.vue';
import TransactionReportCard from '@/Components/Reports/TransactionReportCard.vue';
import {Hospital} from '@/types/model';
import {EnumResponse, TransactionReportResponse} from '@/types/response';

const props = defineProps<{
    reports: TransactionReportResponse;
    hospitals?: Hospital[];
    transactionTypes: EnumResponse[];
    paymentMethods: EnumResponse[];
}>();

const hospitalNames = computed(() => {
    const data: {[key: number]: string} = {0: 'Tüm Hastaneler'};

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
            <TransactionReportCard
                v-for="(report, hospitalId) in reports"
                :key="hospitalId"
                :hospital-name="hospitalNames[hospitalId] ?? undefined"
                :report
            />
        </div>
    </DashboardLayout>
</template>
