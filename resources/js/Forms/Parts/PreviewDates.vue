<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import axios from 'axios';
import {isArray} from 'lodash';
import {Card, Column, DataTable} from 'primevue';
import {ref, watch} from 'vue';
import {dateFormat, dateTimeFormat, timeFormat} from '@/Utilities/formatters';
import {AppointmentFormType, AppointmentMultipleFormType, TreatmentFormType} from '@/types/form';

const props = defineProps<{
    form: InertiaForm<AppointmentFormType | AppointmentMultipleFormType | TreatmentFormType>;
}>();

watch(() => props.form.data(), getPreviewDates, {deep: true});

const previewDates = ref();

function getPreviewDates() {
    const dates: Date[] = [];

    if (props.form.appointments !== undefined && isArray(props.form.appointments)) {
        props.form.appointments.forEach((appointment) => {
            if (appointment.start_date !== null) {
                dates.push(appointment.start_date);
            }
        });
    } else if (props.form.start_date !== null) {
        dates.push(props.form.start_date);
    }

    if (dates.length > 0) {
        axios.post(route('dashboard.appointment.schedule'), {dates}).then((response) => {
            if (response.data && response.data.dates) {
                previewDates.value = response.data.dates;
            }
        });
    }
}
</script>

<template>
    <Card v-if="previewDates && previewDates.length > 0">
        <template #title>Gün İçindeki Randevular/İzinler</template>
        <template #content>
            <DataTable :value="previewDates" row-hover>
                <Column field="type">
                    <template #body="slotProps">
                        <b>{{ slotProps.data.type === 'appointment' ? 'Randevu' : 'İzinli' }}</b>
                    </template>
                </Column>
                <Column header="Gün">
                    <template #body="slotProps">
                        {{ dateFormat(slotProps.data.start_date) }}
                    </template>
                </Column>
                <Column header="Başlangıç">
                    <template #body="slotProps">
                        {{
                            slotProps.data.type === 'appointment'
                                ? timeFormat(slotProps.data.start_date)
                                : dateTimeFormat(slotProps.data.start_date)
                        }}
                    </template>
                </Column>
                <Column header="Bitiş">
                    <template #body="slotProps">
                        {{
                            slotProps.data.type === 'appointment'
                                ? timeFormat(slotProps.data.due_date)
                                : dateTimeFormat(slotProps.data.due_date)
                        }}
                    </template>
                </Column>

                <template #footer>
                    Belirtilen tarihlerde randevunuz veya izniniz bulunmaktadır. Lütfen yeni randevu
                    oluştururken bu tarihlere dikkat ediniz.
                </template>
            </DataTable>
        </template>
    </Card>
</template>
