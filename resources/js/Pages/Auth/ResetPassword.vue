<script lang="ts" setup>
import {Head, useForm} from '@inertiajs/vue3';
import {Button, Message} from 'primevue';
import GuestLayout from '@/Layouts/GuestAuthLayout.vue';
import InputField from '@/Components/Form/InputField.vue';

const props = defineProps<{
    email: string;
    token: string;
}>();

const form = useForm({
    email: props.email,
    token: props.token,
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('password.email'));
}
</script>

<template>
    <Head title="Giriş Yap" />

    <GuestLayout>
        <div class="mx-auto bg-white p-10 shadow-md sm:max-w-xl sm:rounded-lg">
            <h2 class="sm:text-title-xl2 mb-9 text-2xl font-bold text-black dark:text-white">
                Şifremi Sıfırla
            </h2>

            <form class="space-y-4" @submit.prevent="submit">
                <InputField
                    v-model="form.password"
                    :error="form.errors.password"
                    label="Şifre"
                    required
                    type="password"
                />

                <InputField
                    v-model="form.password_confirmation"
                    :error="form.errors.password_confirmation"
                    label="Şifre (Tekrar)"
                    required
                    type="password"
                />

                <Message
                    v-if="
                        form.hasErrors &&
                        !form.errors.password &&
                        !form.errors.password_confirmation
                    "
                    message="Şifre sıfırlama bağlantısı doğrulanamadı. Lütfen yeni bir bağlantı alınız."
                    severity="error"
                />

                <Button class="btn-block" type="submit">Şifreyi Sıfırla</Button>
            </form>
        </div>
    </GuestLayout>
</template>
