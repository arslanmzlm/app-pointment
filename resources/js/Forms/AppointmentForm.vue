<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import {computed} from 'vue';
import PatientSelector from '@/Forms/Parts/PatientSelector.vue';
import PreviewDates from '@/Forms/Parts/PreviewDates.vue';
import DateField from '@/Components/Form/DateField.vue';
import InputField from '@/Components/Form/InputField.vue';
import NumberField from '@/Components/Form/NumberField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import TextareaField from '@/Components/Form/TextareaField.vue';
import AppointmentTable from '@/Components/Tables/AppointmentTable.vue';
import {AppointmentFormType} from '@/types/form';
import {Appointment, AppointmentType, Patient} from '@/types/model';

const props = defineProps<{
    form: InertiaForm<AppointmentFormType>;
    appointmentTypes: AppointmentType[];
    passiveDates: string[];
    patient?: Patient;
    appointments?: Appointment[];
    doctorId?: number;
}>();

const disabledDates = computed(() => {
    return props.passiveDates.map((date) => {
        return new Date(date);
    });
});
</script>

<template>
    <form class="space-y-6">
        <Card>
            <template #content>
                <PatientSelector
                    v-if="form.patient_id !== undefined || patient !== undefined"
                    v-model="form.patient_id"
                    :error="form.errors.patient_id"
                    :readonly="patient"
                />

                <SelectField
                    v-model="form.appointment_type_id"
                    :error="form.errors.appointment_type_id"
                    :options="appointmentTypes"
                    label="Randevu Tipi"
                    required
                />

                <DateField
                    v-model="form.start_date"
                    :disabled-dates
                    :error="form.errors.start_date ?? form.errors.appointment"
                    :min-date="new Date()"
                    label="Başlangıç tarihi"
                    required
                    show-time
                />

                <NumberField
                    v-model.number="form.duration"
                    :error="form.errors.duration"
                    :step="5"
                    button-layout="horizontal"
                    label="Randevu Süresi"
                    name="duration"
                    required
                    show-buttons
                />

                <InputField
                    v-model="form.title"
                    :error="form.errors.title"
                    label="Başlık"
                    name="title"
                />

                <TextareaField
                    v-model="form.note"
                    :error="form.errors.note"
                    label="Not"
                    name="note"
                />
            </template>
        </Card>

        <PreviewDates :doctor-id :form />

        <AppointmentTable
            v-if="appointments && appointments.length > 0"
            :appointments
            title="Hastanın Aktif Randevuları"
        />

        <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
    </form>
</template>
