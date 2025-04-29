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

    <table>
        <thead>
            <tr>
                <td>
                    <div class="print-header-space">&nbsp;</div>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="print-body px-6 py-1">
                        <div class="date flex justify-end">
                            Tarih: {{ dayjs().format('DD/MM/YYYY') }}
                        </div>

                        <div class="mb-5">
                            <table>
                                <tbody>
                                    <tr>
                                        <th class="print-label">Adı Soyadı</th>
                                        <td>:</td>
                                        <td class="print-value">{{ patient.full_name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="print-label">Cinsiyet</th>
                                        <td>:</td>
                                        <td class="print-value">{{ patient.gender_label }}</td>
                                    </tr>
                                    <tr>
                                        <th class="print-label">Telefon</th>
                                        <td>:</td>
                                        <td class="print-value">{{ patient.phone }}</td>
                                    </tr>
                                    <tr>
                                        <th class="print-label">Doğum Tarihi</th>
                                        <td>:</td>
                                        <td class="print-value">
                                            {{ dayjs(patient.birthday).format('DD/MM/YYYY') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="print-label">Şehir</th>
                                        <td>:</td>
                                        <td class="print-value">{{ province.name }}</td>
                                    </tr>
                                    <tr v-for="(field, index) in fields" :key="index">
                                        <th class="print-label">{{ field.name }}</th>
                                        <td>:</td>
                                        <td class="print-value">{{ field.value }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="print-description" v-html="description"></div>

                        <div class="print-signature text-end font-bold italic">İmza</div>
                    </div>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td>
                    <div class="print-footer-space">&nbsp;</div>
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

.print-header {
    width: 210mm;
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
    @apply py-1.5 text-start font-semibold;
    width: 35%;
}

.print-value {
    width: 65%;
    border-bottom: 2px dotted #000000 !important;
}

.print-signature {
    font-size: 14pt;
}
</style>
