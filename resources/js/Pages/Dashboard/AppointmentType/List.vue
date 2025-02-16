<script lang="ts" setup>
import {Column} from 'primevue';
import {ref} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import BaseDataTable from '@/Components/BaseDataTable.vue';
import EditLink from '@/Components/EditLink.vue';
import InputField from '@/Components/Form/InputField.vue';
import {DataTableFilter} from '@/types/component';
import {AppointmentType} from '@/types/model';
import {PaginateResponse} from '@/types/response';

defineProps<{
    appointmentTypes: PaginateResponse<AppointmentType>;
}>();

const filters = ref<DataTableFilter>({
    search: {value: ''},
});
</script>

<template>
    <DashboardLayout title="Randevu Tipleri">
        <BaseDataTable
            :create-url="route('dashboard.appointment.type.create')"
            :filters
            :paginate="appointmentTypes"
            create-label="Randevu Tipi Ekle"
            only="appointmentTypes"
        >
            <template #filters>
                <InputField
                    v-model.trim="filters.search.value"
                    class="col-span-1"
                    label="Genel arama"
                    size="small"
                />
            </template>

            <Column field="id" header="ID" sortable />
            <Column field="name" header="Randevu Tipi" />
            <Column header="İşlemler">
                <template #body="slotProps">
                    <EditLink
                        :url="route('dashboard.appointment.type.edit', {id: slotProps.data.id})"
                    />
                </template>
            </Column>
        </BaseDataTable>
    </DashboardLayout>
</template>
