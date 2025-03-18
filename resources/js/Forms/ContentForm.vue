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
import TextareaField from '@/Components/Form/TextareaField.vue';
import {ContentFormType} from '@/types/form';

defineProps<{
    form: InertiaForm<ContentFormType>;
    sections: string[];
    imageSrc?: string | null;
    mobileImageSrc?: string | null;
    iconSrc?: string | null;
}>();
</script>

<template>
    <form class="space-y-6">
        <Card>
            <template #content>
                <SelectField
                    v-model="form.section"
                    :error="form.errors.section"
                    :option-label="null"
                    :option-value="null"
                    :options="sections"
                    editable
                    filter
                    label="Bölüm"
                    required
                />

                <InputField
                    v-model="form.title"
                    :error="form.errors.title"
                    label="Başlık"
                    required
                />

                <SlugField v-model="form.slug" :error="form.errors.slug" label="SEO URL" />

                <InputField
                    v-model="form.subtitle"
                    :error="form.errors.subtitle"
                    label="Alt Başlık"
                />

                <InputField
                    v-model="form.top_title"
                    :error="form.errors.top_title"
                    label="Üst Başlık"
                />

                <InputField
                    v-model="form.alt_title"
                    :error="form.errors.alt_title"
                    label="Alternatif Başlık"
                />

                <InputField v-model="form.link" :error="form.errors.link" label="Link" />

                <InputField
                    v-model="form.link_label"
                    :error="form.errors.link_label"
                    label="Link Başlığı"
                />

                <ImageUploadField
                    v-model="form.image"
                    :default-img="imageSrc"
                    :error="form.errors.image"
                    :max-file-size="5"
                    label="Görsel"
                    name="image"
                />

                <ImageUploadField
                    v-model="form.mobile_image"
                    :default-img="mobileImageSrc"
                    :error="form.errors.mobile_image"
                    :max-file-size="5"
                    label="Görsel (Mobil)"
                    name="mobile_image"
                />

                <InputField
                    v-model="form.image_alt"
                    :error="form.errors.image_alt"
                    label="Görsel Başlığı"
                />

                <ImageUploadField
                    v-model="form.icon"
                    :default-img="iconSrc"
                    :error="form.errors.icon"
                    :max-file-size="5"
                    label="Simge"
                    name="icon"
                />

                <NumberField
                    v-model.number="form.order"
                    :error="form.errors.order"
                    :step="1"
                    label="Sıra"
                    name="order"
                    type="number"
                />

                <TextareaField
                    v-model="form.description"
                    :error="form.errors.description"
                    label="Açıklama"
                    placeholder="Boş bırakılırsa açıklama kısmı yazdırılır."
                />

                <EditorField
                    v-model="form.body"
                    :error="form.errors.body"
                    label="İçerik"
                    name="description"
                />

                <ActivityCheckbox v-model="form.active" />
            </template>
        </Card>

        <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
    </form>
</template>
