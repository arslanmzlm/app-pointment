<script lang="ts" setup>
import {Column, Message} from 'primevue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import BaseDataTable from '@/Components/BaseDataTable.vue';
import EditLink from '@/Components/EditLink.vue';
import {Field} from '@/types/model';
import {PaginateResponse} from '@/types/response';

defineProps<{
    fields: PaginateResponse<Field>;
}>();
</script>

<template>
    <DashboardLayout title="Alanlar">
        <Message severity="info">
            Hasta kayıt sırasında tutulabilecek ek bilgileri belirlemek için bu alanı
            kullanabilirsiniz.
        </Message>

        <BaseDataTable
            :create-url="route('dashboard.field.create')"
            :paginate="fields"
            create-label="Alan Ekle"
            only="fields"
        >
            <Column field="id" header="ID" sortable />
            <Column field="name" header="Alan" />
            <Column field="order" header="Sıra" sortable />
            <Column header="İşlemler">
                <template #body="slotProps">
                    <EditLink :url="route('dashboard.field.edit', {id: slotProps.data.id})" />
                </template>
            </Column>
        </BaseDataTable>
    </DashboardLayout>
</template>
