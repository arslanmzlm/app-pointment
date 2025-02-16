<script lang="ts" setup>
import {Column, Tag} from 'primevue';
import {ref} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import BaseDataTable from '@/Components/BaseDataTable.vue';
import DeletePopup from '@/Components/DeletePopup.vue';
import EditLink from '@/Components/EditLink.vue';
import InputField from '@/Components/Form/InputField.vue';
import MultiSelectField from '@/Components/Form/MultiSelectField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {userType} from '@/Utilities/enumHelper';
import {dateTimeFormat} from '@/Utilities/formatters';
import {DataTableFilter} from '@/types/component';
import {Hospital, User} from '@/types/model';
import {EnumResponse, PaginateResponse} from '@/types/response';

const props = defineProps<{
    users: PaginateResponse<User>;
    userTypes: EnumResponse[];
    hospitals?: Hospital[];
}>();

const filters = ref<DataTableFilter>({
    search: {value: ''},
    type: {value: null, type: 'array:number'},
    deleted: {value: null, type: 'number'},
});

if (props.hospitals) {
    filters.value.hospital = {value: null, type: 'number'};
}
</script>

<template>
    <DashboardLayout title="Kullanıcılar">
        <BaseDataTable
            :create-url="route('dashboard.user.create')"
            :filters
            :paginate="users"
            create-label="Kullanıcı Ekle"
            only="users"
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

                <MultiSelectField
                    v-model="filters.type.value"
                    :options="userTypes"
                    class="col-span-1"
                    label="Kullanıcı tipi"
                    multiple
                    option-label="label"
                    option-value="value"
                    show-clear
                    size="small"
                >
                    <template #option="slotProps">
                        <Tag
                            :severity="userType(slotProps.data.option.value).severity"
                            :value="slotProps.data.option.label"
                        />
                    </template>
                </MultiSelectField>

                <SelectField
                    v-model="filters.deleted.value"
                    :options="[
                        {label: 'Silinmiş Kayıtlarıda Listele', value: 1},
                        {label: 'Sadece Silinmiş Kayıtları Listele', value: 2},
                    ]"
                    class="col-span-1"
                    label="Kayıt Durumu"
                    option-label="label"
                    option-value="value"
                    show-clear
                    size="small"
                />
            </template>

            <Column field="id" header="ID" sortable />
            <Column field="type" header="Tip">
                <template #body="slotProps">
                    <Tag
                        :severity="userType(slotProps.data.type).severity"
                        :value="slotProps.data.type_label"
                    />
                </template>
            </Column>
            <Column field="username" header="Kullanıcı Adı" />
            <Column field="name" header="Kullanıcı" />
            <Column field="phone" header="Telefon" />
            <Column field="created_at" header="Kayıt Tarihi">
                <template #body="slotProps">
                    {{ dateTimeFormat(slotProps.data.created_at) }}
                </template>
            </Column>
            <Column header="İşlemler">
                <template #body="slotProps">
                    <div v-if="slotProps.data.deleted_at === null" class="table-actions">
                        <DeletePopup
                            :url="route('dashboard.user.destroy', {id: slotProps.data.id})"
                        />

                        <EditLink :url="route('dashboard.user.edit', {id: slotProps.data.id})" />
                    </div>
                </template>
            </Column>
        </BaseDataTable>
    </DashboardLayout>
</template>
