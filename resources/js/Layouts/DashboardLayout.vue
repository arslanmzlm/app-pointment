<script lang="ts" setup>
import {Head, usePage} from '@inertiajs/vue3';
import {isEmpty} from 'lodash';
import {ConfirmDialog, ConfirmPopup, Toast, useToast} from 'primevue';
import {MenuItem} from 'primevue/menuitem';
import {computed, onMounted, reactive, watch} from 'vue';
import DashboardBreadcrumb from '@/Layouts/Parts/DashboardBreadcrumb.vue';
import DashboardHeader from '@/Layouts/Parts/DashboardHeader.vue';
import DashboardSidebar from '@/Layouts/Parts/DashboardSidebar.vue';
import CalendarCircleExclamationIcon from '@/Icons/CalendarCircleExclamationIcon.vue';
import CalendarDayIcon from '@/Icons/CalendarDayIcon.vue';
import CalendarIcon from '@/Icons/CalendarIcon.vue';
import CashRegisterIcon from '@/Icons/CashRegisterIcon.vue';
import ClipboardAttachmentIcon from '@/Icons/ClipboardAttachmentIcon.vue';
import FileInfoIcon from '@/Icons/FileInfoIcon.vue';
import FileWiredIcon from '@/Icons/FileWiredIcon.vue';
import HospitalIcon from '@/Icons/HospitalIcon.vue';
import HospitalUserIcon from '@/Icons/HospitalUserIcon.vue';
import IdCardIcon from '@/Icons/IdCardIcon.vue';
import NoteMedicalIcon from '@/Icons/NoteMedicalIcon.vue';
import PrescriptionBottleMedicalIcon from '@/Icons/PrescriptionBottleMedicalIcon.vue';
import PresentationChartIcon from '@/Icons/PresentationChartIcon.vue';
import SuitcaseMedical from '@/Icons/SuitcaseMedical.vue';
import UsersIcon from '@/Icons/UsersIcon.vue';
import {SidebarMenuRow} from '@/types/component';
import {UserType} from '@/types/enums';

defineProps<{
    title: string;
    breadcrumbs?: MenuItem[];
}>();

const page = usePage();
const toastHelper = useToast();
const errors = computed(() => page.props.errors);
const toasts = computed(() => page.props.toasts);

watch(
    errors,
    () => {
        if (!isEmpty(errors.value)) {
            toastHelper.add({
                severity: 'error',
                summary: 'Bildirim',
                detail: 'Formda eksik veya yanlış alan(lar) bulunmakta. Lütfen formu kontrol ediniz.',
                life: 5000,
            });
        }
    },
    {deep: true},
);

watch(
    toasts,
    () => {
        fireToasts();
    },
    {deep: true},
);

onMounted(() => {
    fireToasts();
});

function fireToasts() {
    toasts.value.forEach((item) => {
        toastHelper.add({
            severity: item.type,
            summary: 'Bildirim',
            detail: item.message,
            life: 5000,
        });
    });
}

let menu = reactive<SidebarMenuRow[]>([]);

if (page.props.auth.user.type === UserType.ADMIN) {
    menu = [
        {
            name: 'HASTANE',
            items: [
                {icon: HospitalIcon, label: 'Hastaneler', url: route('dashboard.hospital.list')},
                {icon: IdCardIcon, label: 'Podologlar', url: route('dashboard.doctor.list')},
                {icon: SuitcaseMedical, label: 'Hizmetler', url: route('dashboard.service.list')},
                {
                    icon: PrescriptionBottleMedicalIcon,
                    label: 'Ürünler',
                    url: route('dashboard.product.list'),
                },
            ],
        },
        {
            name: 'HASTA',
            items: [
                {icon: HospitalUserIcon, label: 'Hastalar', url: route('dashboard.patient.list')},
                {
                    icon: CalendarIcon,
                    label: 'Randevular',
                    url: route('dashboard.appointment.list'),
                },
                {
                    icon: CalendarIcon,
                    label: 'Takvim',
                    url: route('dashboard.appointment.calendar'),
                },
                {
                    icon: NoteMedicalIcon,
                    label: 'İşlemler',
                    url: route('dashboard.treatment.list'),
                },
            ],
        },
        {
            name: 'İDARİ',
            items: [
                {
                    icon: CashRegisterIcon,
                    label: 'Kasa',
                    url: route('dashboard.transaction.list'),
                },
                {
                    icon: CalendarCircleExclamationIcon,
                    label: 'İzinler',
                    url: route('dashboard.passive.date.list'),
                },
            ],
        },
        {
            name: 'RAPORLAR',
            items: [
                {
                    icon: PresentationChartIcon,
                    label: 'Kasa Raporu',
                    url: route('dashboard.report.transaction'),
                },
                {
                    icon: PresentationChartIcon,
                    label: 'Hasta Raporu',
                    url: route('dashboard.report.patient'),
                },
                {
                    icon: PresentationChartIcon,
                    label: 'İşlem Raporu',
                    url: route('dashboard.report.treatment'),
                },
                {
                    icon: PresentationChartIcon,
                    label: 'Randevu Raporu',
                    url: route('dashboard.report.appointment'),
                },
            ],
        },
        {
            name: 'SİSTEM',
            items: [
                {icon: UsersIcon, label: 'Kullanıcılar', url: route('dashboard.user.list')},
                {
                    icon: ClipboardAttachmentIcon,
                    label: 'Hasta Kayıt Ek Alanlar',
                    url: route('dashboard.field.list'),
                },
                {
                    icon: CalendarDayIcon,
                    label: 'Randevu Tipleri',
                    url: route('dashboard.appointment.type.list'),
                },
                {
                    icon: FileInfoIcon,
                    label: 'Uygulama İçeriği',
                    url: route('dashboard.content.list'),
                },
                {
                    icon: FileWiredIcon,
                    label: 'Ayarlar',
                    url: route('dashboard.setting.index'),
                },
            ],
        },
    ];
} else if (page.props.auth.user.type === UserType.DOCTOR) {
    menu = [
        {
            name: 'HASTA',
            items: [
                {icon: HospitalUserIcon, label: 'Hastalar', url: route('dashboard.patient.list')},
                {icon: NoteMedicalIcon, label: 'İşlemler', url: route('dashboard.treatment.list')},
                {
                    icon: CalendarIcon,
                    label: 'Randevular',
                    url: route('dashboard.appointment.list'),
                },
                {
                    icon: CalendarIcon,
                    label: 'Takvim',
                    url: route('dashboard.appointment.calendar'),
                },
            ],
        },
        {
            name: 'HASTANE',
            items: [
                {icon: SuitcaseMedical, label: 'Hizmetler', url: route('dashboard.service.list')},
                {
                    icon: PrescriptionBottleMedicalIcon,
                    label: 'Ürünler',
                    url: route('dashboard.product.list'),
                },
            ],
        },
        {
            name: 'İDARİ',
            items: [
                {
                    icon: CashRegisterIcon,
                    label: 'Kasa',
                    url: route('dashboard.transaction.list'),
                },
                {
                    icon: CalendarCircleExclamationIcon,
                    label: 'İzinler',
                    url: route('dashboard.passive.date.list'),
                },
            ],
        },
    ];
}
</script>

<template>
    <Head :title />

    <!-- ===== Page Wrapper Start ===== -->
    <div class="flex h-screen overflow-hidden">
        <!-- ===== Sidebar Start ===== -->
        <DashboardSidebar :menu />
        <!-- ===== Sidebar End ===== -->

        <!-- ===== Content Area Start ===== -->
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            <!-- ===== Header Start ===== -->
            <DashboardHeader />
            <!-- ===== Header End ===== -->

            <!-- ===== Main Content Start ===== -->
            <main>
                <div class="mx-auto space-y-6 p-4 md:p-4 2xl:p-8">
                    <DashboardBreadcrumb
                        v-if="title || breadcrumbs"
                        :home-url="route('dashboard.index')"
                        :items="breadcrumbs"
                        :title
                    />
                    <slot></slot>
                </div>
            </main>
            <!-- ===== Main Content End ===== -->
        </div>
    </div>
    <!-- ===== Page Wrapper End ===== -->

    <Toast position="bottom-center" />

    <ConfirmPopup group="popup" />
    <ConfirmDialog group="dialog" />
</template>
