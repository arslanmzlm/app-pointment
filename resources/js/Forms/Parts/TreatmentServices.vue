<script lang="ts" setup>
import {Button, Card, FloatLabel, InputNumber, InputText, Select} from 'primevue';
import {computed, ref} from 'vue';
import {serviceLabel} from '@/Utilities/labels';
import {TreatmentServiceFormType} from '@/types/form';
import {Service} from '@/types/model';

const props = defineProps<{
    services: Service[];
    data: TreatmentServiceFormType[];
    errors?: Partial<Record<string, string>>;
}>();

const service = ref<Service | null>();

const selects = computed<number[]>(() => {
    return props.data.map((service) => {
        return service.service_id;
    });
});

const options = computed(() => {
    return props.services.filter((service) => {
        return !selects.value.includes(service.id);
    });
});

function add() {
    if (service.value) {
        props.data.push({
            label: serviceLabel(service.value),
            service_id: service.value.id,
            price: service.value.price ?? 0,
        });

        service.value = null;
    }
}

function remove(index: number) {
    props.data.splice(index, 1);
}
</script>

<template>
    <Card>
        <template #title>Hizmetler</template>
        <template #content>
            <div v-if="data" class="space-y-3">
                <div
                    v-if="data && data.length"
                    class="hidden lg:grid lg:grid-cols-3 lg:gap-3 lg:pr-10"
                >
                    <div class="col-span-2 font-bold">Hizmet</div>
                    <div class="col-span-1 font-bold">Fiyat</div>
                </div>

                <div v-for="(service, index) in data" :key="service.service_id" class="flex gap-3">
                    <div class="grid grow grid-cols-1 gap-3 lg:grid-cols-3">
                        <FloatLabel class="lg:col-span-2" variant="on">
                            <InputText
                                v-model="service.label"
                                class="lg:col-span-2"
                                fluid
                                readonly
                                size="small"
                            />

                            <label class="block lg:hidden">Hizmet</label>
                        </FloatLabel>

                        <FloatLabel class="lg:col-span-1" variant="on">
                            <InputNumber
                                v-model.number="service.price"
                                currency="TRY"
                                fluid
                                locale="tr"
                                mode="currency"
                                size="small"
                            />

                            <label class="block lg:hidden">Fiyat</label>
                        </FloatLabel>
                    </div>

                    <Button
                        class="w-10 shrink-0 self-end"
                        icon="pi pi-minus"
                        severity="danger"
                        size="small"
                        title="Değeri Çıkar"
                        @click="remove(index)"
                    />
                </div>

                <div class="flex gap-3">
                    <Select
                        v-model="service"
                        :option-label="serviceLabel"
                        :options="options"
                        class="max-w-[30rem] grow"
                        filter
                        placeholder="Hizmeti Seçin"
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
