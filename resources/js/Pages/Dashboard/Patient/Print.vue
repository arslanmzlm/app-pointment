<script lang="ts" setup>
import {Head} from '@inertiajs/vue3';
import dayjs from 'dayjs';
import {Button} from 'primevue';
import {onMounted} from 'vue';
import {Patient, Province} from '@/types/model';
import {PatientFieldResponse} from '@/types/response';

defineProps<{
    patient: Patient;
    province: Province;
    fields: PatientFieldResponse[];
    description: string;
}>();

onMounted(() => {
    document.addEventListener('click', () => {
        window.print();
    });
});
</script>

<template>
    <Head title="Hasta Kayıt Çıktısı" />

    <img
        alt="Form üst görseli"
        class="print-header fixed left-0 top-0"
        src="@/Images/consent-form/header.jpg"
    />

    <img
        alt="Form alt görseli"
        class="print-footer fixed bottom-0 left-0"
        src="@/Images/consent-form/footer.jpg"
    />

    <div class="print-button fixed top-32 flex justify-center print:hidden">
        <Button icon="pi pi-print" label="Yazdır" />
    </div>

    <table class="print-content">
        <thead>
            <tr>
                <td>
                    <div class="print-header-space h-px">&nbsp;</div>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="print-body">
                        <div class="print-date me-8 flex justify-end">
                            Tarih: {{ dayjs().format('DD/MM/YYYY') }}
                        </div>

                        <div class="mb-4 space-y-1">
                            <div class="grid grid-cols-4 items-baseline gap-1">
                                <div class="print-label col-span-1">Adı Soyadı</div>
                                <div class="print-value col-span-3">: {{ patient.full_name }}</div>
                            </div>

                            <div class="grid grid-cols-4 items-baseline gap-1">
                                <div class="print-label col-span-1">Doğum Tarihi</div>
                                <div class="print-value col-span-3">
                                    :
                                    {{
                                        patient.birthday
                                            ? dayjs(patient.birthday).format('DD/MM/YYYY')
                                            : ''
                                    }}
                                </div>
                            </div>

                            <div class="grid grid-cols-4 items-baseline gap-1">
                                <div class="print-label col-span-1">Cinsiyet</div>
                                <div class="print-value col-span-3">
                                    : {{ patient.gender ? patient.gender_label : '' }}
                                </div>
                            </div>

                            <div class="grid grid-cols-4 items-baseline gap-1">
                                <div class="print-label col-span-1">Telefon</div>
                                <div class="print-value col-span-3">
                                    : {{ patient.phone ? patient.phone : '' }}
                                </div>
                            </div>

                            <div class="grid grid-cols-4 items-baseline gap-1">
                                <div class="print-label col-span-1">Şehir</div>
                                <div class="print-value col-span-3">
                                    : {{ province ? province.name : '' }}
                                </div>
                            </div>

                            <div
                                v-for="(field, index) in fields"
                                :key="index"
                                class="flex items-end gap-2"
                            >
                                <div class="print-label shrink-0">{{ field.name }}</div>
                                <div class="print-value grow">
                                    :
                                    {{
                                        field.value && field.value.description !== undefined
                                            ? field.value.description
                                            : field.value
                                    }}
                                </div>

                                <div
                                    v-if="field.value && field.value.options"
                                    class="print-options flex justify-center gap-3"
                                >
                                    <div
                                        v-for="option in field.value.options"
                                        class="flex flex-col items-center"
                                    >
                                        <div class="text-sm">{{ option.value }}</div>
                                        <div class="flex size-5 items-center justify-center border">
                                            <span
                                                v-if="field.value.selection === option.id"
                                                class="pi pi-check text-xs"
                                            ></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div
                            class="print-description space-y-2 font-semibold"
                            v-html="description"
                        ></div>

                        <div class="print-signature text-end italic">İMZA</div>
                    </div>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td>
                    <div class="print-footer-space h-px">&nbsp;</div>
                </td>
            </tr>
        </tfoot>
    </table>
</template>

<style scoped>
:global(html),
:global(body) {
    @apply bg-white;

    width: 210mm;
    height: 297mm;
    font-size: 12pt;
}

@page {
    size: A4;
    margin: 0;
}

.print-content {
    font-weight: 700;
    color: #001c34;
}

.print-header {
    width: 210mm;
}

.print-body {
    margin-left: 60px;
    margin-right: 60px;
}

.print-description {
    font-size: 11pt;
}

.print-footer {
    width: 210mm;
}

.print-button {
    width: 210mm;
}

.print-header-space {
    margin-top: 211px;
}

.print-footer-space {
    margin-top: 87px;
}

.print-label {
    @apply text-start uppercase;
}

.print-value {
    @apply pb-1;
    border-bottom: 2px dotted #000000 !important;
    font-size: 11pt;
}

.print-signature {
    font-size: 14pt;
}
</style>
