<script lang="ts" setup>
import {Card, Column, DataTable, Tag} from 'primevue';
import {computed} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ReportCard from '@/Components/Reports/ReportCard.vue';
import ReportDateFilter from '@/Components/Reports/ReportDateFilter.vue';
import {appointmentState} from '@/Utilities/enumHelper';
import {Hospital} from '@/types/model';
import {AppointmentReportResponse} from '@/types/response';

const props = defineProps<{
    reports: AppointmentReportResponse;
    hospitals?: Hospital[];
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
    <DashboardLayout title="İşlem Raporu">
        <ReportDateFilter cache-key="appointment" />

        <Card>
            <template #title>Genel Rapor</template>
            <template #content>
                <div class="grid grid-cols-12 gap-4">
                    <ReportCard
                        v-for="report in reports.per_state"
                        :icon="appointmentState(report.state).icon"
                        :severity="appointmentState(report.state).severity"
                        :title="report.state_label"
                        :value="report.count"
                        type-icon="pi pi-calendar"
                        type-label="Randevu Durumu"
                        type-severity="primary"
                    />
                    <ReportCard
                        :value="reports.per_patient ?? 0"
                        icon="pi pi-users"
                        severity="info"
                        title="Hastaların Oluşturduğu Randevu"
                        type-icon="pi pi-calendar"
                        type-label="Randevu"
                        type-severity="primary"
                    />
                </div>
            </template>
        </Card>

        <Card v-for="(report, hospitalId) in reports.per_doctor" :key="hospitalId">
            <template #title>{{ hospitalNames[hospitalId] }}</template>
            <template #content>
                <DataTable
                    v-if="report && report.length > 0"
                    :value="report"
                    group-rows-by="full_name"
                    row-group-mode="rowspan"
                    row-hover
                >
                    <Column field="full_name" header="Podolog" />
                    <Column field="state_label" header="Randevu Durumu">
                        <template #body="slotProps">
                            <Tag
                                :icon="appointmentState(slotProps.data.state).icon"
                                :severity="appointmentState(slotProps.data.state).severity"
                                :value="slotProps.data.state_label"
                            />
                        </template>
                    </Column>
                    <Column field="count" header="Randevu Sayısı" />
                </DataTable>
            </template>
        </Card>
    </DashboardLayout>
</template>
