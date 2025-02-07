<script lang="ts" setup>
import {Button, Column, Tag} from 'primevue';
import {computed, ref} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import BaseDataTable from '@/Components/BaseDataTable.vue';
import EditLink from '@/Components/EditLink.vue';
import DateField from '@/Components/Form/DateField.vue';
import InputField from '@/Components/Form/InputField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {dateFormat, dateTimeFormat} from '@/Utilities/formatters';
import {DataTableFilter} from '@/types/component';
import {Patient, Province} from '@/types/model';
import {EnumResponse, PaginateResponse} from '@/types/response';

const props = defineProps<{
    patients: PaginateResponse<Patient>;
    provinces: Province[];
    genders: EnumResponse[];
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
    old: {value: null, type: 'boolean'},
    gender: {value: null, type: 'number'},
    birthday: {value: null, type: 'date'},
    notification: {value: null, type: 'boolean'},
});
</script>

<template>
    <DashboardLayout title="Hastalar">
        <BaseDataTable
            :create-url="route('dashboard.patient.create')"
            :filters
            :paginate="patients"
            create-label="Hasta Ekle"
            only="patients"
        >
            <template #filters>
                <InputField
                    v-model.trim="filters.search.value"
                    class="col-span-1"
                    label="Genel arama"
                    size="small"
                />

                <SelectField
                    v-model.number="filters.province.value"
                    :options="provinces"
                    class="col-span-1"
                    label="Şehir"
                    show-clear
                    size="small"
                />

                <SelectField
                    v-model="filters.old.value"
                    :options="[
                        {label: 'Eski Kayıtları Listele', value: true},
                        {label: 'Yeni Kayıtları Listele', value: false},
                    ]"
                    class="col-span-1"
                    label="Kayıt Durumu"
                    option-label="label"
                    option-value="value"
                    show-clear
                    size="small"
                />

                <SelectField
                    v-model.number="filters.gender.value"
                    :options="genders"
                    class="col-span-1"
                    label="Cinsiyet"
                    option-label="label"
                    option-value="value"
                    show-clear
                    size="small"
                />

                <DateField
                    v-model="filters.birthday.value"
                    class="col-span-1"
                    date-format="d MM yy"
                    label="Doğum Tarihi"
                    size="small"
                />

                <SelectField
                    v-model="filters.notification.value"
                    :options="[
                        {label: 'Bildirimi Aktif Olanları Listele', value: true},
                        {label: 'Bildirimi Aktif Olmayanları Listele', value: false},
                    ]"
                    class="col-span-1"
                    label="Bildirim Durumu"
                    option-label="label"
                    option-value="value"
                    show-clear
                    size="small"
                />
            </template>

            <Column expander style="width: 3rem" />
            <Column field="id" header="ID" sortable />
            <Column field="full_name" header="Hasta" />
            <Column field="phone" header="Telefon" />
            <Column header="Şehir">
                <template #body="slotProps">
                    {{ provinceNames[slotProps.data.province_id] }}
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
                        <EditLink :url="route('dashboard.patient.edit', {id: slotProps.data.id})" />

                        <Button
                            :href="route('dashboard.patient.show', {id: slotProps.data.id})"
                            as="Link"
                            class="size-12"
                            icon="pi pi-address-book"
                            severity="info"
                            title="Görüntüle"
                        />
                    </div>
                </template>
            </Column>

            <template #expansion="{data}">
                <div class="flex items-center gap-4 p-4">
                    <Tag
                        v-if="data.gender"
                        :severity="data.gender == 1 ? 'info' : 'danger'"
                        :value="data.gender_label"
                        title="Cinsiyet"
                    />
                    <Tag
                        :severity="data.old ? 'secondary' : 'primary'"
                        :value="data.old ? 'Eski Kayıt' : 'Yeni Kayıt'"
                        title="Kayıt durumu"
                    />
                    <Tag
                        :severity="data.notification ? 'info' : 'warn'"
                        :value="data.notification ? 'Bildirimler Aktif' : 'Bildirimler Pasif'"
                        title="Bildirim durumu"
                    />
                    <Tag
                        v-if="data.birthday"
                        :value="dateFormat(data.birthday)"
                        severity="primary"
                        title="Doğum tarihi"
                    />
                    <Tag
                        v-if="data.email"
                        :value="data.email"
                        severity="warn"
                        title="E-posta adresi"
                    />
                    <Tag
                        :value="dateTimeFormat(data.created_at)"
                        severity="danger"
                        title="Kayıt tarihi"
                    />
                </div>
            </template>
        </BaseDataTable>
    </DashboardLayout>
</template>
