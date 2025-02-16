<script lang="ts" setup>
import {usePage} from '@inertiajs/vue3';
import {Button} from 'primevue';
import {computed} from 'vue';
import {isDoctor} from '@/Utilities/auth';
import {timeFormat} from '@/Utilities/formatters';

const page = usePage();
const appointment = computed(() => {
    return page.props.activeAppointment;
});
const label = computed(() => {
    if (isDoctor() && appointment.value) {
        return `${timeFormat(appointment.value.start_date)} - ${appointment.value.patient?.full_name}`;
    }
});
</script>

<template>
    <div v-if="isDoctor() && appointment">
        <Button
            :badge="appointment.type_name"
            :href="route('dashboard.appointment.process', {id: appointment.id})"
            :label
            as="link"
            badge-severity="primary"
            outlined
            size="small"
        />
    </div>
</template>
