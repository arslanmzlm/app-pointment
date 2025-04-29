<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {clone} from 'lodash';
import {Button, Card, DatePicker, FloatLabel, InputNumber, InputText, Select} from 'primevue';
import {computed, watch} from 'vue';
import PreviewDates from '@/Forms/Parts/PreviewDates.vue';
import {AppointmentMultipleFormType, TreatmentFormType} from '@/types/form';
import {AppointmentType, Service} from '@/types/model';

const props = defineProps<{
    form: InertiaForm<AppointmentMultipleFormType | TreatmentFormType>;
    passiveDates?: string[];
    appointmentTypes: AppointmentType[];
    hospitalId?: number | null;
    services: Service[];
}>();

function add() {
    if (props.form.appointments) {
        props.form.appointments.push({
            start_date: null,
            duration: 60,
            appointment_type_id: null,
            service_id: null,
            note: '',
        });
    }
}

function remove(index: number) {
    if (props.form.appointments) {
        props.form.appointments.splice(index, 1);
    }
}

function getMinDate(index: number) {
    if (index === 0) {
        return new Date();
    }

    if (props.form.appointments) {
        let prevElement = props.form.appointments[index - 1];

        if (prevElement && prevElement.start_date !== null) {
            let date = clone(prevElement.start_date);
            date.setDate(date.getDate() + 1);
            date.setHours(0, 0, 0);

            return date;
        }
    }

    return undefined;
}

function check() {
    if (props.form.appointments) {
        props.form.appointments.forEach((appointment, index) => {
            if (props.form.appointments && props.form.appointments[index - 1]) {
                let prevDate = props.form.appointments[index - 1].start_date;

                if (prevDate === null) {
                    appointment.start_date = null;
                } else if (appointment.start_date !== null && prevDate >= appointment.start_date) {
                    appointment.start_date = null;
                }
            }
        });
    }
}

const disabledDates = computed(() => {
    if (props.passiveDates) {
        return props.passiveDates.map((date) => {
            return new Date(date);
        });
    }

    return undefined;
});

const serviceOptions = computed(() => {
    if (props.services) {
        if (props.hospitalId === undefined) {
            return props.services;
        }

        return props.services.filter((service) => {
            return service.hospital_id === props.hospitalId;
        });
    }

    return [];
});

watch(
    () => props.hospitalId,
    () => {
        if (props.form.appointments) {
            props.form.appointments.forEach((appointment) => {
                appointment.service_id = null;
            });
        }
    },
);
</script>

<template>
    <Card>
        <template #title>Randevular</template>
        <template #content>
            <div v-if="form" class="space-y-4">
                <div
                    v-if="form && form.appointments && form.appointments.length > 0"
                    class="hidden lg:grid lg:grid-cols-7 lg:gap-3 lg:pr-10"
                >
                    <div class="col-span-1 font-bold">Tarih</div>
                    <div class="col-span-1 font-bold">Randevu süresi (dakika)</div>
                    <div class="col-span-1 font-bold">Randevu Tipi</div>
                    <div class="col-span-1 font-bold">Hizmet</div>
                    <div class="col-span-3 font-bold">Not</div>
                </div>

                <div
                    v-for="(appointment, index) in form.appointments"
                    :key="index"
                    class="flex gap-3"
                >
                    <div class="grid grow gap-3 lg:grid-cols-7">
                        <FloatLabel class="lg:col-span-1" variant="on">
                            <DatePicker
                                v-model="appointment.start_date"
                                :disabled="getMinDate(index) === undefined"
                                :disabled-dates
                                :invalid="
                                    !!(
                                        form.errors[`appointments.${index}.start_date`] ??
                                        form.errors[`appointments.${index}`]
                                    )
                                "
                                :min-date="getMinDate(index)"
                                :step-minute="5"
                                date-format="d MM"
                                fluid
                                hour-format="24"
                                show-time
                                size="small"
                                @value-change="check"
                            />

                            <label class="block lg:hidden">Tarih</label>
                        </FloatLabel>

                        <FloatLabel class="lg:col-span-1" variant="on">
                            <InputNumber
                                v-model="appointment.duration"
                                :invalid="!!form.errors[`appointments.${index}.duration`]"
                                :step="5"
                                button-layout="horizontal"
                                fluid
                                show-buttons
                                size="small"
                            >
                                <template #incrementbuttonicon>
                                    <span class="pi pi-plus" />
                                </template>
                                <template #decrementbuttonicon>
                                    <span class="pi pi-minus" />
                                </template>
                            </InputNumber>

                            <label class="block lg:hidden">Randevu süresi (dakika)</label>
                        </FloatLabel>

                        <FloatLabel class="lg:col-span-1" variant="on">
                            <Select
                                v-model="appointment.appointment_type_id"
                                :invalid="
                                    !!form.errors[`appointments.${index}.appointment_type_id`]
                                "
                                :options="appointmentTypes"
                                fluid
                                option-label="name"
                                option-value="id"
                                size="small"
                            />

                            <label class="block lg:hidden">Randevu Tipi</label>
                        </FloatLabel>

                        <FloatLabel class="lg:col-span-1" variant="on">
                            <Select
                                v-model="appointment.service_id"
                                :invalid="!!form.errors[`appointments.${index}.service_id`]"
                                :options="serviceOptions"
                                fluid
                                option-label="name"
                                option-value="id"
                                show-clear
                                size="small"
                            />

                            <label class="block lg:hidden">Hizmet</label>
                        </FloatLabel>

                        <FloatLabel class="lg:col-span-3" variant="on">
                            <InputText v-model="appointment.note" fluid size="small" />

                            <label class="block lg:hidden">Not</label>
                        </FloatLabel>

                        <div
                            v-if="
                                form.errors[`appointments.${index}`] ||
                                form.errors[`appointments.${index}.start_date`]
                            "
                            class="col-span-full text-sm text-danger"
                        >
                            {{
                                form.errors[`appointments.${index}`] ??
                                form.errors[`appointments.${index}.start_date`]
                            }}
                        </div>
                    </div>

                    <Button
                        class="w-10 shrink-0 self-start"
                        icon="pi pi-minus"
                        severity="danger"
                        size="small"
                        title="Randevuyu Çıkar"
                        @click="remove(index)"
                    />
                </div>

                <Button icon="pi pi-plus" label="Randevu Ekle" size="small" @click="add" />
            </div>
        </template>
    </Card>

    <PreviewDates :form />
</template>
