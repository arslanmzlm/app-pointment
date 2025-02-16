<script lang="ts" setup>
import {Card} from 'primevue';
import ReportCard from '@/Components/Reports/ReportCard.vue';
import {paymentMethod, transactionType} from '@/Utilities/enumHelper';
import {currencyFormat} from '@/Utilities/formatters';
import {PaymentMethod, TransactionType} from '@/types/enums';
import {TransactionReportItem} from '@/types/response';

defineProps<{
    report: TransactionReportItem;
    hospitalName?: string;
}>();

function typeLabel(key: keyof TransactionReportItem) {
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

function typeIcon(key: keyof TransactionReportItem) {
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

function typeSeverity(key: keyof TransactionReportItem) {
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

function methodLabel(key: keyof TransactionReportItem) {
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
            return 'Toplam';
    }
}

function methodIcon(key: keyof TransactionReportItem) {
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

function methodSeverity(key: keyof TransactionReportItem) {
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
        <template v-if="hospitalName" #title>{{ hospitalName }}</template>
        <template #content>
            <div class="grid grid-cols-12 gap-4">
                <ReportCard
                    v-for="(total, key) in report"
                    :key
                    :icon="methodIcon(key)"
                    :severity="methodSeverity(key)"
                    :title="methodLabel(key)"
                    :type-icon="typeIcon(key)"
                    :type-label="typeLabel(key)"
                    :type-severity="typeSeverity(key)"
                    :value="currencyFormat(total)"
                />
            </div>
        </template>
    </Card>
</template>
