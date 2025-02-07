<script lang="ts" setup>
import {Column, Tag} from 'primevue';
import {computed, ref} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import BaseDataTable from '@/Components/BaseDataTable.vue';
import EditLink from '@/Components/EditLink.vue';
import InputField from '@/Components/Form/InputField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {currencyFormat} from '@/Utilities/formatters';
import {DataTableFilter} from '@/types/component';
import {Hospital, Service} from '@/types/model';
import {PaginateResponse} from '@/types/response';

const props = defineProps<{
    services: PaginateResponse<Service>;
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

const filters = ref<DataTableFilter>({
    search: {value: ''},
    active: {value: null, type: 'boolean'},
});

if (props.hospitals) {
    filters.value.hospital = {value: null, type: 'number'};
}
</script>

<template>
    <DashboardLayout title="Hizmetler">
        <BaseDataTable
            :create-url="route('dashboard.service.create')"
            :filters
            :paginate="services"
            create-label="Hizmet Ekle"
            only="services"
        >
            <template #filters>
                <InputField
                    v-model.trim="filters.search.value"
                    class="col-span-1"
                    label="Genel arama"
                    size="small"
                />

                <SelectField
                    v-if="hospitals && hospitals.length > 0 && filters.hospital"
                    v-model.number="filters.hospital.value"
                    :options="hospitals"
                    class="col-span-1"
                    label="Hastane"
                    show-clear
                    size="small"
                />

                <SelectField
                    v-model="filters.active.value"
                    :options="[
                        {label: 'Aktif', value: true},
                        {label: 'Pasif', value: false},
                    ]"
                    class="col-span-1"
                    label="Durum"
                    option-label="label"
                    option-value="value"
                    show-clear
                    size="small"
                >
                    <template #option="slotProps">
                        <Tag
                            :severity="slotProps.data.option.value ? 'success' : 'danger'"
                            :value="slotProps.data.option.label"
                        />
                    </template>
                </SelectField>
            </template>

            <Column field="id" header="ID" sortable />
            <Column v-if="hospitals" field="hospital" header="Hastane">
                <template #body="slotProps">
                    {{ hospitalNames[slotProps.data.hospital_id] }}
                </template>
            </Column>
            <Column field="code" header="Hizmet Kodu" />
            <Column field="name" header="Hizmet" />
            <Column field="active" header="Durum">
                <template #body="slotProps">
                    <Tag
                        :severity="slotProps.data.active ? 'success' : 'danger'"
                        :value="slotProps.data.active ? 'Aktif' : 'Pasif'"
                    />
                </template>
            </Column>
            <Column field="price" header="Fiyat">
                <template #body="slotProps">
                    <div v-if="slotProps.data.price">
                        {{ currencyFormat(slotProps.data.price) }}
                    </div>
                </template>
            </Column>
            <Column header="İşlemler">
                <template #body="slotProps">
                    <EditLink :url="route('dashboard.service.edit', {id: slotProps.data.id})" />
                </template>
            </Column>
        </BaseDataTable>
    </DashboardLayout>
</template>
