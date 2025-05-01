<script lang="ts" setup>
import {Column, Tag} from 'primevue';
import {computed, ref} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PatientSelector from '@/Forms/Parts/PatientSelector.vue';
import BaseDataTable from '@/Components/BaseDataTable.vue';
import DeletePopup from '@/Components/DeletePopup.vue';
import EditLink from '@/Components/EditLink.vue';
import DateField from '@/Components/Form/DateField.vue';
import InputField from '@/Components/Form/InputField.vue';
import MultiSelectField from '@/Components/Form/MultiSelectField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {paymentMethod, transactionType} from '@/Utilities/enumHelper';
import {currencyFormat, dateTimeFormat} from '@/Utilities/formatters';
import {DataTableFilter} from '@/types/component';
import {Hospital, Transaction} from '@/types/model';
import {EnumResponse, PaginateResponse} from '@/types/response';

const props = defineProps<{
    transactions: PaginateResponse<Transaction>;
    transactionTypes: EnumResponse[];
    paymentMethods: EnumResponse[];
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
    type: {value: null, type: 'number'},
    method: {value: null, type: 'array:number'},
    patient: {value: null, type: 'number'},
    start_date: {value: null, type: 'date'},
    due_date: {value: null, type: 'date'},
});

if (props.hospitals) {
    filters.value.hospital = {value: null, type: 'number'};
}
</script>

<template>
    <DashboardLayout title="Kasa İşlemleri">
        <BaseDataTable
            :create-url="route('dashboard.transaction.create')"
            :filters
            :paginate="transactions"
            create-label="Kasa İşlemi Oluştur"
            only="transactions"
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
                    v-model.number="filters.type.value"
                    :options="transactionTypes"
                    class="col-span-1"
                    label="İşlem türü"
                    option-label="label"
                    option-value="value"
                    show-clear
                    size="small"
                >
                    <template #option="slotProps">
                        <Tag
                            :icon="transactionType(slotProps.data.option.value).icon"
                            :severity="transactionType(slotProps.data.option.value).severity"
                            :value="slotProps.data.option.label"
                        />
                    </template>
                </SelectField>

                <MultiSelectField
                    v-model="filters.method.value"
                    :options="paymentMethods"
                    class="col-span-1"
                    label="Ödeme yöntemi"
                    multiple
                    option-label="label"
                    option-value="value"
                    show-clear
                    size="small"
                >
                    <template #option="slotProps">
                        <Tag
                            :icon="paymentMethod(slotProps.data.option.value).icon"
                            :severity="paymentMethod(slotProps.data.option.value).severity"
                            :value="slotProps.data.option.label"
                        />
                    </template>
                </MultiSelectField>

                <PatientSelector
                    v-model.number="filters.patient.value"
                    :default-data="transactions.data[0]?.patient ?? undefined"
                    show-clear
                    size="small"
                />

                <DateField
                    v-model="filters.start_date.value"
                    :max-date="filters.due_date.value ? filters.due_date.value : null"
                    class="col-span-1"
                    date-format="d MM yy"
                    label="Başlangıç tarihi"
                    size="small"
                />

                <DateField
                    v-model="filters.due_date.value"
                    :min-date="filters.start_date.value ? filters.start_date.value : null"
                    class="col-span-1"
                    date-format="d MM yy"
                    label="Bitiş tarihi"
                    size="small"
                />
            </template>

            <Column field="id" header="ID" sortable />
            <Column v-if="hospitals" field="hospital" header="Hastane">
                <template #body="slotProps">
                    {{ hospitalNames[slotProps.data.hospital_id] }}
                </template>
            </Column>
            <Column field="type" header="İşlem Türü">
                <template #body="slotProps">
                    <Tag
                        :icon="transactionType(slotProps.data.type).icon"
                        :severity="transactionType(slotProps.data.type).severity"
                        :value="slotProps.data.type_label"
                    />
                </template>
            </Column>
            <Column field="method" header="Ödeme Yöntemi">
                <template #body="slotProps">
                    <Tag
                        :icon="paymentMethod(slotProps.data.method).icon"
                        :severity="paymentMethod(slotProps.data.method).severity"
                        :value="slotProps.data.method_label"
                    />
                </template>
            </Column>
            <Column field="user.name" header="Kullanıcı" />
            <Column field="patient.full_name" header="Hasta" />
            <Column field="total" header="Tutar" sortable>
                <template #body="slotProps">
                    {{ currencyFormat(slotProps.data.total) }}
                </template>
            </Column>
            <Column field="description" header="Açıklama" />
            <Column field="created_at" header="İşlem Tarihi">
                <template #body="slotProps">
                    {{ dateTimeFormat(slotProps.data.created_at) }}
                </template>
            </Column>
            <Column header="İşlemler">
                <template #body="slotProps">
                    <div class="table-actions">
                        <DeletePopup
                            :url="route('dashboard.transaction.destroy', {id: slotProps.data.id})"
                        />

                        <EditLink
                            :url="route('dashboard.transaction.edit', {id: slotProps.data.id})"
                        />
                    </div>
                </template>
            </Column>
        </BaseDataTable>
    </DashboardLayout>
</template>
