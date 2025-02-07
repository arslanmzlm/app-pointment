<script lang="ts" setup>
import {CalendarOptions, EventClickArg} from '@fullcalendar/core';
import trLocale from '@fullcalendar/core/locales/tr';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';
import timeGridPlugin from '@fullcalendar/timegrid';
import FullCalendar from '@fullcalendar/vue3';
import {router} from '@inertiajs/vue3';
import dayjs from 'dayjs';
import {isArray} from 'lodash';
import {Button, Card, ContextMenu, Tag} from 'primevue';
import {MenuItem} from 'primevue/menuitem';
import {computed, reactive, ref, watch} from 'vue';
import {useHospitalStore} from '@/Stores/hospital';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import DateField from '@/Components/Form/DateField.vue';
import MultiSelectField from '@/Components/Form/MultiSelectField.vue';
import {appointmentState} from '@/Utilities/enumHelper';
import {getInt} from '@/Utilities/parser';
import {AppointmentState} from '@/types/enums';
import {Appointment} from '@/types/model';
import {EnumResponse} from '@/types/response';

const props = defineProps<{
    appointments: Appointment[];
    appointmentStates: EnumResponse[];
}>();

const hospitalStore = useHospitalStore();

const calendarOptions = reactive<CalendarOptions>({
    plugins: [dayGridPlugin, listPlugin, timeGridPlugin, interactionPlugin],
    initialView: 'timeGridWeek',
    locale: trLocale,
    expandRows: true,
    slotMinTime: `${hospitalStore.start_work}:00:00`,
    slotMaxTime: `${hospitalStore.end_work}:00:00`,
    eventClick: (data) => {
        openContextMenu(data);
    },
    headerToolbar: {
        left: 'prev,next',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
    },
});

updateEvents();

function eventTitle(appointment: Appointment) {
    let title = '';
    if (appointment.patient) {
        title += appointment.patient.full_name;
    }
    if (appointment.title) {
        title += ' - ' + appointment.title;
    }
    return title;
}

function updateEvents() {
    calendarOptions.events = props.appointments.map((appointment) => {
        return {
            id: appointment.id.toString(),
            title: eventTitle(appointment),
            start: appointment.start_date,
            end: appointment.due_date,
            color: appointmentState(appointment.state).color,
        };
    });
}

const filters = ref<{
    dateRange: (Date | null)[] | null;
    states: AppointmentState[] | null;
}>({
    dateRange: null,
    states: null,
});

const urlParams = new URLSearchParams(window.location.search);

if (urlParams.has('start_date')) {
    const start_date = new Date(<string>urlParams.get('start_date'));

    if (!isNaN(start_date.getTime())) {
        filters.value.dateRange = [start_date, null];

        const due_date = urlParams.has('due_date')
            ? new Date(<string>urlParams.get('due_date'))
            : null;
        if (due_date && !isNaN(due_date.getTime())) {
            filters.value.dateRange[1] = due_date;
        }
    }
}

if (urlParams.has('states')) {
    const states = <string>urlParams.get('states');
    filters.value.states = states.split(',').map((state) => {
        return parseInt(state);
    });
}

function reloadData() {
    let query: {
        start_date?: string;
        due_date?: string;
        states?: AppointmentState | AppointmentState[] | string;
    } = {};

    if (isArray(filters.value.dateRange)) {
        if (filters.value.dateRange[0] !== null) {
            query.start_date = dayjs(filters.value.dateRange[0]).format('YYYY-MM-DD');
        }

        if (filters.value.dateRange[1] !== null) {
            query.due_date = dayjs(filters.value.dateRange[1]).format('YYYY-MM-DD');
        }
    }

    if (filters.value.states !== null) {
        if (isArray(filters.value.states)) {
            query.states = filters.value.states.join(',');
        } else {
            query.states = filters.value.states;
        }
    }

    router.visit(window.location.pathname, {
        only: ['appointments'],
        data: query,
        preserveState: true,
        onFinish: () => {
            updateEvents();
        },
    });
}

watch(
    filters.value,
    () => {
        reloadData();
    },
    {deep: true},
);

const appointment = computed(() => {
    if (eventId.value) {
        const id = getInt(eventId.value);
        return props.appointments.find((appointment) => {
            return appointment.id === id;
        });
    }

    return null;
});

const eventId = ref<number | string | null>(null);
const menu = ref();
const menuItems = reactive<MenuItem[]>([
    {
        label: 'İşleme Al',
        icon: 'pi pi-play',
        command: () => {
            router.get(route('dashboard.appointment.process', {id: eventId.value}));
        },
    },
    {
        label: 'Ertele',
        icon: 'pi pi-calendar-clock',
        command: () => {
            router.get(route('dashboard.appointment.edit', {id: eventId.value}));
        },
    },
]);

const openContextMenu = (event: EventClickArg) => {
    eventId.value = event.event.id;
    menu.value.show(event.jsEvent);
};
</script>

<template>
    <DashboardLayout title="Randevular">
        <div class="flex justify-end">
            <Button
                :href="route('dashboard.appointment.create')"
                as="Link"
                icon="pi pi-plus"
                label="Toplu Randevu Oluştur"
            />
        </div>

        <Card>
            <template #content>
                <div class="grid grid-cols-4 gap-4">
                    <DateField
                        v-model="filters.dateRange"
                        fluid
                        label="Tarih Aralığı"
                        selection-mode="range"
                    />

                    <MultiSelectField
                        v-model="filters.states"
                        :options="appointmentStates"
                        display="chip"
                        label="Randevu statüsü"
                        multiple
                        option-label="label"
                        option-value="value"
                        show-clear
                    >
                        <template #option="slotProps">
                            <Tag
                                :icon="appointmentState(slotProps.data.option.value).icon"
                                :severity="appointmentState(slotProps.data.option.value).severity"
                                :value="slotProps.data.option.label"
                            />
                        </template>
                    </MultiSelectField>
                </div>
            </template>
        </Card>

        <Card>
            <template #content>
                <FullCalendar :options="calendarOptions" />
            </template>
        </Card>
    </DashboardLayout>

    <ContextMenu ref="menu" :model="menuItems" />
</template>
