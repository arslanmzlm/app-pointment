<script lang="ts" setup>
import {router} from '@inertiajs/vue3';
import dayjs from 'dayjs';
import {isArray} from 'lodash';
import {Button, Menu} from 'primevue';
import {computed, ref, watch} from 'vue';
import DateField from '@/Components/Form/DateField.vue';
import {dateFormat} from '@/Utilities/formatters';
import {getBool} from '@/Utilities/parser';

const props = defineProps<{
    cacheKey?: string;
}>();

const urlParams = new URLSearchParams(window.location.search);

const dateRange = ref<Array<Date | null>>([
    dayjs().startOf('month').toDate(),
    dayjs().endOf('month').toDate(),
]);

const entire = ref<boolean>(false);

if (urlParams.has('start_date') && urlParams.has('due_date')) {
    const startDate = new Date(<string>urlParams.get('start_date'));
    const dueDate = new Date(<string>urlParams.get('due_date'));
    if (!isNaN(startDate.getTime()) && !isNaN(dueDate.getTime())) {
        dateRange.value = [startDate, dueDate];
    }
} else if (urlParams.has('entire') && getBool(urlParams.get('entire'))) {
    entire.value = true;

    dateRange.value = [null, null];
}

const title = computed(() => {
    if (entire.value) {
        return 'Tüm Zamanlar';
    }

    let val = '';
    if (dateFormat(dateRange.value[0]) !== null) {
        val += dateFormat(dateRange.value[0]);
    }
    if (dateFormat(dateRange.value[1]) !== null) {
        if (val !== '') val += ' -';
        val += ' ' + dateFormat(dateRange.value[1]);
    }

    return val;
});

function reloadData() {
    let query: {
        start_date?: string;
        due_date?: string;
    } = {};

    if (isArray(dateRange.value) && dateRange.value[0] !== null && dateRange.value[1] !== null) {
        query.start_date = dayjs(dateRange.value[0]).format('YYYY-MM-DD');
        query.due_date = dayjs(dateRange.value[1]).format('YYYY-MM-DD');

        router.visit(window.location.pathname, {
            only: ['reports'],
            data: query,
            preserveState: true,
        });
    } else if (entire.value === true) {
        router.visit(window.location.pathname, {
            only: ['reports'],
            data: {entire: true},
            preserveState: true,
        });
    }
}

watch(
    () => dateRange.value,
    () => {
        reloadData();
    },
);

const menu = ref();
const items = ref([
    {
        label: 'Aralıklar',
        items: [
            {
                label: 'Bugün',
                command: () => {
                    entire.value = false;

                    dateRange.value = [
                        dayjs().startOf('day').toDate(),
                        dayjs().endOf('day').toDate(),
                    ];
                },
            },
            {
                label: 'Dün',
                command: () => {
                    entire.value = false;

                    dateRange.value = [
                        dayjs().subtract(1, 'day').startOf('day').toDate(),
                        dayjs().subtract(1, 'day').endOf('day').toDate(),
                    ];
                },
            },
            {
                label: 'Bu Hafta',
                command: () => {
                    entire.value = false;

                    dateRange.value = [
                        dayjs().startOf('week').toDate(),
                        dayjs().endOf('week').toDate(),
                    ];
                },
            },
            {
                label: 'Bu Ay',
                command: () => {
                    entire.value = false;

                    dateRange.value = [
                        dayjs().startOf('month').toDate(),
                        dayjs().endOf('month').toDate(),
                    ];
                },
            },
            {
                label: 'Geçen Ay',
                command: () => {
                    entire.value = false;

                    dateRange.value = [
                        dayjs().subtract(1, 'month').startOf('month').toDate(),
                        dayjs().subtract(1, 'month').endOf('month').toDate(),
                    ];
                },
            },
            {
                label: 'Tüm Zamanlar',
                command: () => {
                    dateRange.value = [null, null];

                    entire.value = true;
                },
            },
        ],
    },
]);

function toggle(event: any) {
    menu.value.toggle(event);
}

function clearCache() {
    if (props.cacheKey) {
        router.post(route('dashboard.report.clear'), {key: props.cacheKey});

        router.reload();
    }
}
</script>

<template>
    <div class="flex flex-col items-center justify-between gap-4 lg:flex-row">
        <h2 class="max-lg:text-xl">{{ title }}</h2>

        <div class="flex gap-2">
            <Button
                v-if="cacheKey"
                icon="pi pi-refresh"
                severity="info"
                type="button"
                @click="clearCache"
            />

            <DateField
                v-model="dateRange"
                class="min-w-72 shrink-0"
                date-format="d MM yy"
                placeholder="Rapor aralığını belirleyiniz"
                selection-mode="range"
            />

            <Button
                aria-controls="overlay_menu"
                aria-haspopup="true"
                icon="pi pi-ellipsis-v"
                type="button"
                @click="toggle"
            />
        </div>
    </div>
    <Menu id="overlay_menu" ref="menu" :model="items" :popup="true" />
</template>
