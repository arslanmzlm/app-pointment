<script lang="ts" setup>
import {Button, Card, Column, DataTable, Timeline} from 'primevue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {isDoctor} from '@/Utilities/auth';
import {currencyFormat, dateTimeFormat, timeFormat} from '@/Utilities/formatters';
import {Appointment, Patient, Treatment} from '@/types/model';

defineProps<{
    patients: Patient[];
    treatments: Treatment[];
    appointments: Appointment[];
}>();
</script>

<template>
    <DashboardLayout title="Anasayfa">
        <div v-if="isDoctor()" class="grid gap-4">
            <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
                <Button
                    :href="route('dashboard.patient.create')"
                    as="link"
                    class="col-span-1 py-8 text-center"
                    icon="pi pi-user-plus"
                    icon-pos="top"
                    label="Hasta Oluştur"
                    severity="success"
                    size="large"
                />

                <Button
                    :href="route('dashboard.appointment.create')"
                    as="link"
                    class="col-span-1 py-8 text-center"
                    icon="pi pi-calendar-plus"
                    icon-pos="top"
                    label="Randevu Oluştur"
                    severity="info"
                    size="large"
                />

                <Button
                    :href="route('dashboard.treatment.create')"
                    as="link"
                    class="col-span-1 py-8 text-center"
                    icon="pi pi-file-plus"
                    icon-pos="top"
                    label="İşlem Oluştur"
                    severity="warn"
                    size="large"
                />

                <Button
                    :href="route('dashboard.transaction.create')"
                    as="link"
                    class="col-span-1 py-8 text-center"
                    icon="pi pi-money-bill"
                    icon-pos="top"
                    label="Gelir/Gider Oluştur"
                    severity="danger"
                    size="large"
                />
            </div>

            <div class="grid items-start gap-4 xl:grid-cols-6">
                <Card v-if="patients && patients.length > 0" class="col-span-2">
                    <template #title>Son Kaydolan Hastalar</template>
                    <template #content>
                        <div class="timeline-box">
                            <Timeline :value="patients">
                                <template #opposite="slotProps">
                                    <small class="text-surface-500 dark:text-surface-400">{{
                                        timeFormat(slotProps.item.created_at)
                                    }}</small>
                                </template>
                                <template #content="slotProps">
                                    <div class="text-surface-700 dark:text-surface-300">
                                        {{ slotProps.item.full_name }}
                                    </div>
                                </template>
                            </Timeline>
                        </div>
                    </template>
                </Card>

                <Card v-if="treatments && treatments.length > 0" class="col-span-2">
                    <template #title>İşlemleriniz</template>
                    <template #content>
                        <div class="timeline-box">
                            <Timeline :value="treatments">
                                <template #opposite="slotProps">
                                    <small class="text-surface-500 dark:text-surface-400">{{
                                        timeFormat(slotProps.item.created_at)
                                    }}</small>
                                </template>
                                <template #content="slotProps">
                                    <div class="text-surface-700 dark:text-surface-300">
                                        {{ slotProps.item.patient.full_name }}
                                    </div>
                                </template>
                            </Timeline>
                        </div>
                    </template>
                </Card>

                <Card v-if="appointments && appointments.length > 0" class="col-span-2">
                    <template #title>Randevularınız</template>
                    <template #content>
                        <div class="timeline-box">
                            <Timeline :value="appointments">
                                <template #opposite="slotProps">
                                    <small class="text-surface-500 dark:text-surface-400">{{
                                        timeFormat(slotProps.item.start_date)
                                    }}</small>
                                </template>
                                <template #content="slotProps">
                                    <div class="text-surface-700 dark:text-surface-300">
                                        {{ slotProps.item.patient.full_name }}
                                    </div>
                                    <div class="text-sm text-surface-600 dark:text-surface-400">
                                        {{ slotProps.item.type_name }}
                                    </div>
                                </template>
                            </Timeline>
                        </div>
                    </template>
                </Card>
            </div>
        </div>
        <div v-else class="grid gap-4">
            <div class="grid grid-cols-2 gap-4 xl:grid-cols-4">
                <Button
                    :href="route('dashboard.patient.create')"
                    as="link"
                    class="col-span-1 py-8 text-center"
                    icon="pi pi-user-plus"
                    icon-pos="top"
                    label="Hasta Oluştur"
                    severity="success"
                    size="large"
                />

                <Button
                    :href="route('dashboard.appointment.create')"
                    as="link"
                    class="col-span-1 py-8 text-center"
                    icon="pi pi-calendar-plus"
                    icon-pos="top"
                    label="Randevu Oluştur"
                    severity="info"
                    size="large"
                />

                <Button
                    :href="route('dashboard.transaction.create')"
                    as="link"
                    class="col-span-1 py-8 text-center"
                    icon="pi pi-money-bill"
                    icon-pos="top"
                    label="Gelir/Gider Oluştur"
                    severity="danger"
                    size="large"
                />

                <Button
                    :href="route('dashboard.doctor.create')"
                    as="link"
                    class="col-span-1 py-8 text-center"
                    icon="pi pi-user-plus"
                    icon-pos="top"
                    label="Podolog Oluştur"
                    severity="warn"
                    size="large"
                />
            </div>

            <div class="grid items-start gap-4 xl:grid-cols-4">
                <Card v-if="patients && patients.length > 0" class="col-span-2">
                    <template #title>Yeni Hastalar</template>
                    <template #content>
                        <div class="timeline-box">
                            <Timeline :value="patients">
                                <template #opposite="slotProps">
                                    <small class="text-surface-500 dark:text-surface-400">{{
                                        timeFormat(slotProps.item.created_at)
                                    }}</small>
                                </template>
                                <template #content="slotProps">
                                    <div class="text-surface-700 dark:text-surface-300">
                                        {{ slotProps.item.full_name }}
                                    </div>
                                </template>
                            </Timeline>
                        </div>
                    </template>
                </Card>

                <Card v-if="appointments && appointments.length > 0" class="col-span-2">
                    <template #title>Bugünkü Randevular</template>
                    <template #content>
                        <div class="timeline-box">
                            <Timeline :value="appointments">
                                <template #opposite="slotProps">
                                    <div class="text-sm text-surface-500 dark:text-surface-400">
                                        <div>{{ timeFormat(slotProps.item.start_date) }}</div>
                                        <div>{{ slotProps.item.type_name }}</div>
                                    </div>
                                </template>
                                <template #content="slotProps">
                                    <div class="text-surface-700 dark:text-surface-300">
                                        <div>{{ slotProps.item.patient.full_name }}</div>
                                        <div class="text-sm">
                                            {{ slotProps.item.doctor.full_name }}
                                        </div>
                                    </div>
                                </template>
                            </Timeline>
                        </div>
                    </template>
                </Card>

                <Card v-if="treatments && treatments.length > 0" class="col-span-full">
                    <template #title>Bugünkü İşlemler</template>
                    <template #content>
                        <DataTable :rows="15" :value="treatments" data-key="id" paginator>
                            <Column field="id" header="ID" />
                            <Column
                                v-if="treatments[0].hospital !== undefined"
                                field="hospital.name"
                                header="Hastane"
                            />
                            <Column field="doctor.full_name" header="Podolog" />
                            <Column field="patient.full_name" header="Hasta">
                                <template #body="slotProps">
                                    <Button
                                        :href="
                                            route('dashboard.patient.show', {
                                                id: slotProps.data.patient.id,
                                            })
                                        "
                                        :label="slotProps.data.patient.full_name"
                                        as="Link"
                                        link
                                    />
                                </template>
                            </Column>
                            <Column field="total" header="Toplam Tutar">
                                <template #body="slotProps">
                                    {{ currencyFormat(slotProps.data.total) }}
                                </template>
                            </Column>
                            <Column field="created_at" header="İşlem Tarihi">
                                <template #body="slotProps">
                                    {{ dateTimeFormat(slotProps.data.created_at) }}
                                </template>
                            </Column>
                        </DataTable>
                    </template>
                </Card>
            </div>
        </div>
    </DashboardLayout>
</template>

<style>
.timeline-box {
    @apply max-h-[400px] overflow-y-auto;
}

.p-timeline-event-opposite {
    @apply w-40 flex-shrink-0 flex-grow-0 basis-auto;
}
</style>
