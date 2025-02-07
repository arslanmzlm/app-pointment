<script lang="ts" setup>
import {Column, Tag} from 'primevue';
import {computed, ref, watch} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PatientSelector from '@/Forms/Parts/PatientSelector.vue';
import BaseDataTable from '@/Components/BaseDataTable.vue';
import EditLink from '@/Components/EditLink.vue';
import DateRangeField from '@/Components/Form/DateRangeField.vue';
import InputField from '@/Components/Form/InputField.vue';
import MultiSelectField from '@/Components/Form/MultiSelectField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {appointmentState} from '@/Utilities/enumHelper';
import {dateTimeFormat} from '@/Utilities/formatters';
import {DataTableFilter} from '@/types/component';
import {Appointment, Doctor, Hospital} from '@/types/model';
import {EnumResponse, PaginateResponse} from '@/types/response';

const props = defineProps<{
    appointments: PaginateResponse<Appointment>;
    appointmentStates: EnumResponse[];
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
    <DashboardLayout title="Randevular">
        <BaseDataTable
            :create-url="route('dashboard.appointment.create')"
            :filters
            :paginate="appointments"
            create-label="Randevu Ekle"
            only="appointments"
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
                    :default-data="appointments.data[0]?.patient ?? undefined"
                    show-clear
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
                    label="Doktor"
                    option-label="full_name"
                    placeholder="Önce hastane seçiniz"
                    show-clear
                    size="small"
                />

                <MultiSelectField
                    v-model="filters.state.value"
                    :options="appointmentStates"
                    label="Statü"
                    multiple
                    option-label="label"
                    option-value="value"
                    show-clear
                    size="small"
                >
                    <template #option="slotProps">
                        <Tag
                            :icon="appointmentState(slotProps.data.option.value).icon"
                            :severity="appointmentState(slotProps.data.option.value).severity"
                            :value="slotProps.data.option.label"
                        />
                    </template>
                </MultiSelectField>
            </template>

            <Column field="id" header="ID" sortable />
            <Column field="state" header="Durum">
                <template #body="slotProps">
                    <Tag
                        :icon="appointmentState(slotProps.data.state).icon"
                        :severity="appointmentState(slotProps.data.state).severity"
                        :value="slotProps.data.state_label"
                    />
                </template>
            </Column>
            <Column v-if="hospitals" field="hospital" header="Hastane">
                <template #body="slotProps">
                    {{ hospitalNames[slotProps.data.hospital_id] }}
                </template>
            </Column>
            <Column v-if="doctors" field="doctor" header="Doktor">
                <template #body="slotProps">
                    {{ doctorNames[slotProps.data.doctor_id] }}
                </template>
            </Column>
            <Column field="patient.full_name" header="Hasta" />
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
            <Column field="duration" header="Seans Süresi (dakika)" sortable />
            <Column field="updated_at" header="Son Düzenlenme Tarihi">
                <template #body="slotProps">
                    {{ dateTimeFormat(slotProps.data.updated_at) }}
                </template>
            </Column>
            <Column header="İşlemler">
                <template #body="slotProps">
                    <EditLink :url="route('dashboard.appointment.edit', {id: slotProps.data.id})" />
                </template>
            </Column>
        </BaseDataTable>
    </DashboardLayout>
</template>
