<script lang="ts" setup>
import {Link, router} from '@inertiajs/vue3';
import {Button, Column, Tag, useConfirm} from 'primevue';
import {computed, ref, watch} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PatientSelector from '@/Forms/Parts/PatientSelector.vue';
import BaseDataTable from '@/Components/BaseDataTable.vue';
import DeletePopup from '@/Components/DeletePopup.vue';
import EditLink from '@/Components/EditLink.vue';
import DateRangeField from '@/Components/Form/DateRangeField.vue';
import InputField from '@/Components/Form/InputField.vue';
import MultiSelectField from '@/Components/Form/MultiSelectField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {appointmentState} from '@/Utilities/enumHelper';
import {dateFormat, dateTimeFormat, timeFormat} from '@/Utilities/formatters';
import {appointmentActive} from '@/Utilities/modelHelper';
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

const confirm = useConfirm();

function showConfirm(event: Event, url: string) {
    confirm.require({
        target: <HTMLElement>event.currentTarget,
        message: 'Randevuyu iptal etmek istediğinizden emin misiniz?',
        icon: 'pi pi-exclamation-triangle',
        group: 'popup',
        rejectProps: {
            label: 'Kapat',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'İptal Et',
            severity: 'warn',
        },
        accept: () => {
            router.post(url);
        },
    });
}
</script>

<template>
    <DashboardLayout title="Randevular">
        <BaseDataTable
            :create-url="route('dashboard.appointment.create')"
            :filters
            :paginate="appointments"
            create-label="Randevu Oluştur"
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
                    :placeholder="
                        hospitals && hospitals.length > 0 ? 'Önce hastane seçiniz' : undefined
                    "
                    class="col-span-1"
                    label="Podolog"
                    option-label="full_name"
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
            <Column v-if="hospitals || doctors" header="Hastane / Podolog">
                <template #body="slotProps">
                    <div>{{ hospitalNames[slotProps.data.hospital_id] }}</div>
                    <div class="font-semibold">{{ doctorNames[slotProps.data.doctor_id] }}</div>
                </template>
            </Column>
            <Column field="patient.full_name" header="Hasta">
                <template #body="slotProps">
                    <Link
                        :href="route('dashboard.patient.show', {id: slotProps.data.patient_id})"
                        class="text-primary underline hover:text-primary-700"
                    >
                        {{ slotProps.data.patient.full_name }}
                    </Link>
                </template>
            </Column>
            <Column field="type_name" header="Randevu Tipi">
                <template #body="slotProps">
                    <div>{{ slotProps.data.type_name }}</div>
                    <div v-if="slotProps.data.service">{{ slotProps.data.service.name }}</div>
                </template>
            </Column>
            <Column field="state" header="Durum">
                <template #body="slotProps">
                    <Tag
                        :icon="appointmentState(slotProps.data.state).icon"
                        :severity="appointmentState(slotProps.data.state).severity"
                        :value="slotProps.data.state_label"
                    />
                </template>
            </Column>
            <Column field="start_date" header="Tarihi" sortable>
                <template #body="slotProps">
                    <div class="whitespace-nowrap">
                        <div>{{ dateFormat(slotProps.data.start_date) }}</div>
                        <div>
                            {{ timeFormat(slotProps.data.start_date) }} -
                            {{ timeFormat(slotProps.data.due_date) }}
                        </div>
                        <div>{{ slotProps.data.duration }} dk</div>
                    </div>
                </template>
            </Column>
            <Column field="note" header="Not">
                <template #body="slotProps">
                    <span
                        v-if="slotProps.data.note"
                        v-tooltip.bottom="
                            slotProps.data.note.length > 10 ? slotProps.data.note : undefined
                        "
                        >{{
                            slotProps.data.note.length > 10
                                ? slotProps.data.note.substring(0, 10) + '...'
                                : slotProps.data.note
                        }}</span
                    >
                </template>
            </Column>
            <Column field="updated_at" header="Son Düzenlenme Tarihi">
                <template #body="slotProps">
                    {{ dateTimeFormat(slotProps.data.updated_at) }}
                </template>
            </Column>
            <Column header="İşlemler">
                <template #body="slotProps">
                    <div class="table-actions">
                        <DeletePopup
                            :url="route('dashboard.appointment.destroy', {id: slotProps.data.id})"
                        />

                        <Button
                            v-if="appointmentActive(slotProps.data)"
                            class="size-12"
                            icon="pi pi-times"
                            severity="warn"
                            title="İptal"
                            @click="
                                showConfirm(
                                    $event,
                                    route('dashboard.appointment.cancel', {id: slotProps.data.id}),
                                )
                            "
                        />

                        <EditLink
                            :url="route('dashboard.appointment.edit', {id: slotProps.data.id})"
                        />

                        <Button
                            v-if="appointmentActive(slotProps.data)"
                            :href="route('dashboard.appointment.process', {id: slotProps.data.id})"
                            as="Link"
                            class="size-12"
                            icon="pi pi-play"
                            severity="info"
                            title="İşleme Al"
                        />
                    </div>
                </template>
            </Column>
        </BaseDataTable>
    </DashboardLayout>
</template>
