<script lang="ts" setup>
import axios from 'axios';
import {trim} from 'lodash';
import {SelectFilterEvent} from 'primevue/select';
import {reactive, ref} from 'vue';
import InputField from '@/Components/Form/InputField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {Patient} from '@/types/model';

const props = defineProps<{
    error?: string;
    showClear?: boolean;
    size?: 'small' | 'large';
    readonly?: Patient;
    defaultData?: Patient;
}>();

const model = defineModel<number | null>();

let patients = reactive<Patient[]>([]);
const loading = ref(false);

if (model.value && patients.length === 0 && props.defaultData) {
    patients.push(props.defaultData);
}

function getPatients(event: SelectFilterEvent) {
    const val = trim(event.value);

    if (val.length > 2) {
        loading.value = true;

        return axios
            .get(route('dashboard.patient.search'), {params: {search: val}})
            .then((response) => {
                if (response.data) {
                    patients = response.data;
                }
                return patients;
            })
            .finally(() => {
                loading.value = false;
            });
    }
}
</script>

<template>
    <InputField
        v-if="readonly"
        :model-value="`${readonly.full_name} - ${readonly.phone}`"
        label="Hasta"
    />
    <SelectField
        v-else
        v-model="model"
        :error="error"
        :loading
        :option-label="(data: Patient) => `${data.full_name} - ${data.phone}`"
        :options="patients"
        :show-clear
        :size
        filter
        label="Hasta"
        placeholder="Arama yapmak için en az 3 harf girin"
        @filter="getPatients"
    />
</template>
