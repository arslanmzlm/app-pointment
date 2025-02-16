<script lang="ts" setup>
import {Card, Column, DataTable} from 'primevue';
import {computed} from 'vue';
import ReportCard from '@/Components/Reports/ReportCard.vue';
import {genderHelper} from '@/Utilities/enumHelper';
import {Gender} from '@/types/enums';
import {EnumResponse, PatientReportResponse} from '@/types/response';

const props = defineProps<{
    reports: PatientReportResponse;
    provinceNames: {[key: number]: string};
    genders: EnumResponse[];
}>();

const perGender = computed(() => {
    const male = props.reports.per_gender.find((row) => {
        return row.gender === Gender.MALE;
    });
    const female = props.reports.per_gender.find((row) => {
        return row.gender === Gender.FEMALE;
    });

    const data: {[key: number]: string} = {};
    props.genders.forEach((row) => {
        data[row.value] = row.label;
    });

    return {
        male: {
            icon: genderHelper(Gender.MALE).icon,
            severity: genderHelper(Gender.MALE).severity,
            label: data[Gender.MALE],
            value: male?.count ?? 0,
        },
        female: {
            icon: genderHelper(Gender.FEMALE).icon,
            severity: genderHelper(Gender.FEMALE).severity,
            label: data[Gender.FEMALE],
            value: female?.count ?? 0,
        },
    };
});
</script>

<template>
    <Card>
        <template #title>Genel Rapor</template>
        <template #content>
            <div class="grid grid-cols-12 gap-4">
                <ReportCard
                    :value="reports.total"
                    icon="pi pi-users"
                    severity="info"
                    title="Toplam Kayıt"
                    type-icon="pi pi-user"
                    type-label="Hasta"
                    type-severity="success"
                />

                <ReportCard
                    :value="reports.old_patient"
                    icon="pi pi-check-square"
                    severity="secondary"
                    title="Eski Hasta Olarak İşaretlenenler"
                    type-icon="pi pi-user"
                    type-label="Eski Hasta"
                    type-severity="danger"
                />

                <ReportCard
                    :value="reports.total - reports.old_patient"
                    icon="pi pi-check-square"
                    severity="primary"
                    title="Yeni Hasta Olarak İşaretlenenler"
                    type-icon="pi pi-user"
                    type-label="Eski Hasta"
                    type-severity="danger"
                />

                <ReportCard
                    :value="reports.registered_own"
                    icon="pi pi-user-plus"
                    severity="warn"
                    title="Kendi Kayıt Olan"
                    type-icon="pi pi-user"
                    type-label="Kayıt Türü"
                    type-severity="info"
                />

                <ReportCard
                    :value="reports.total - reports.registered_own"
                    icon="pi pi-user-plus"
                    severity="danger"
                    title="Sistem Tarafından Kayıt Olan"
                    type-icon="pi pi-user"
                    type-label="Kayıt Türü"
                    type-severity="info"
                />

                <ReportCard
                    :icon="perGender.male.icon"
                    :severity="perGender.male.severity"
                    :title="perGender.male.label"
                    :value="perGender.male.value"
                    type-icon="pi pi-user"
                    type-label="Cinsiyete Göre"
                    type-severity="warn"
                />

                <ReportCard
                    :icon="perGender.female.icon"
                    :severity="perGender.female.severity"
                    :title="perGender.female.label"
                    :value="perGender.female.value"
                    type-icon="pi pi-user"
                    type-label="Cinsiyete Göre"
                    type-severity="warn"
                />
            </div>
        </template>
    </Card>

    <Card v-if="reports.per_province && reports.per_province.length > 0">
        <template #title>Şehre Göre Kayıt Sayısı</template>
        <template #content>
            <DataTable :value="reports.per_province" row-hover>
                <Column field="province_id" header="Şehir">
                    <template #body="slotProps">
                        <span class="font-medium">{{
                            provinceNames[slotProps.data.province_id]
                        }}</span>
                    </template>
                </Column>
                <Column field="count" header="Kayıt Sayısı" sortable />
            </DataTable>
        </template>
    </Card>
</template>
