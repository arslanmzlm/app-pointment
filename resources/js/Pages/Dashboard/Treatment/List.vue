<script lang="ts" setup>
import {Button, Column, DataTable} from 'primevue';
import {computed, ref, watch} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PatientSelector from '@/Forms/Parts/PatientSelector.vue';
import BaseDataTable from '@/Components/BaseDataTable.vue';
import DeletePopup from '@/Components/DeletePopup.vue';
import EditLink from '@/Components/EditLink.vue';
import InputField from '@/Components/Form/InputField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {currencyFormat, dateTimeFormat} from '@/Utilities/formatters';
import {DataTableFilter} from '@/types/component';
import {Doctor, Hospital, Treatment} from '@/types/model';
import {PaginateResponse} from '@/types/response';

const props = defineProps<{
    treatments: PaginateResponse<Treatment>;
    hospitals?: Hospital[];
    doctors?: Doctor[];
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

const doctorNames = computed(() => {
    const data: {[key: number]: string} = {};

    if (props.doctors) {
        props.doctors.forEach((doctor) => {
            data[doctor.id] = doctor.full_name;
        });
    }

    return data;
});

const doctorOptions = computed(() => {
    if (props.doctors) {
        if (props.hospitals === undefined) {
            return props.doctors;
        }

        const hospitalId = filters.value.hospital.value;
        return props.doctors.filter((doctor) => {
            return doctor.hospital_id === hospitalId;
        });
    }

    return [];
});

const filters = ref<DataTableFilter>({
    search: {value: ''},
    patient: {value: null, type: 'number'},
});

if (props.hospitals) {
    filters.value.hospital = {value: null, type: 'number'};

    watch(
        () => filters.value.hospital.value,
        () => {
            filters.value.doctor.value = null;
        },
    );
}

if (props.doctors) {
    filters.value.doctor = {value: null, type: 'number'};
}
</script>

<template>
    <DashboardLayout title="İşlemler">
        <BaseDataTable
            :create-url="route('dashboard.treatment.create')"
            :filters
            :paginate="treatments"
            create-label="İşlem Ekle"
            only="treatments"
        >
            <template #filters>
                <InputField
                    v-model.trim="filters.search.value"
                    class="col-span-1"
                    label="Genel arama"
                    size="small"
                />

                <PatientSelector
                    v-model.number="filters.patient.value"
                    :default-data="treatments.data[0]?.patient ?? undefined"
                    show-clear
                    size="small"
                />

                <SelectField
                    v-if="filters.hospital && hospitals && hospitals.length > 0"
                    v-model.number="filters.hospital.value"
                    :options="hospitals"
                    class="col-span-1"
                    label="Hastane"
                    show-clear
                    size="small"
                />

                <SelectField
                    v-if="filters.doctor && doctors && doctors.length > 0"
                    v-model.number="filters.doctor.value"
                    :options="doctorOptions"
                    :placeholder="
                        hospitals && hospitals.length > 0 ? 'Önce hastane seçiniz' : undefined
                    "
                    class="col-span-1"
                    label="Podolog"
                    option-label="full_name"
                    show-clear
                    size="small"
                />
            </template>

            <Column expander style="width: 5rem" />
            <Column field="id" header="ID" sortable />
            <Column v-if="hospitals" field="hospital" header="Hastane">
                <template #body="slotProps">
                    {{ hospitalNames[slotProps.data.hospital_id] }}
                </template>
            </Column>
            <Column v-if="doctors" field="doctor" header="Podolog">
                <template #body="slotProps">
                    {{ doctorNames[slotProps.data.doctor_id] }}
                </template>
            </Column>
            <Column field="patient.full_name" header="Hasta">
                <template #body="slotProps">
                    <Button
                        :href="route('dashboard.patient.show', {id: slotProps.data.patient.id})"
                        :label="slotProps.data.patient.full_name"
                        as="Link"
                        link
                    />
                </template>
            </Column>
            <Column field="total" header="Toplam Tutar">
                <template #body="slotProps">
                    {{ currencyFormat(slotProps.data.total) }}
                </template>
            </Column>
            <Column field="created_at" header="İşlem Tarihi">
                <template #body="slotProps">
                    {{ dateTimeFormat(slotProps.data.created_at) }}
                </template>
            </Column>
            <Column header="İşlemler">
                <template #body="slotProps">
                    <div class="table-actions">
                        <DeletePopup
                            :url="route('dashboard.treatment.destroy', {id: slotProps.data.id})"
                        />

                        <EditLink
                            :url="route('dashboard.treatment.edit', {id: slotProps.data.id})"
                        />
                    </div>
                </template>
            </Column>

            <template #expansion="{data}">
                <div class="grid grid-cols-1 gap-4 text-sm lg:grid-cols-2">
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
        </BaseDataTable>
    </DashboardLayout>
</template>
