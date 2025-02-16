<script lang="ts" setup>
import {Column} from 'primevue';
import {computed, ref} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import BaseDataTable from '@/Components/BaseDataTable.vue';
import EditLink from '@/Components/EditLink.vue';
import InputField from '@/Components/Form/InputField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {DataTableFilter} from '@/types/component';
import {Hospital, Province} from '@/types/model';
import {PaginateResponse} from '@/types/response';

const props = defineProps<{
    hospitals: PaginateResponse<Hospital>;
    provinces: Province[];
}>();

const provinceNames = computed(() => {
    const data: {[key: number]: string} = {};

    props.provinces.forEach((province) => {
        data[province.id] = province.name;
    });

    return data;
});

const filters = ref<DataTableFilter>({
    search: {value: ''},
    province: {value: null, type: 'number'},
});
</script>

<template>
    <DashboardLayout title="Hastaneler">
        <BaseDataTable :filters :paginate="hospitals" only="hospitals">
            <template #filters>
                <InputField
                    v-model.trim="filters.search.value"
                    class="col-span-1"
                    label="Genel arama"
                    size="small"
                />

                <SelectField
                    v-model="filters.province.value"
                    :options="provinces"
                    class="col-span-1"
                    label="Şehir"
                    show-clear
                    size="small"
                />
            </template>

            <Column field="id" header="ID" sortable />
            <Column field="name" header="Hastane" sortable />
            <Column field="province" header="Şehir">
                <template #body="slotProps">
                    {{ provinceNames[slotProps.data.province_id] }}
                </template>
            </Column>
            <Column header="İşlemler">
                <template #body="slotProps">
                    <EditLink :url="route('dashboard.hospital.edit', {id: slotProps.data.id})" />
                </template>
            </Column>
        </BaseDataTable>
    </DashboardLayout>
</template>
