<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {clone} from 'lodash';
import {Button, Card, DatePicker, FloatLabel, InputNumber, InputText} from 'primevue';
import {computed} from 'vue';
import PreviewDates from '@/Forms/Parts/PreviewDates.vue';
import {AppointmentMultipleFormType, TreatmentFormType} from '@/types/form';

const props = defineProps<{
    form: InertiaForm<AppointmentMultipleFormType | TreatmentFormType>;
    passiveDates?: string[];
}>();

function add() {
    if (props.form.appointments) {
        props.form.appointments.push({
            start_date: null,
            duration: 60,
            title: `${props.form.appointments.length + 1}. seans`,
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
</script>

<template>
    <Card>
        <template #title>Seanslar</template>
        <template #content>
            <div v-if="form" class="space-y-3">
                <div
                    v-if="form && form.length"
                    class="hidden lg:grid lg:grid-cols-5 lg:gap-3 lg:pr-10"
                >
                    <div class="col-span-1 font-bold">Tarih</div>
                    <div class="col-span-1 font-bold">Seans süresi (dakika)</div>
                    <div class="col-span-3 font-bold">Başlık</div>
                </div>

                <div
                    v-for="(appointment, index) in form.appointments"
                    :key="index"
                    class="flex gap-3"
                >
                    <div class="grid grow gap-3 lg:grid-cols-5">
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

                            <label class="block lg:hidden">Seans süresi (dakika)</label>
                        </FloatLabel>

                        <FloatLabel class="lg:col-span-3" variant="on">
                            <InputText v-model="appointment.title" fluid size="small" />

                            <label class="block lg:hidden">Başlık</label>
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
                        title="Seansı Çıkar"
                        @click="remove(index)"
                    />
                </div>

                <Button icon="pi pi-plus" label="Seans Ekle" size="small" @click="add" />
            </div>
        </template>
    </Card>

    <PreviewDates :form />
</template>
