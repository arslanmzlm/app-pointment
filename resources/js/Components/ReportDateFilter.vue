<script lang="ts" setup>
import {router} from '@inertiajs/vue3';
import dayjs from 'dayjs';
import {isArray} from 'lodash';
import {Button, ButtonGroup} from 'primevue';
import {computed, ref, watch} from 'vue';
import DateField from '@/Components/Form/DateField.vue';
import {dateFormat} from '@/Utilities/formatters';

const urlParams = new URLSearchParams(window.location.search);

const dateRange = ref<Array<Date | null>>([
    dayjs().startOf('month').toDate(),
    dayjs().endOf('month').toDate(),
]);

if (urlParams.has('start_date') && urlParams.has('due_date')) {
    const startDate = new Date(<string>urlParams.get('start_date'));
    const dueDate = new Date(<string>urlParams.get('due_date'));
    if (!isNaN(startDate.getTime()) && !isNaN(dueDate.getTime())) {
        dateRange.value = [startDate, dueDate];
    }
}

const title = computed(() => {
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
    }
}

watch(
    () => dateRange.value,
    () => {
        reloadData();
    },
);

function today() {
    dateRange.value = [dayjs().startOf('day').toDate(), dayjs().endOf('day').toDate()];
}

function week() {
    dateRange.value = [dayjs().startOf('week').toDate(), dayjs().endOf('week').toDate()];
}

function month() {
    dateRange.value = [dayjs().startOf('month').toDate(), dayjs().endOf('month').toDate()];
}

function lastMonth() {
    dateRange.value = [
        dayjs().subtract(1, 'month').startOf('month').toDate(),
        dayjs().subtract(1, 'month').endOf('month').toDate(),
    ];
}
</script>

<template>
    <div class="flex items-center justify-between">
        <h2>{{ title }}</h2>

        <div class="flex items-center justify-between gap-2">
            <ButtonGroup>
                <Button label="Bugün" @click="today" />
                <Button label="Bu hafta" @click="week" />
                <Button label="Bu ay" @click="month" />
                <Button label="Geçen ay" @click="lastMonth" />
            </ButtonGroup>

            <DateField
                v-model="dateRange"
                class="shrink-0"
                date-format="d MM yy"
                selection-mode="range"
            />
        </div>
    </div>
</template>
