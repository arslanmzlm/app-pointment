<script lang="ts" setup>
import {router} from '@inertiajs/vue3';
import dayjs from 'dayjs';
import {isArray, parseInt} from 'lodash';
import {Button, Card, DataTable, DataTablePageEvent, DataTableSortEvent} from 'primevue';
import {computed, ref, watch} from 'vue';
import {getBool, getInt} from '@/Utilities/parser';
import {DataTableFilter} from '@/types/component';
import {PaginateResponse} from '@/types/response';

const props = defineProps<{
    paginate: PaginateResponse<any>;
    only: string | string[];
    createUrl?: string;
    createLabel?: string;
}>();

const urlParams = new URLSearchParams(window.location.search);

const showFilters = ref(false);
const filters = defineModel<DataTableFilter>('filters', {
    required: false,
});

if (filters.value) {
    for (let field in filters.value) {
        if (urlParams.has(field)) {
            const filter = filters.value[field];

            if (filter.type) {
                if (filter.type === 'number') {
                    filter.value = parseInt(<string>urlParams.get(field));
                } else if (filter.type === 'boolean') {
                    filter.value = getBool(<string>urlParams.get(field));
                } else if (filter.type === 'date') {
                    const date = new Date(<string>urlParams.get(field));
                    if (!isNaN(date.getTime())) {
                        filter.value = date;
                    }
                } else if (filter.type === 'array') {
                    filter.value = urlParams.get(field)?.split(',');
                } else if (filter.type === 'array:number') {
                    filter.value = urlParams
                        .get(field)
                        ?.split(',')
                        .map((item) => parseInt(item));
                }
            } else {
                filter.value = urlParams.get(field);
            }

            showFilters.value = true;
        }
    }
}

watch(
    filters,
    () => {
        reloadData();
    },
    {deep: true},
);

const loading = ref(false);
const expandedRows = ref([]);

const total = computed(() => <number>getInt(props.paginate.total));
const params = ref({
    page: <number>getInt(props.paginate.current_page, 1),
    perPage: <number>getInt(props.paginate.per_page, 15),
    sortField: urlParams.get('sort_field'),
    sortOrder: <1 | 0 | -1 | undefined | null>getInt(urlParams.get('sort_order')),
});
const onlyData = computed(() => (!isArray(props.only) ? [props.only] : props.only));

function reloadData(event: DataTableSortEvent | DataTablePageEvent | null = null) {
    let query: {
        page?: number;
        per_page?: number;
        sort_field?: string | null;
        sort_order?: number | null;
        [key: string]: any;
    } = {};

    expandedRows.value = [];

    if (event) {
        if ('page' in event && event.page) {
            params.value.page = event.page + 1;
        } else {
            params.value.page = 1;
        }

        params.value.perPage = event.rows;
        params.value.sortField = <string | undefined>event.sortField ?? null;
        params.value.sortOrder = event.sortOrder;
    } else {
        params.value.page = 1;
    }

    if (params.value.page !== 1) {
        query.page = params.value.page;
    }

    if (params.value.perPage !== 15) {
        query.per_page = params.value.perPage;
    }

    if (params.value.sortField) {
        query.sort_field = params.value.sortField;

        if (params.value.sortOrder) {
            query.sort_order = params.value.sortOrder;
        }
    }

    for (let key in filters.value) {
        const filter = filters.value[key].value;
        if (filter !== null && filter !== '') {
            if (isArray(filter)) {
                query[key] = filter.join(',');
            } else if (filters.value[key].type === 'date') {
                query[key] = dayjs(filter).format('YYYY-MM-DD');
            } else {
                query[key] = filter;
            }
        }
    }

    router.visit(window.location.pathname, {
        only: onlyData.value,
        data: query,
        preserveState: true,
        onBefore: () => {
            loading.value = true;
        },
        onFinish: () => {
            loading.value = false;
        },
    });
}
</script>

<template>
    <Card class="card-fluid">
        <template #content>
            <DataTable
                v-model:expanded-rows="expandedRows"
                :first="(params.page - 1) * params.perPage"
                :loading
                :rows="params.perPage"
                :rows-per-page-options="[15, 30, 50, 100]"
                :sort-field="params.sortField ?? undefined"
                :sort-order="params.sortOrder ?? undefined"
                :total-records="total"
                :value="paginate.data"
                class="base-datatable"
                data-key="id"
                lazy
                paginator
                removable-sort
                row-hover
                size="large"
                @page="reloadData"
                @sort="reloadData"
            >
                <slot></slot>

                <template #header>
                    <div class="flex px-5">
                        <Button
                            v-if="filters"
                            class="size-12"
                            icon="pi pi-filter"
                            title="Filtreleri görüntüle"
                            @click="showFilters = !showFilters"
                        />

                        <Button
                            v-if="createUrl"
                            :href="createUrl"
                            :label="createLabel"
                            as="Link"
                            class="ml-auto h-12"
                            icon="pi pi-plus"
                        />
                    </div>

                    <div
                        v-if="filters && $slots.filters && showFilters"
                        class="mt-4 border-t px-5 pt-4 dark:border-surface-700"
                    >
                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3 xl:grid-cols-4">
                            <slot name="filters"></slot>
                        </div>
                    </div>
                </template>

                <template #expansion="slotProps">
                    <slot :data="slotProps.data" :index="slotProps.index" name="expansion"></slot>
                </template>

                <template #footer>
                    <slot name="footer"></slot>
                </template>
            </DataTable>
        </template>
    </Card>
</template>
