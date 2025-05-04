<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import {computed} from 'vue';
import NumberField from '@/Components/Form/NumberField.vue';
import {paymentMethod} from '@/Utilities/enumHelper';
import {PaymentMethod} from '@/types/enums';
import {TreatmentFormType} from '@/types/form';
import {EnumResponse} from '@/types/response';

const props = defineProps<{
    form: InertiaForm<TreatmentFormType>;
    paymentMethods: EnumResponse[];
}>();

const total = computed(() => {
    let total = 0;

    props.form.services.forEach((service) => {
        if (service.price) {
            total += service.price;
        }
    });

    props.form.products.forEach((product) => {
        if (product.count && product.price) {
            total += product.count * product.price;
        }
    });

    return total;
});

const paymentTotal = computed(() => {
    let total = 0;

    props.form.payments.forEach((payment) => {
        if (payment.amount) {
            total += payment.amount;
        }
    });

    return total;
});

const setTotal = (method: PaymentMethod) => {
    props.form.payments.forEach((payment) => {
        if (payment.method === method) {
            payment.amount = total.value;
        } else {
            payment.amount = 0;
        }
    });
};
</script>

<template>
    <Card>
        <template #title>Ödeme Bilgileri</template>
        <template #content>
            <div class="grid grid-cols-3">
                <div
                    v-for="(payment, index) in form.payments"
                    :key="index"
                    class="col-span-1 flex flex-col items-center gap-3"
                >
                    <div>
                        <span :class="paymentMethod(payment.method).icon"></span>&nbsp;<b>{{
                            payment.label
                        }}</b>
                    </div>
                    <div class="flex gap-2">
                        <NumberField v-model="payment.amount" mode="currency" size="small" />
                        <Button
                            icon="pi pi-calculator"
                            severity="info"
                            size="small"
                            title="Toplam Tutarı Yansıt"
                            @click="setTotal(payment.method)"
                        />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-1">
                    <NumberField
                        :model-value="total"
                        label="Toplam Tutar"
                        mode="currency"
                        readonly
                    />
                </div>
                <div class="col-span-1">
                    <NumberField
                        :error="
                            total !== paymentTotal
                                ? 'Girilen tutar ile toplam tutar eşit değil'
                                : undefined
                        "
                        :model-value="paymentTotal"
                        label="Girilen Tutar"
                        mode="currency"
                        readonly
                    />
                </div>
            </div>
        </template>
    </Card>
</template>
