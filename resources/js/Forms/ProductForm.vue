<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import ActivityCheckbox from '@/Components/Form/ActivityCheckbox.vue';
import EditorField from '@/Components/Form/EditorField.vue';
import ImageUploadField from '@/Components/Form/ImageUploadField.vue';
import InputField from '@/Components/Form/InputField.vue';
import NumberField from '@/Components/Form/NumberField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import SlugField from '@/Components/Form/SlugField.vue';
import {ProductFormType} from '@/types/form';

defineProps<{
    form: InertiaForm<ProductFormType>;
    categories: string[];
    brands: string[];
}>();
</script>

<template>
    <form class="space-y-6">
        <Card>
            <template #content>
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

                <SelectField
                    v-model="form.brand"
                    :error="form.errors.brand"
                    :option-label="null"
                    :option-value="null"
                    :options="brands"
                    editable
                    filter
                    label="Marka"
                    required
                />

                <InputField v-model="form.name" :error="form.errors.name" label="Ürün" required />

                <SlugField v-model="form.slug" :error="form.errors.slug" label="SEO URL" />

                <InputField v-model="form.code" :error="form.errors.code" label="Ürün Kodu" />

                <div v-for="(row, index) in form.stocks">
                    <NumberField
                        v-model.number="row.stock"
                        :error="form.errors[`stocks.${index}.stock`]"
                        :label="`${row.hospital_name} - Stok`"
                        button-layout="horizontal"
                        show-buttons
                    />
                </div>

                <NumberField
                    v-model="form.price"
                    :error="form.errors.price"
                    label="Fiyat"
                    mode="currency"
                />

                <ImageUploadField
                    v-if="form.images"
                    v-model="form.images"
                    :error="form.errors.images"
                    :file-limit="10"
                    :max-file-size="5"
                    label="Resimler"
                    multiple
                    name="images"
                />

                <EditorField
                    v-model="form.description"
                    :error="form.errors.description"
                    label="Açıklama"
                    name="description"
                />

                <ActivityCheckbox v-model="form.active" />
            </template>
        </Card>

        <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
    </form>
</template>
