<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import ActivityCheckbox from '@/Components/Form/ActivityCheckbox.vue';
import InputField from '@/Components/Form/InputField.vue';
import NumberField from '@/Components/Form/NumberField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {ProductFormType} from '@/types/form';
import {Hospital} from '@/types/model';

defineProps<{
    form: InertiaForm<ProductFormType>;
    hospitals?: Hospital[];
    categories: string[];
}>();
</script>

<template>
    <form class="space-y-6">
        <Card>
            <template #content>
                <SelectField
                    v-if="hospitals && form.hospital_id !== undefined"
                    v-model="form.hospital_id"
                    :error="form.errors.hospital_id"
                    :options="hospitals"
                    label="Hastane"
                    required
                />

                <SelectField
                    v-model="form.category"
                    :error="form.errors.category"
                    :option-label="null"
                    :option-value="null"
                    :options="categories"
                    editable
                    filter
                    label="Kategori"
                    required
                />

                <InputField v-model="form.name" :error="form.errors.name" label="Ürün" required />

                <InputField v-model="form.code" :error="form.errors.code" label="Ürün Kodu" />

                <NumberField
                    v-model.number="form.stock"
                    :error="form.errors.stock"
                    button-layout="horizontal"
                    label="Stok"
                    show-buttons
                />

                <NumberField
                    v-model="form.price"
                    :error="form.errors.price"
                    label="Fiyat"
                    mode="currency"
                />

                <ActivityCheckbox v-model="form.active" />
            </template>
        </Card>

        <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
    </form>
</template>
