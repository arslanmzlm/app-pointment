<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {Button, Card, Message} from 'primevue';
import InputField from '@/Components/Form/InputField.vue';
import MaskField from '@/Components/Form/MaskField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {UserFormType} from '@/types/form';
import {Hospital} from '@/types/model';

defineProps<{
    form: InertiaForm<UserFormType>;
    hospitals?: Hospital[];
}>();
</script>

<template>
    <form class="space-y-6">
        <Message severity="warn">
            Bu alanda oluşturulan kullanıcıların yönetim alanına erişimleri olacaktır, bundan dolayı
            burada oluşturulan kullanıcıları hastalarla vs. paylaşmayınız. Podolog/hasta için
            kullanıcı oluşturmak istiyorsanız yeni podolog/hasta kaydedildiğinde otomatik olarak
            kullanıcı oluşturulmakta. Otomatik oluşturulan kullanıcıların, kullanıcı adı olarak
            telefon numarası, şifre olarak telefon numarasının son 6 hanesi tanımlanır.
        </Message>

        <Message severity="info">
            Kullanıcı tanımlanasına örnek olarak kaydedilen 0 512 345 67 89 telefon numarası için
            kullanıcı adı 5123456789 şifre ise 456789 olarak tanımlanır.
        </Message>

        <Card>
            <template #content>
                <SelectField
                    v-if="hospitals && hospitals.length > 0"
                    v-model="form.hospital_id"
                    :error="form.errors.hospital_id"
                    :options="hospitals"
                    label="Hastane"
                    placeholder="Genel yönetici"
                >
                    <template #message>
                        <p>
                            Eğer kullanıcıya hastane tanımlarsanız, sadece tanımlı hastanenin
                            verileri görüntüleyebilecek ve o verilerle işlem yapabilecek. Eğer
                            hastane tanımlamazsanız, tüm hastaneleri görüntüleyebilecek ve tüm
                            verilerle işlem yapabilecek.
                        </p>
                    </template>
                </SelectField>

                <InputField
                    v-model="form.username"
                    :error="form.errors.username"
                    label="Kullanıcı Adı"
                    name="username"
                >
                    <template #message>
                        <p>
                            Eğer kullanıcı adını boş bırakırsanız telefon numarası kullanıcı adı
                            olarak tanımlanacaktır.
                        </p>
                    </template>
                </InputField>

                <InputField
                    v-model="form.name"
                    :error="form.errors.name"
                    label="Ad Soyad"
                    name="name"
                    required
                />

                <MaskField
                    v-model="form.phone"
                    :error="form.errors.phone"
                    label="Telefon"
                    mask="phone"
                    name="phone"
                    required
                />

                <InputField
                    v-model="form.email"
                    :error="form.errors.email"
                    label="E-Posta Adresi"
                    name="email"
                    type="email"
                />

                <InputField
                    v-model="form.password"
                    :error="form.errors.password"
                    label="Şifre"
                    name="password"
                    required
                    type="password"
                />

                <InputField
                    v-model="form.password_confirmation"
                    :error="form.errors.password_confirmation"
                    label="Şifre (tekrar)"
                    name="password_confirmation"
                    required
                    type="password"
                />
            </template>
        </Card>

        <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
    </form>
</template>
