<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import {Button, Card} from 'primevue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import EditorField from '@/Components/Form/EditorField.vue';
import MaskField from '@/Components/Form/MaskField.vue';
import {SettingFormType} from '@/types/form';
import {SettingsResponse} from '@/types/response';

const props = defineProps<{
    settings: SettingsResponse;
}>();

const form = useForm<SettingFormType>({
    contact_phone: props.settings.contact_phone ?? '',
    agreement_policy: props.settings.agreement_policy ?? '',
    privacy_policy: props.settings.privacy_policy ?? '',
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

                    <EditorField
                        v-model="form.agreement_policy"
                        :error="form.errors.agreement_policy"
                        label="Kullanım Şartları ve Koşulları"
                        name="agreement_policy"
                    />

                    <EditorField
                        v-model="form.privacy_policy"
                        :error="form.errors.privacy_policy"
                        label="Gizlilik Politikası"
                        name="privacy_policy"
                    />
                </template>
            </Card>

            <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
        </form>
    </DashboardLayout>
</template>
