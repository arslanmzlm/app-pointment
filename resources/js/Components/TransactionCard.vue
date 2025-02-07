<script lang="ts" setup>
import {Card, Tag} from 'primevue';
import {paymentMethod, transactionType} from '@/Utilities/enumHelper';
import {currencyFormat} from '@/Utilities/formatters';
import {PaymentMethod, TransactionType} from '@/types/enums';
import {TransactionReport} from '@/types/response';

defineProps<{
    report: TransactionReport;
    hospitalName: string;
}>();

function typeLabel(key: string) {
    switch (key) {
        case 'income':
        case 'income_cash':
        case 'income_card':
        case 'income_transfer':
            return 'Gelir';
        case 'expense':
        case 'expense_cash':
        case 'expense_card':
        case 'expense_transfer':
            return 'Gider';
        case 'total':
            return 'Net';
    }
}

function typeIcon(key: string) {
    switch (key) {
        case 'income':
        case 'income_cash':
        case 'income_card':
        case 'income_transfer':
            return transactionType(TransactionType.INCOME).icon;
        case 'expense':
        case 'expense_cash':
        case 'expense_card':
        case 'expense_transfer':
            return transactionType(TransactionType.EXPENSE).icon;
        case 'total':
            return 'pi pi-arrows-v';
    }
}

function typeSeverity(key: string) {
    switch (key) {
        case 'income':
        case 'income_cash':
        case 'income_card':
        case 'income_transfer':
            return transactionType(TransactionType.INCOME).severity;
        case 'expense':
        case 'expense_cash':
        case 'expense_card':
        case 'expense_transfer':
            return transactionType(TransactionType.EXPENSE).severity;
        case 'total':
            return 'info';
    }
}

function methodLabel(key: string) {
    switch (key) {
        case 'income_cash':
        case 'expense_cash':
            return 'Nakit';
        case 'income_card':
        case 'expense_card':
            return 'Kredi/Banka Kartı';
        case 'income_transfer':
        case 'expense_transfer':
            return 'Havale/EFT';
        case 'total':
            return 'Ciro';
        case 'income':
            return 'Toplam Gelir';
        case 'expense':
            return 'Toplam Gider';
        default:
            return null;
    }
}

function methodIcon(key: string) {
    switch (key) {
        case 'income_cash':
        case 'expense_cash':
            return paymentMethod(PaymentMethod.CASH).icon;
        case 'income_card':
        case 'expense_card':
            return paymentMethod(PaymentMethod.CARD).icon;
        case 'income_transfer':
        case 'expense_transfer':
            return paymentMethod(PaymentMethod.TRANSFER).icon;
        default:
            return 'pi pi-wallet';
    }
}

function methodSeverity(key: string) {
    switch (key) {
        case 'income_cash':
        case 'expense_cash':
            return paymentMethod(PaymentMethod.CASH).severity;
        case 'income_card':
        case 'expense_card':
            return paymentMethod(PaymentMethod.CARD).severity;
        case 'income_transfer':
        case 'expense_transfer':
            return paymentMethod(PaymentMethod.TRANSFER).severity;
        default:
            return 'danger';
    }
}
</script>

<template>
    <Card>
        <template #content>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-full">
                    <h3>{{ hospitalName }}</h3>
                </div>
                <div v-for="(total, key) in report" :key class="col-span-12 md:col-span-6 lg:col-span-4">
                    <div class="rounded border p-4 shadow-sm dark:border-surface-700">
                        <div class="mb-4 flex justify-between">
                            <div>
                                <span
                                    class="mb-4 block font-medium text-surface-500 dark:text-surface-300"
                                    >{{ methodLabel(key) }}</span
                                >
                                <div
                                    class="text-xl font-medium text-surface-900 dark:text-surface-0"
                                >
                                    {{ currencyFormat(total) }}
                                </div>
                            </div>
                            <Tag
                                :icon="methodIcon(key)"
                                :severity="methodSeverity(key)"
                                class="method-icon flex size-12 items-center justify-center rounded !p-0"
                            />
                        </div>
                        <Tag
                            :icon="typeIcon(key)"
                            :severity="typeSeverity(key)"
                            :value="typeLabel(key)"
                        />
                    </div>
                </div>
            </div>
        </template>
    </Card>
</template>

<style scoped>
.method-icon .p-tag-icon {
    @apply size-5;
    font-size: 20px !important;
    line-height: 1 !important;
}
</style>
