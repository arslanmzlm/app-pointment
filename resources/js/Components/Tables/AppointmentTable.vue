<script lang="ts" setup>
import {Card, Column, DataTable, Tag} from 'primevue';
import {appointmentState} from '@/Utilities/enumHelper';
import {dateFormat, dateTimeFormat, timeFormat} from '@/Utilities/formatters';
import {Appointment} from '@/types/model';

defineProps<{
    appointments: Appointment[];
    title?: string;
    size?: 'small' | 'large';
}>();
</script>

<template>
    <Card v-if="appointments && appointments.length > 0">
        <template v-if="title" #title>{{ title }}</template>
        <template #content>
            <DataTable
                :class="{'text-sm': size === 'small', 'text-lg': size === 'large'}"
                :rows="10"
                :size
                :value="appointments"
                data-key="id"
                paginator
                row-hover
            >
                <Column field="id" header="ID" />
                <Column field="doctor.full_name" header="Podolog" />
                <Column field="type_name" header="Randevu Tipi" />
                <Column field="state" header="Durum">
                    <template #body="slotProps">
                        <Tag
                            :icon="appointmentState(slotProps.data.state).icon"
                            :severity="appointmentState(slotProps.data.state).severity"
                            :value="slotProps.data.state_label"
                        />
                    </template>
                </Column>
                <Column field="start_date" header="Tarih">
                    <template #body="slotProps">
                        <div class="whitespace-nowrap">
                            <div>{{ dateFormat(slotProps.data.start_date) }}</div>
                            <div>
                                {{ timeFormat(slotProps.data.start_date) }} -
                                {{ timeFormat(slotProps.data.due_date) }}
                            </div>
                            <div>{{ slotProps.data.duration }} dk</div>
                        </div>
                    </template>
                </Column>
                <Column field="created_at" header="Kayıt Tarihi">
                    <template #body="slotProps">
                        {{ dateTimeFormat(slotProps.data.created_at) }}
                    </template>
                </Column>
            </DataTable>
        </template>
    </Card>
</template>
