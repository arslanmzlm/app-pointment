<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import {computed, ref, watch} from 'vue';
import DateRangeField from '@/Components/Form/DateRangeField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import TextareaField from '@/Components/Form/TextareaField.vue';
import {PassiveDateFromType} from '@/types/form';
import {Doctor, Hospital} from '@/types/model';

const props = defineProps<{
    form: InertiaForm<PassiveDateFromType>;
    hospitals?: Hospital[];
    doctors?: Doctor[];
}>();

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
        props.form.doctor_id = 0;
    },
);
</script>

<template>
    <form class="space-y-6">
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

                <DateRangeField
                    v-model:due-date="form.due_date"
                    v-model:start-date="form.start_date"
                    show-time
                />

                <TextareaField
                    v-model="form.description"
                    :error="form.errors.description"
                    label="Açıklama"
                />
            </template>
        </Card>

        <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
    </form>
</template>
