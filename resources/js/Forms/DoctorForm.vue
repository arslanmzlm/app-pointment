<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import EditorField from '@/Components/Form/EditorField.vue';
import ImageUploadField from '@/Components/Form/ImageUploadField.vue';
import InputField from '@/Components/Form/InputField.vue';
import MaskField from '@/Components/Form/MaskField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {DoctorFormType} from '@/types/form';
import {Hospital} from '@/types/model';

defineProps<{
    form: InertiaForm<DoctorFormType>;
    hospitals?: Hospital[];
    branches: string[];
    avatarSrc?: string | null;
}>();
</script>

<template>
    <form class="space-y-6">
        <Card>
            <template #content>
                <SelectField
                    v-if="hospitals && hospitals.length > 0 && form.hospital_id !== undefined"
                    v-model="form.hospital_id"
                    :options="hospitals"
                    filter
                    label="Hastane"
                    required
                />

                <div class="grid gap-6 lg:grid-cols-2">
                    <InputField
                        v-model="form.name"
                        :error="form.errors.name"
                        label="Ad"
                        name="name"
                        required
                    />

                    <InputField
                        v-model="form.surname"
                        :error="form.errors.surname"
                        label="Soyad"
                        name="surname"
                        required
                    />
                </div>

                <MaskField
                    v-model="form.phone"
                    :error="form.errors.phone"
                    label="Telefon"
                    mask="phone"
                    name="phone"
                    required
                />

                <SelectField
                    v-model="form.branch"
                    :error="form.errors.branch"
                    :option-label="null"
                    :option-value="null"
                    :options="branches"
                    editable
                    filter
                    label="Alan"
                    required
                />

                <InputField
                    v-model="form.title"
                    :error="form.errors.title"
                    label="Ünvan"
                    name="title"
                >
                    <template #message>
                        <p>
                            Doktorun isminin göründüğü randevu alma gibi alanlarda, doktorun ünvanı
                            tanımlanmışsa ünvanı görünecektir eğer yoksa doktorun adı görünecektir.
                        </p>
                    </template>
                </InputField>

                <ImageUploadField
                    v-model="form.avatar"
                    :default-img="avatarSrc"
                    :error="form.errors.avatar"
                    :max-file-size="5"
                    label="Avatar"
                    name="avatar"
                />

                <InputField
                    v-model="form.email"
                    :error="form.errors.email"
                    label="E-Posta Adresi"
                    name="email"
                    type="email"
                />

                <EditorField
                    v-model="form.resume"
                    :error="form.errors.resume"
                    label="Özgeçmiş"
                    name="resume"
                />

                <EditorField
                    v-model="form.certificate"
                    :error="form.errors.certificate"
                    label="Sertifika"
                    name="certificate"
                />
            </template>
        </Card>

        <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
    </form>
</template>
