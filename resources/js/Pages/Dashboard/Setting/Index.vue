<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import MaskField from '@/Components/Form/MaskField.vue';
import {SettingFormType} from '@/types/form';
import {SettingsResponse} from '@/types/response';

const props = defineProps<{
    settings: SettingsResponse;
}>();

const form = useForm<SettingFormType>({
    contact_phone: props.settings.contact_phone ?? '',
});

function submit() {
    form.post(route('dashboard.setting.update'));
}
</script>

<template>
    <DashboardLayout title="Ayarlar">
        <form class="space-y-6" @submit.prevent="submit">
            <Card>
                <template #content>
                    <MaskField
                        v-model="form.contact_phone"
                        :error="form.errors.contact_phone"
                        label="Telefon"
                        mask="phone"
                        name="contact_phone"
                        required
                    >
                        <template #message>
                            <p>Uygulamada kullanıcıların yönlendirildiği telefon numarası.</p>
                        </template>
                    </MaskField>
                </template>
            </Card>

            <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
        </form>
    </DashboardLayout>
</template>
