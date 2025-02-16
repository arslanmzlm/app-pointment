<script lang="ts" setup>
import {Card, Column, DataTable} from 'primevue';
import {computed} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ReportDateFilter from '@/Components/Reports/ReportDateFilter.vue';
import {currencyFormat} from '@/Utilities/formatters';
import {Hospital} from '@/types/model';
import {PerProductReportResponse, TreatmentReportResponse} from '@/types/response';

const props = defineProps<{
    reports: TreatmentReportResponse;
    perProductReport: PerProductReportResponse[];
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
        <ReportDateFilter />

        <Card v-for="(report, hospitalId) in reports" :key="hospitalId">
            <template #title>{{ hospitalNames[hospitalId] }}</template>
            <template #content>
                <DataTable
                    v-if="report.per_doctor && report.per_doctor.length > 0"
                    :value="report.per_doctor"
                    row-hover
                >
                    <template #header>
                        <h4>Doktora Göre Kayıtlar</h4>
                    </template>

                    <Column field="full_name" header="Doktor" sortable />
                    <Column field="branch" header="Alan" sortable />
                    <Column field="count" header="İşlem Sayısı" sortable />
                    <Column field="total" header="Toplam Tutar" sortable>
                        <template #body="slotProps">
                            {{ currencyFormat(slotProps.data.total) }}
                        </template>
                    </Column>
                </DataTable>

                <DataTable
                    v-if="report.per_service && report.per_service.length > 0"
                    :value="report.per_service"
                    row-hover
                >
                    <template #header>
                        <h4>Hizmete Göre Kayıtlar</h4>
                    </template>

                    <Column field="code" header="Hizmet Kodu" sortable />
                    <Column field="name" header="Hizmet" sortable />
                    <Column field="count" header="İşlem Sayısı" sortable />
                    <Column field="total" header="Toplam Tutar" sortable>
                        <template #body="slotProps">
                            {{ currencyFormat(slotProps.data.total) }}
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>

        <Card v-if="perProductReport && perProductReport.length > 0">
            <template #title>Ürüne Göre Kayıtlar</template>
            <template #content>
                <DataTable :value="perProductReport" row-hover>
                    <Column field="code" header="Ürün Kodu" sortable />
                    <Column field="name" header="Ürün" sortable />
                    <Column field="count" header="İşlem Sayısı" sortable />
                    <Column field="count_total" header="Satılan Toplam Ürün" sortable />
                    <Column field="total" header="Toplam Tutar" sortable>
                        <template #body="slotProps">
                            {{ currencyFormat(slotProps.data.total) }}
                        </template>
                    </Column>
                </DataTable>
            </template>
        </Card>
    </DashboardLayout>
</template>
