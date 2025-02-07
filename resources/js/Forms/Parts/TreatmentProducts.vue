<script lang="ts" setup>
import {Button, Card, FloatLabel, InputNumber, InputText, Select} from 'primevue';
import {computed, ref} from 'vue';
import {productLabel} from '@/Utilities/labels';
import {TreatmentProductFormType} from '@/types/form';
import {Product} from '@/types/model';

const props = defineProps<{
    products: Product[];
    data: TreatmentProductFormType[];
    errors?: Partial<Record<string, string>>;
}>();

const product = ref<Product | null>();

const selects = computed<number[]>(() => {
    return props.data.map((product) => {
        return product.product_id;
    });
});

const options = computed(() => {
    return props.products.filter((product) => {
        return !selects.value.includes(product.id);
    });
});

function add() {
    if (product.value) {
        props.data.push({
            label: productLabel(product.value),
            product_id: product.value.id,
            count: 1,
            price: product.value.price ?? 0,
        });

        product.value = null;
    }
}

function remove(index: number) {
    props.data.splice(index, 1);
}
</script>

<template>
    <Card>
        <template #title>Ürünler</template>
        <template #content>
            <div v-if="data" class="space-y-3">
                <div
                    v-if="data && data.length"
                    class="hidden lg:grid lg:grid-cols-5 lg:gap-3 lg:pr-10"
                >
                    <div class="col-span-2 font-bold">Ürün</div>
                    <div class="col-span-1 font-bold">Adet</div>
                    <div class="col-span-1 font-bold">Fiyat</div>
                    <div class="col-span-1 font-bold">Tutar</div>
                </div>

                <div v-for="(product, index) in data" :key="product.product_id" class="flex gap-3">
                    <div class="grid grow gap-3 lg:grid-cols-5">
                        <FloatLabel class="lg:col-span-2" variant="on">
                            <InputText v-model="product.label" fluid readonly size="small" />

                            <label class="block lg:hidden">Ürün</label>
                        </FloatLabel>

                        <FloatLabel class="lg:col-span-1" variant="on">
                            <InputNumber
                                v-model="product.count"
                                :min="1"
                                fluid
                                show-buttons
                                size="small"
                            />

                            <label class="block lg:hidden">Adet</label>
                        </FloatLabel>

                        <FloatLabel class="lg:col-span-1" variant="on">
                            <InputNumber
                                v-model.number="product.price"
                                currency="TRY"
                                fluid
                                locale="tr"
                                mode="currency"
                                size="small"
                            />

                            <label class="block lg:hidden">Fiyat</label>
                        </FloatLabel>

                        <FloatLabel class="lg:col-span-1" variant="on">
                            <InputNumber
                                :default-value="product.count * product.price"
                                currency="TRY"
                                fluid
                                locale="tr"
                                mode="currency"
                                readonly
                                size="small"
                            />

                            <label class="block lg:hidden">Ürün</label>
                        </FloatLabel>
                    </div>

                    <Button
                        class="w-10 shrink-0 self-end"
                        icon="pi pi-minus"
                        severity="danger"
                        size="small"
                        title="Ürünü Çıkar"
                        @click="remove(index)"
                    />
                </div>

                <div class="flex gap-3">
                    <Select
                        v-model="product"
                        :option-label="productLabel"
                        :options="options"
                        class="max-w-[30rem] grow"
                        filter
                        placeholder="Ürünü Seçin"
                        size="small"
                    />

                    <Button
                        class="shrink-0"
                        icon="pi pi-plus"
                        label="Ekle"
                        size="small"
                        @click="add"
                    />
                </div>
            </div>
        </template>
    </Card>
</template>
