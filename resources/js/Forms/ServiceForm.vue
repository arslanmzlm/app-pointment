<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import ActivityCheckbox from '@/Components/Form/ActivityCheckbox.vue';
import InputField from '@/Components/Form/InputField.vue';
import NumberField from '@/Components/Form/NumberField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {ServiceFormType} from '@/types/form';
import {Hospital} from '@/types/model';

defineProps<{
    form: InertiaForm<ServiceFormType>;
    hospitals?: Hospital[];
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

                <InputField
                    v-model="form.name"
                    :error="form.errors.name"
                    label="Hizmet"
                    name="name"
                    required
                />

                <InputField
                    v-model="form.code"
                    :error="form.errors.code"
                    label="Hizmet Kodu"
                    name="code"
                />

                <NumberField
                    v-model="form.price"
                    :error="form.errors.price"
                    label="Fiyat"
                    mode="currency"
                    name="price"
                />

                <ActivityCheckbox v-model="form.active" />
            </template>
        </Card>

        <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
    </form>
</template>
