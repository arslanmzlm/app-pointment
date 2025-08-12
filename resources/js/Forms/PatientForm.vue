<script lang="ts" setup>
import {InertiaForm} from '@inertiajs/vue3';
import {Button, Card, Message} from 'primevue';
import FieldInput from '@/Components/Field/FieldInput.vue';
import CheckboxField from '@/Components/Form/CheckboxField.vue';
import DateField from '@/Components/Form/DateField.vue';
import InputField from '@/Components/Form/InputField.vue';
import MaskField from '@/Components/Form/MaskField.vue';
import SelectField from '@/Components/Form/SelectField.vue';
import {PatientFormType} from '@/types/form';
import {Field, Province} from '@/types/model';
import {EnumResponse} from '@/types/response';

const props = defineProps<{
    form: InertiaForm<PatientFormType>;
    provinces: Province[];
    genders: EnumResponse[];
    fields: Field[];
}>();

function getFieldIndex(field_id: number) {
    return props.form.fields.findIndex((item) => item.field_id === field_id);
}

function getFieldError(field_id: number) {
    const index = props.form.fields.findIndex((field) => {
        return field.field_id === field_id;
    });

    return props.form.errors[`fields.${index}.value`] ?? null;
}
</script>

<template>
    <form class="space-y-6">
        <Message severity="info">
            Hasta kaydedildiğinde otomatik olarak kullanıcı oluşturulmakta. Otomatik oluşturulan
            kullanıcıya, kullanıcı adı olarak telefon numarası, şifre olarak telefon numarasının son
            6 hanesi tanımlanır.
        </Message>

        <Message severity="info">
            Kullanıcı tanımlanasına örnek olarak kaydedilen 0 512 345 67 89 telefon numarası için
            kullanıcı adı 5123456789 şifre ise 456789 olarak tanımlanır.
        </Message>

        <Message severity="warn">
            Hasta bilgisi düzenlendiğinde hastanın kullanıcı bilgileri otomatik güncellenmektedir.
        </Message>

        <Card>
            <template #content>
                <SelectField
                    v-model="form.province_id"
                    :error="form.errors.province_id"
                    :options="provinces"
                    label="Şehir"
                    required
                />

                <div class="grid grid-cols-2 gap-6">
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
                />

                <div>
                    <InputField
                        v-model="form.contact_phone"
                        :error="form.errors.contact_phone"
                        label="İkincil Telefon"
                        mask="contact_phone"
                        name="contact_phone"
                    />

                    <Message class="mt-3" severity="error">
                        Yurt dışı veya Veli numaralarını bu alana giriniz. Yurt dışı numaralarında
                        ülke kodu ile başlamalı, yurt içi numaralarında ise 0 ile başlamalı.
                    </Message>
                </div>

                <InputField
                    v-model="form.email"
                    :error="form.errors.email"
                    label="E-Posta Adresi"
                    name="email"
                    type="email"
                />

                <SelectField
                    v-model="form.gender"
                    :error="form.errors.gender"
                    :options="genders"
                    label="Cinsiyet"
                    option-label="label"
                    option-value="value"
                />

                <DateField
                    v-model="form.birthday"
                    :error="form.errors.birthday"
                    date-format="d MM yy"
                    label="Doğum Günü"
                />

                <CheckboxField v-model="form.old" :value="true" label="Eski Kayıt" name="old" />

                <div>
                    <CheckboxField
                        v-model="form.notification"
                        :value="true"
                        label="Bildirim"
                        name="notification"
                    />
                    <p class="mt-2">
                        SMS bildirimi almak istemeyen hastalar için bu tiki kaldırınız.
                    </p>
                </div>
            </template>
        </Card>

        <Card v-if="form.fields && form.fields.length">
            <template #content>
                <div class="space-y-4">
                    <FieldInput
                        v-for="(field, index) in fields"
                        :key="index"
                        v-model="form.fields[getFieldIndex(field.id)].value"
                        :error="getFieldError(field.id)"
                        :field
                    />
                </div>
            </template>
        </Card>

        <Button :loading="form.processing" class="btn-block" label="Kaydet" type="submit" />
    </form>
</template>
