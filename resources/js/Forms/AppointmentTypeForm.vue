<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import InputField from '@/Components/Form/InputField.vue';
import {AppointmentTypeFormType} from '@/types/form';

defineProps<{
    form: InertiaForm<AppointmentTypeFormType>;
}>();
</script>

<template>
    <form class="space-y-6">
        <Card>
            <template #content>
                <InputField
                    v-model="form.name"
                    :error="form.errors.name"
                    label="Randevu Tipi"
                    name="name"
                    required
                />
            </template>
        </Card>

        <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
    </form>
</template>
