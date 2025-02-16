<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import EditorField from '@/Components/Form/EditorField.vue';
import ImageUploadField from '@/Components/Form/ImageUploadField.vue';
import InputField from '@/Components/Form/InputField.vue';
import MaskField from '@/Components/Form/MaskField.vue';
import NumberField from '@/Components/Form/NumberField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import TextareaField from '@/Components/Form/TextareaField.vue';
import {HospitalFormType} from '@/types/form';
import {Province} from '@/types/model';

defineProps<{
    form: InertiaForm<HospitalFormType>;
    provinces: Province[];
    logoSrc?: string | null;
}>();
</script>

<template>
    <form class="space-y-6">
        <Card>
            <template #content>
                <SelectField
                    v-model="form.province_id"
                    :options="provinces"
                    filter
                    label="Şehir"
                    required
                />

                <InputField
                    v-model="form.name"
                    :error="form.errors.name"
                    label="Hastane"
                    name="name"
                    required
                />

                <NumberField
                    v-model.number="form.start_work"
                    :error="form.errors.start_work"
                    :max="form.end_work - 1"
                    :min="0"
                    button-layout="horizontal"
                    label="Mesai başlangıç saati"
                    name="start_work"
                    required
                    show-buttons
                />

                <NumberField
                    v-model.number="form.end_work"
                    :error="form.errors.end_work"
                    :max="23"
                    :min="form.start_work + 1"
                    button-layout="horizontal"
                    label="Mesai bitiş saati"
                    name="end_work"
                    required
                    show-buttons
                />

                <NumberField
                    v-model.number="form.duration"
                    :error="form.errors.duration"
                    :min="0"
                    :step="5"
                    button-layout="horizontal"
                    label="Randevu süresi"
                    name="duration"
                    required
                    show-buttons
                />

                <InputField
                    v-model="form.title"
                    :error="form.errors.title"
                    label="Başlık"
                    name="title"
                >
                    <template #message>
                        <p>
                            Hastane isminin göründüğü randevu alma gibi alanlarda, hastanenin
                            başlığı tanımlanmışsa başlık görünecektir eğer yoksa hastane adı
                            görünecektir.
                        </p>
                    </template>
                </InputField>

                <ImageUploadField
                    v-model="form.logo"
                    :default-img="logoSrc"
                    :error="form.errors.logo"
                    label="Logo"
                    name="logo"
                />

                <EditorField
                    v-model="form.description"
                    :error="form.errors.description"
                    label="Açıklama"
                    name="description"
                />

                <InputField
                    v-model="form.owner"
                    :error="form.errors.owner"
                    label="Yetkili"
                    name="owner"
                />

                <MaskField
                    v-model="form.phone"
                    :error="form.errors.phone"
                    label="Telefon"
                    mask="phone"
                    name="phone"
                />

                <InputField
                    v-model="form.email"
                    :error="form.errors.email"
                    label="E-Posta Adresi"
                    name="email"
                    type="email"
                />

                <TextareaField
                    v-model="form.address"
                    :error="form.errors.address"
                    label="Adres"
                    name="address"
                />

                <InputField
                    v-model="form.address_link"
                    :error="form.errors.address_link"
                    label="Konum linki"
                    name="address_link"
                />
            </template>
        </Card>

        <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
    </form>
</template>
