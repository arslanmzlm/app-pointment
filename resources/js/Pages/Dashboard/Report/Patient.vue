<script lang="ts" setup>
import {computed} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PatientReportCard from '@/Components/Reports/PatientReportCard.vue';
import ReportDateFilter from '@/Components/Reports/ReportDateFilter.vue';
import {Province} from '@/types/model';
import {EnumResponse, PatientReportResponse} from '@/types/response';

const props = defineProps<{
    reports: PatientReportResponse;
    provinces: Province[];
    genders: EnumResponse[];
}>();

const provinceNames = computed(() => {
    const data: {[key: number]: string} = {};

    if (props.provinces) {
        props.provinces.forEach((province) => {
            data[province.id] = province.name;
        });
    }

    return data;
});
</script>

<template>
    <DashboardLayout title="Hasta Raporu">
        <ReportDateFilter />

        <PatientReportCard :genders :province-names :reports />
    </DashboardLayout>
</template>
