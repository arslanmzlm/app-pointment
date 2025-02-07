<script lang="ts" setup>
import {Button, Card, Column, DataTable, Tag} from 'primevue';
import {ref} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import FieldData from '@/Components/Field/FieldData.vue';
import AppointmentTable from '@/Components/Tables/AppointmentTable.vue';
import {currencyFormat, dateFormat, dateTimeFormat} from '@/Utilities/formatters';
import {Appointment, Patient, Province, Treatment} from '@/types/model';
import {PatientFieldResponse} from '@/types/response';

const props = defineProps<{
    patient: Patient;
    province: Province;
    fields: PatientFieldResponse[];
    treatments: Treatment[];
    appointments: Appointment[];
}>();

const expandedRows = ref<{[key: number]: boolean} | null>({});

const expandAll = () => {
    expandedRows.value = {};
    props.treatments.forEach((treatment) => {
        if (expandedRows.value) {
            expandedRows.value[treatment.id] = true;
        }
    });
};

const collapseAll = () => {
    expandedRows.value = null;
};
</script>

<template>
    <DashboardLayout title="Hasta Detayları">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <Card class="col-span-1">
                <template #title>{{ patient.full_name }}</template>
                <template #content>
                    <div class="space-y-2">
                        <div>{{ province.name }}</div>
                        <div>{{ patient.phone }}</div>
                        <div v-if="patient.email">{{ patient.email }}</div>
                        <div>{{ patient.gender_label }}</div>
                        <div v-if="patient.birthday" class="flex gap-1">
                            <span class="font-semibold">Doğum Tarihi:</span>
                            <span>{{ dateFormat(patient.birthday) }}</span>
                        </div>
                        <div class="flex gap-1">
                            <span class="font-semibold">Kayıt Tarihi:</span>
                            <span>{{ dateTimeFormat(patient.created_at) }}</span>
                        </div>
                        <div class="flex gap-1">
                            <span class="font-semibold">Son Düzenlenme Tarihi:</span>
                            <span>{{ dateTimeFormat(patient.updated_at) }}</span>
                        </div>
                        <div class="space-x-2">
                            <Tag
                                :severity="patient.old ? 'secondary' : 'primary'"
                                :value="patient.old ? 'Eski Kayıt' : 'Yeni Kayıt'"
                                title="Kayıt durumu"
                            />

                            <Tag
                                :severity="patient.notification ? 'info' : 'warn'"
                                :value="
                                    patient.notification ? 'Bildirimler Aktif' : 'Bildirimler Pasif'
                                "
                                title="Bildirim durumu"
                            />
                        </div>
                    </div>
                </template>
            </Card>

            <Card class="col-span-1">
                <template #title>Hasta Detayları</template>
                <template #content>
                    <div class="space-y-2">
                        <div v-for="(field, index) in fields" :key="index">
                            <FieldData :field />
                        </div>
                    </div>
                </template>
            </Card>

            <Card class="col-span-1">
                <template #title>
                    <div class="flex justify-between gap-2">
                        <div>Yapılan İşlemler</div>

                        <div v-if="treatments && treatments.length" class="flex gap-2">
                            <Button
                                class="h-7"
                                icon="pi pi-plus"
                                label="Tümünü genişlet"
                                severity="primary"
                                size="small"
                                @click="expandAll"
                            />

                            <Button
                                class="h-7"
                                icon="pi pi-minus"
                                label="Tümünü daralt"
                                severity="info"
                                size="small"
                                @click="collapseAll"
                            />
                        </div>
                    </div>
                </template>
                <template #content>
                    <DataTable
                        v-if="treatments && treatments.length > 0"
                        v-model:expanded-rows="expandedRows"
                        :value="treatments"
                        class="text-sm"
                        data-key="id"
                        row-hover
                        size="small"
                    >
                        <Column expander style="width: 3rem" />
                        <Column field="id" header="ID" />
                        <Column field="total" header="Tutar">
                            <template #body="slotProps">
                                <span class="font-medium">{{
                                    currencyFormat(slotProps.data.total)
                                }}</span>
                            </template>
                        </Column>
                        <Column field="created_at" header="İşlem Tarihi">
                            <template #body="slotProps">
                                {{ dateTimeFormat(slotProps.data.created_at) }}
                            </template>
                        </Column>

                        <template #expansion="{data}">
                            <div class="grid grid-cols-1 gap-2">
                                <DataTable
                                    v-if="data.services && data.services.length > 0"
                                    :value="data.services"
                                    class="col-span-1"
                                    show-gridlines
                                    size="small"
                                >
                                    <Column field="service.code" header="Kod" />
                                    <Column field="service.name" header="Hizmet" />
                                    <Column field="price" header="Tutar">
                                        <template #body="slotProps">
                                            <span class="font-medium">{{
                                                currencyFormat(slotProps.data.price)
                                            }}</span>
                                        </template>
                                    </Column>
                                </DataTable>

                                <DataTable
                                    v-if="data.products && data.products.length > 0"
                                    :value="data.products"
                                    class="col-span-1"
                                    show-gridlines
                                    size="small"
                                >
                                    <Column field="product.code" header="Kod" />
                                    <Column field="product.name" header="Ürün" />
                                    <Column field="count" header="Adet" />
                                    <Column field="price" header="Fiyat">
                                        <template #body="slotProps">
                                            {{ currencyFormat(slotProps.data.price) }}
                                        </template>
                                    </Column>
                                    <Column field="total" header="Tutar">
                                        <template #body="slotProps">
                                            <span class="font-medium">{{
                                                currencyFormat(slotProps.data.total)
                                            }}</span>
                                        </template>
                                    </Column>
                                </DataTable>

                                <div
                                    v-if="data.note"
                                    class="surface-default col-span-full border p-2"
                                >
                                    <div class="font-medium">Not</div>
                                    <div>{{ data.note }}</div>
                                </div>
                            </div>
                        </template>
                    </DataTable>
                </template>
            </Card>

            <AppointmentTable :appointments class="col-span-1" size="small" title="Randevular" />
        </div>
    </DashboardLayout>
</template>
