<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import {Button, Card, Message} from 'primevue';
import {computed, ref, watch} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import AppointmentMultipleForm from '@/Forms/AppointmentMultipleForm.vue';
import PatientSelector from '@/Forms/Parts/PatientSelector.vue';
import InputField from '@/Components/Form/InputField.vue';
import MaskField from '@/Components/Form/MaskField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {AppointmentMultipleFormType} from '@/types/form';
import {AppointmentType, Doctor, Hospital, Service} from '@/types/model';

const props = defineProps<{
    appointmentTypes: AppointmentType[];
    passiveDates?: string[];
    hospitals?: Hospital[];
    doctors?: Doctor[];
    services: Service[];
}>();

const breadcrumbs = [{label: 'Randevular', url: route('dashboard.appointment.list')}];

const form = useForm<AppointmentMultipleFormType>({
    doctor_id: 0,
    patient_id: null,
    patient_name: null,
    patient_surname: null,
    patient_phone: '',
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

if (props.hospitals || props.doctors) {
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
                        label="Podolog"
                        option-label="full_name"
                        placeholder="Önce hastane seçiniz."
                        required
                    />

                    <PatientSelector
                        v-if="!form.patient_name && !form.patient_surname && !form.patient_phone"
                        v-model="form.patient_id"
                        :error="form.errors.patient_id"
                        show-clear
                    />

                    <div v-if="!form.patient_id" class="grid gap-6 lg:grid-cols-3">
                        <InputField
                            v-model="form.patient_name"
                            :error="form.errors.patient_name"
                            label="Hastanın Adı"
                            name="patient_name"
                        />

                        <InputField
                            v-model="form.patient_surname"
                            :error="form.errors.patient_surname"
                            label="Hastanın Soyadı"
                            name="patient_surname"
                        />

                        <MaskField
                            v-model="form.patient_phone"
                            :error="form.errors.patient_phone"
                            label="Hastanın Telefonu"
                            mask="phone"
                            name="patient_phone"
                        />
                    </div>

                    <Message severity="info">
                        Kayıtlı bir hasta seçebilirsiniz yada yeni hasta kaydı oluşturmak için
                        gerekli alanları doldurabilirsiniz.
                    </Message>
                </template>
            </Card>

            <AppointmentMultipleForm
                v-if="form.appointments"
                :appointment-types
                :form
                :hospital-id="hospitals && hospitals.length > 0 ? hospital : undefined"
                :passive-dates
                :services
            />

            <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
        </form>
    </DashboardLayout>
</template>
