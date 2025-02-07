<script lang="ts" setup>
import {Column} from 'primevue';
import {computed, ref} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import BaseDataTable from '@/Components/BaseDataTable.vue';
import EditLink from '@/Components/EditLink.vue';
import InputField from '@/Components/Form/InputField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {DataTableFilter} from '@/types/component';
import {Doctor, Hospital} from '@/types/model';
import {PaginateResponse} from '@/types/response';

const props = defineProps<{
    doctors: PaginateResponse<Doctor>;
    hospitals?: Hospital[];
    branches: string[];
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

const filters = ref<DataTableFilter>({
    search: {value: ''},
    branch: {value: null},
});

if (props.hospitals) {
    filters.value.hospital = {value: null, type: 'number'};
}
</script>

<template>
    <DashboardLayout title="Doktorlar">
        <BaseDataTable
            :create-url="route('dashboard.doctor.create')"
            :filters
            :paginate="doctors"
            create-label="Doktor Ekle"
            only="doctors"
        >
            <template #filters>
                <InputField
                    v-model.trim="filters.search.value"
                    class="col-span-1"
                    label="Genel arama"
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
                    v-if="branches && branches.length > 0"
                    v-model="filters.branch.value"
                    :option-label="null"
                    :option-value="null"
                    :options="branches"
                    class="col-span-1"
                    label="Alan"
                    show-clear
                    size="small"
                />
            </template>

            <Column field="id" header="ID" sortable />
            <Column field="full_name" header="Doktor" sortable />
            <Column v-if="hospitals" field="hospital" header="Hastane">
                <template #body="slotProps">
                    {{ hospitalNames[slotProps.data.hospital_id] }}
                </template>
            </Column>
            <Column field="branch" header="Alan" />
            <Column field="title" header="Ünvan" />
            <Column field="phone" header="Telefon" />
            <Column header="İşlemler">
                <template #body="slotProps">
                    <EditLink :url="route('dashboard.doctor.edit', {id: slotProps.data.id})" />
                </template>
            </Column>
        </BaseDataTable>
    </DashboardLayout>
</template>
