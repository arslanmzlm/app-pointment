<script lang="ts" setup>
import {Column, Tag} from 'primevue';
import {ref} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import BaseDataTable from '@/Components/BaseDataTable.vue';
import DeletePopup from '@/Components/DeletePopup.vue';
import EditLink from '@/Components/EditLink.vue';
import InputField from '@/Components/Form/InputField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {dateTimeFormat} from '@/Utilities/formatters';
import {DataTableFilter} from '@/types/component';
import {Content} from '@/types/model';
import {PaginateResponse} from '@/types/response';

defineProps<{
    contents: PaginateResponse<Content>;
    sections: string[];
}>();

const filters = ref<DataTableFilter>({
    search: {value: ''},
    section: {value: null},
    active: {value: null, type: 'boolean'},
});
</script>

<template>
    <DashboardLayout title="İçerikler">
        <BaseDataTable
            :create-url="route('dashboard.content.create')"
            :filters
            :paginate="contents"
            create-label="İçerik Oluştur"
            only="contents"
        >
            <template #filters>
                <InputField
                    v-model.trim="filters.search.value"
                    class="col-span-1"
                    label="Genel arama"
                    size="small"
                />

                <SelectField
                    v-if="sections && sections.length > 0"
                    v-model="filters.category.value"
                    :option-label="null"
                    :option-value="null"
                    :options="sections"
                    class="col-span-1"
                    label="Bölüm"
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
            <Column field="section" header="Bölüm" sortable />
            <Column field="title" header="Başlık" />
            <Column field="active" header="Durum">
                <template #body="slotProps">
                    <Tag
                        :severity="slotProps.data.active ? 'success' : 'danger'"
                        :value="slotProps.data.active ? 'Aktif' : 'Pasif'"
                    />
                </template>
            </Column>
            <Column field="updated_at" header="Son Düzenlenme Tarihi" sortable>
                <template #body="slotProps">
                    {{ dateTimeFormat(slotProps.data.updated_at) }}
                </template>
            </Column>
            <Column header="İşlemler">
                <template #body="slotProps">
                    <div class="table-actions">
                        <DeletePopup
                            :url="route('dashboard.content.destroy', {id: slotProps.data.id})"
                        />

                        <EditLink :url="route('dashboard.content.edit', {id: slotProps.data.id})" />
                    </div>
                </template>
            </Column>
        </BaseDataTable>
    </DashboardLayout>
</template>
