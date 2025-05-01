<script lang="ts" setup>
import {Column} from 'primevue';
import {computed, ref, watch} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import BaseDataTable from '@/Components/BaseDataTable.vue';
import DeletePopup from '@/Components/DeletePopup.vue';
import EditLink from '@/Components/EditLink.vue';
import DateRangeField from '@/Components/Form/DateRangeField.vue';
import InputField from '@/Components/Form/InputField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {dateTimeFormat} from '@/Utilities/formatters';
import {DataTableFilter} from '@/types/component';
import {Doctor, Hospital, PassiveDate} from '@/types/model';
import {PaginateResponse} from '@/types/response';

const props = defineProps<{
    passiveDates: PaginateResponse<PassiveDate>;
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
    state: {value: null, type: 'array:number'},
    start_date: {value: null, type: 'date'},
    due_date: {value: null, type: 'date'},
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
    <DashboardLayout title="İzinler">
        <BaseDataTable
            :create-url="route('dashboard.passive.date.create')"
            :filters
            :paginate="passiveDates"
            create-label="İzin Oluştur"
            only="passiveDates"
        >
            <template #filters>
                <InputField
                    v-model.trim="filters.search.value"
                    class="col-span-1"
                    label="Genel arama"
                    size="small"
                />

                <DateRangeField
                    v-model:due-date="filters.due_date.value"
                    v-model:start-date="filters.start_date.value"
                    class="col-span-2"
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
                    class="col-span-1"
                    label="Podolog"
                    option-label="full_name"
                    show-clear
                    size="small"
                />
            </template>

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
            <Column field="start_date" header="Başlangıç Tarihi" sortable>
                <template #body="slotProps">
                    {{ dateTimeFormat(slotProps.data.start_date) }}
                </template>
            </Column>
            <Column field="due_date" header="Bitiş Tarihi" sortable>
                <template #body="slotProps">
                    {{ dateTimeFormat(slotProps.data.due_date) }}
                </template>
            </Column>
            <Column field="description" header="Açıklama" />
            <Column field="created_at" header="Kayıt Tarihi" sortable>
                <template #body="slotProps">
                    {{ dateTimeFormat(slotProps.data.created_at) }}
                </template>
            </Column>
            <Column header="İşlemler">
                <template #body="slotProps">
                    <div class="table-actions">
                        <DeletePopup
                            :url="route('dashboard.passive.date.destroy', {id: slotProps.data.id})"
                        />

                        <EditLink
                            :url="route('dashboard.passive.date.edit', {id: slotProps.data.id})"
                        />
                    </div>
                </template>
            </Column>
        </BaseDataTable>
    </DashboardLayout>
</template>
