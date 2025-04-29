<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import {computed, ref, watch} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import AppointmentMultipleForm from '@/Forms/AppointmentMultipleForm.vue';
import PatientSelector from '@/Forms/Parts/PatientSelector.vue';
import TreatmentPayment from '@/Forms/Parts/TreatmentPayment.vue';
import TreatmentProducts from '@/Forms/Parts/TreatmentProducts.vue';
import TreatmentServices from '@/Forms/Parts/TreatmentServices.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import TextareaField from '@/Components/Form/TextareaField.vue';
import {PaymentMethod} from '@/types/enums';
import {TreatmentFormType} from '@/types/form';
import {AppointmentType, Doctor, Hospital, Product, Service} from '@/types/model';
import {EnumResponse} from '@/types/response';

const props = defineProps<{
    hospitals?: Hospital[];
    doctors?: Doctor[];
    services: Service[];
    products: Product[];
    paymentMethods: EnumResponse[];
    passiveDates: string[];
    appointmentTypes: AppointmentType[];
}>();

const breadcrumbs = [{label: 'İşlemler', url: route('dashboard.treatment.list')}];

const form = useForm<TreatmentFormType>({
    doctor_id: 0,
    patient_id: 0,
    note: null,
    services: [],
    products: [],
    appointments: [],
    payment_method: PaymentMethod.CASH,
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

const serviceOptions = computed(() => {
    if (props.services) {
        if (hospital.value === undefined) {
            return props.services;
        }

        return props.services.filter((service) => {
            return service.hospital_id === hospital.value;
        });
    }

    return [];
});

watch(hospital, () => {
    form.services = [];
});

function submit() {
    form.post(route('dashboard.treatment.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="İşlem Oluştur">
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

                    <PatientSelector v-model="form.patient_id" :error="form.errors.patient_id" />

                    <TextareaField v-model="form.note" :error="form.errors.note" label="Not" />
                </template>
            </Card>

            <TreatmentServices
                :data="form.services"
                :errors="form.errors"
                :services="serviceOptions"
            />

            <TreatmentProducts :data="form.products" :errors="form.errors" :products />

            <AppointmentMultipleForm
                v-if="form.appointments"
                :appointment-types
                :form
                :hospital-id="hospitals && hospitals.length > 0 ? hospital : undefined"
                :passive-dates
                :services
            />

            <TreatmentPayment :form :payment-methods />

            <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
        </form>
    </DashboardLayout>
</template>
