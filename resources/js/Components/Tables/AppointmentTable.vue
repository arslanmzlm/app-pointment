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
                :size
                :value="appointments"
                data-key="id"
                row-hover
            >
                <Column field="id" header="ID" />
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
                        {{ dateFormat(slotProps.data.start_date) }}
                    </template>
                </Column>
                <Column field="start_date" header="Başlangıç Saati">
                    <template #body="slotProps">
                        {{ timeFormat(slotProps.data.start_date) }}
                    </template>
                </Column>
                <Column field="due_date" header="Bitiş Saati">
                    <template #body="slotProps">
                        {{ timeFormat(slotProps.data.due_date) }}
                    </template>
                </Column>
                <Column field="duration" header="Seans Süresi">
                    <template #body="slotProps"> {{ slotProps.data.duration }} dk</template>
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

<style scoped></style>
