<script lang="ts" setup>
import {Column, Tag} from 'primevue';
import {computed, ref} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import BaseDataTable from '@/Components/BaseDataTable.vue';
import EditLink from '@/Components/EditLink.vue';
import InputField from '@/Components/Form/InputField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {currencyFormat, dateTimeFormat} from '@/Utilities/formatters';
import {DataTableFilter} from '@/types/component';
import {Hospital, Product} from '@/types/model';
import {PaginateResponse} from '@/types/response';

const props = defineProps<{
    products: PaginateResponse<Product>;
    categories: string[];
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
    category: {value: null},
    active: {value: null, type: 'boolean'},
});

if (props.hospitals) {
    filters.value.hospital = {value: null, type: 'number'};
}
</script>

<template>
    <DashboardLayout title="Ürünler">
        <BaseDataTable
            :create-url="route('dashboard.product.create')"
            :filters
            :paginate="products"
            create-label="Ürün Ekle"
            only="products"
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
                    v-if="categories && categories.length > 0"
                    v-model="filters.category.value"
                    :option-label="null"
                    :option-value="null"
                    :options="categories"
                    class="col-span-1"
                    label="Kategori"
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
            <Column field="category" header="Kategori" sortable />
            <Column field="code" header="Ürün Kodu" sortable />
            <Column field="name" header="Ürün" sortable />
            <Column field="active" header="Durum">
                <template #body="slotProps">
                    <Tag
                        :severity="slotProps.data.active ? 'success' : 'danger'"
                        :value="slotProps.data.active ? 'Aktif' : 'Pasif'"
                    />
                </template>
            </Column>
            <Column field="stock" header="Stok" sortable />
            <Column field="price" header="Fiyat" sortable>
                <template #body="slotProps">
                    {{ currencyFormat(slotProps.data.price) }}
                </template>
            </Column>
            <Column field="updated_at" header="Son Düzenlenme Tarihi" sortable>
                <template #body="slotProps">
                    {{ dateTimeFormat(slotProps.data.updated_at) }}
                </template>
            </Column>
            <Column header="İşlemler">
                <template #body="slotProps">
                    <EditLink :url="route('dashboard.product.edit', {id: slotProps.data.id})" />
                </template>
            </Column>
        </BaseDataTable>
    </DashboardLayout>
</template>
