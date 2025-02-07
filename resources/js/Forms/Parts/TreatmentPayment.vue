<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {Card} from 'primevue';
import {computed} from 'vue';
import ListBoxField from '@/Components/Form/ListBoxField.vue';
import NumberField from '@/Components/Form/NumberField.vue';
import {paymentMethod} from '@/Utilities/enumHelper';
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
</script>

<template>
    <Card>
        <template #title>Ödeme Bilgileri</template>
        <template #content>
            <ListBoxField
                v-model="form.payment_method"
                :error="form.errors.payment_method"
                :options="paymentMethods"
                label="Ödeme Yöntemi"
            >
                <template #option="slotProps">
                    <div class="flex items-center gap-2">
                        <span :class="paymentMethod(slotProps.data.option.value).icon"></span>
                        <div>{{ slotProps.data.option.label }}</div>
                    </div>
                </template>
            </ListBoxField>

            <NumberField :model-value="total" label="Toplam Tutar" mode="currency" readonly />
        </template>
    </Card>
</template>
