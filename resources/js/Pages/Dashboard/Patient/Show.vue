<script lang="ts" setup>
import {Button, Card, Column, DataTable} from 'primevue';
import {ref} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import FieldData from '@/Components/Field/FieldData.vue';
import AppointmentTable from '@/Components/Tables/AppointmentTable.vue';
import BirthdayTag from '@/Components/Tags/BirthdayTag.vue';
import EmailTag from '@/Components/Tags/EmailTag.vue';
import GenderTag from '@/Components/Tags/GenderTag.vue';
import NotificationTag from '@/Components/Tags/NotificationTag.vue';
import OldTag from '@/Components/Tags/OldTag.vue';
import PhoneTag from '@/Components/Tags/PhoneTag.vue';
import ProvinceTag from '@/Components/Tags/ProvinceTag.vue';
import StoreTypeTag from '@/Components/Tags/StoreTypeTag.vue';
import TimestampTag from '@/Components/Tags/TimestampTag.vue';
import {currencyFormat, dateTimeFormat} from '@/Utilities/formatters';
import {Appointment, Patient, Province, Treatment} from '@/types/model';
import {PatientFieldResponse} from '@/types/response';

const props = defineProps<{
    patient: Patient;
    province: Province | null;
    fields: PatientFieldResponse[];
    treatments: Treatment[];
    appointments: Appointment[];
}>();

const breadcrumbs = [{label: 'Hastalar', url: route('dashboard.patient.list')}];

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
    <DashboardLayout :breadcrumbs title="Hasta Detayları">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="col-span-full flex flex-wrap justify-end gap-2">
                <a :href="route('dashboard.patient.print', {id: patient.id})" target="_blank">
                    <Button
                        icon="pi pi-print"
                        label="Hasta Kayıt Çıktısı"
                        severity="warn"
                        title="Görüntüle"
                    />
                </a>

                <Button
                    :href="route('dashboard.patient.edit', {id: patient.id})"
                    as="Link"
                    icon="pi pi-pen-to-square"
                    label="Hasta Bilgilerini Düzenle"
                    title="Düzenle"
                />
            </div>
            <Card class="col-span-1">
                <template #title>{{ patient.full_name }}</template>
                <template #content>
                    <div class="space-y-2">
                        <div class="flex flex-col items-start gap-2">
                            <ProvinceTag v-if="province" :value="province.name" />
                            <PhoneTag :value="patient.phone" />
                            <EmailTag v-if="patient.email" :value="patient.email" />
                            <GenderTag :label="patient.gender_label" :value="patient.gender" />
                            <OldTag :value="patient.old" />
                            <NotificationTag :value="patient.notification" />
                            <StoreTypeTag :value="patient.created_by" />
                        </div>
                        <div v-if="patient.birthday" class="flex items-center gap-1">
                            <span class="font-semibold">Doğum Tarihi:</span>
                            <BirthdayTag :value="patient.birthday" />
                        </div>
                        <div class="flex gap-1">
                            <span class="font-semibold">Kayıt Tarihi:</span>
                            <TimestampTag :value="patient.created_at" />
                        </div>
                        <div class="flex gap-1">
                            <span class="font-semibold">Son Düzenlenme Tarihi:</span>
                            <TimestampTag :value="patient.updated_at" type="updated_at" />
                        </div>
                    </div>
                </template>
            </Card>

            <Card v-if="fields && fields.length > 0" class="col-span-1">
                <template #title>Hasta Ek Bilgiler</template>
                <template #content>
                    <div class="space-y-2">
                        <div v-for="(field, index) in fields" :key="index">
                            <FieldData :field />
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <Card v-if="treatments && treatments.length > 0">
            <template #title>
                <div class="flex justify-between gap-2">
                    <div>Yapılan İşlemler</div>
                    <div class="flex gap-2">
                        <Button
                            class="h-7"
                            icon="pi pi-plus"
                            severity="primary"
                            size="small"
                            @click="expandAll"
                        />

                        <Button
                            class="h-7"
                            icon="pi pi-minus"
                            severity="info"
                            size="small"
                            @click="collapseAll"
                        />
                    </div>
                </div>
            </template>
            <template #content>
                <DataTable
                    v-model:expanded-rows="expandedRows"
                    :value="treatments"
                    class="text-sm"
                    data-key="id"
                    row-hover
                    size="small"
                    striped-rows
                >
                    <Column expander style="width: 3rem" />
                    <Column field="id" header="ID" />
                    <Column field="doctor.full_name" header="Podolog" />
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

                            <div v-if="data.note" class="surface-default col-span-full">
                                <div class="font-medium">Not</div>
                                <div>{{ data.note }}</div>
                            </div>
                        </div>
                    </template>
                </DataTable>
            </template>
        </Card>

        <AppointmentTable :appointments size="small" title="Randevular" />
    </DashboardLayout>
</template>
