<script lang="ts" setup>
import {Button, Column, DataTable} from 'primevue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import BaseDataTable from '@/Components/BaseDataTable.vue';
import DeletePopup from '@/Components/DeletePopup.vue';
import EditLink from '@/Components/EditLink.vue';
import {currencyFormat, dateTimeFormat} from '@/Utilities/formatters';
import {Treatment} from '@/types/model';
import {PaginateResponse} from '@/types/response';

defineProps<{
    treatments: PaginateResponse<Treatment>;
}>();
</script>

<template>
    <DashboardLayout title="İşlemler">
        <BaseDataTable
            :create-url="route('dashboard.treatment.create')"
            :paginate="treatments"
            create-label="İşlem Ekle"
            only="treatments"
        >
            <Column expander style="width: 5rem" />
            <Column field="id" header="ID" sortable />
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
                    <div class="col-span-full">
                        <div class="font-medium">Toplam Tutar</div>
                        <div>{{ currencyFormat(data.total) }}</div>
                    </div>

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

                    <div v-if="data.note" class="col-span-full">
                        <div class="font-medium">Not</div>
                        <div>{{ data.note }}</div>
                    </div>
                </div>
            </template>
        </BaseDataTable>
    </DashboardLayout>
</template>
