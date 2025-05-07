<script lang="ts" setup>
import {router} from '@inertiajs/vue3';
import {Button, Card, Column, DataTable, Tag, useConfirm} from 'primevue';
import DeletePopup from '@/Components/DeletePopup.vue';
import EditLink from '@/Components/EditLink.vue';
import {appointmentState} from '@/Utilities/enumHelper';
import {dateFormat, dateTimeFormat, timeFormat} from '@/Utilities/formatters';
import {appointmentActive} from '@/Utilities/modelHelper';
import {Appointment} from '@/types/model';

defineProps<{
    appointments: Appointment[];
    title?: string;
    size?: 'small' | 'large';
    showActions?: boolean;
}>();

const confirm = useConfirm();

function showConfirm(event: Event, url: string) {
    confirm.require({
        target: <HTMLElement>event.currentTarget,
        message: 'Randevuyu iptal etmek istediğinizden emin misiniz?',
        icon: 'pi pi-exclamation-triangle',
        group: 'popup',
        rejectProps: {
            label: 'Kapat',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'İptal Et',
            severity: 'warn',
        },
        accept: () => {
            router.post(url);
        },
    });
}
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
                <Column field="note" header="Not">
                    <template #body="slotProps">
                        <span
                            v-if="slotProps.data.note"
                            v-tooltip.bottom="
                                slotProps.data.note.length > 10 ? slotProps.data.note : undefined
                            "
                            >{{
                                slotProps.data.note.length > 10
                                    ? slotProps.data.note.substring(0, 10) + '...'
                                    : slotProps.data.note
                            }}</span
                        >
                    </template>
                </Column>
                <Column field="created_at" header="Kayıt Tarihi">
                    <template #body="slotProps">
                        {{ dateTimeFormat(slotProps.data.created_at) }}
                    </template>
                </Column>
                <Column v-if="showActions" header="İşlemler">
                    <template #body="slotProps">
                        <div class="table-actions">
                            <DeletePopup
                                :url="
                                    route('dashboard.appointment.destroy', {id: slotProps.data.id})
                                "
                            />

                            <Button
                                v-if="appointmentActive(slotProps.data)"
                                class="size-12"
                                icon="pi pi-times"
                                severity="warn"
                                title="İptal"
                                @click="
                                    showConfirm(
                                        $event,
                                        route('dashboard.appointment.cancel', {
                                            id: slotProps.data.id,
                                        }),
                                    )
                                "
                            />

                            <EditLink
                                :url="route('dashboard.appointment.edit', {id: slotProps.data.id})"
                            />

                            <Button
                                v-if="appointmentActive(slotProps.data)"
                                :href="
                                    route('dashboard.appointment.process', {id: slotProps.data.id})
                                "
                                as="Link"
                                class="size-12"
                                icon="pi pi-play"
                                severity="info"
                                title="İşleme Al"
                            />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </template>
    </Card>
</template>
