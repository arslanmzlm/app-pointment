<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import {computed, ref, watch} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import AppointmentMultipleForm from '@/Forms/AppointmentMultipleForm.vue';
import PatientSelector from '@/Forms/Parts/PatientSelector.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {AppointmentMultipleFormType} from '@/types/form';
import {AppointmentType, Doctor, Hospital} from '@/types/model';

const props = defineProps<{
    appointmentTypes: AppointmentType[];
    passiveDates?: string[];
    hospitals?: Hospital[];
    doctors?: Doctor[];
}>();

const breadcrumbs = [{label: 'Randevular', url: route('dashboard.appointment.list')}];

const form = useForm<AppointmentMultipleFormType>({
    doctor_id: 0,
    patient_id: 0,
    appointments: [],
});

const hospital = ref<number | null>(null);

const doctorOptions = computed(() => {
    if (props.doctors) {
        if (props.hospitals === undefined) {
            return props.doctors;
        }

        if (hospital.value !== null) {
            const hospitalId = hospital.value;
            return props.doctors.filter((doctor) => {
                return doctor.hospital_id === hospitalId;
            });
        }
    }

    return [];
});

watch(
    () => hospital.value,
    () => {
        form.doctor_id = 0;
    },
);

if (props.hospitals && props.doctors) {
    form.doctor_id = 0;
}

function submit() {
    form.post(route('dashboard.appointment.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Randevu Oluştur">
        <form class="space-y-6" @submit.prevent="submit">
            <Card>
                <template #content>
                    <SelectField
                        v-if="hospitals && hospitals.length > 0"
                        v-model.number="hospital"
                        :options="hospitals"
                        filter
                        label="Hastane"
                        required
                    />

                    <SelectField
                        v-if="doctors && doctors.length > 0 && form.doctor_id !== undefined"
                        v-model.number="form.doctor_id"
                        :options="doctorOptions"
                        filter
                        label="Doktor"
                        option-label="full_name"
                        placeholder="Önce hastane seçiniz."
                        required
                    />

                    <PatientSelector v-model="form.patient_id" :error="form.errors.patient_id" />
                </template>
            </Card>

            <AppointmentMultipleForm
                v-if="form.appointments"
                :appointment-types
                :form
                :passive-dates
            />

            <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
        </form>
    </DashboardLayout>
</template>
