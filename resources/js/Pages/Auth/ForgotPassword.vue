<script lang="ts" setup>
import {Head, Link, useForm} from '@inertiajs/vue3';
import {Button} from 'primevue';
import GuestLayout from '@/Layouts/GuestAuthLayout.vue';
import InputField from '@/Components/Form/InputField.vue';

const form = useForm({
    email: '',
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
                    v-model="form.email"
                    :error="form.errors.email"
                    class="col-span-full lg:col-span-1"
                    label="Kullanıcı Adı veya Telefon Numarası"
                    type="email"
                />

                <Button class="btn-block" type="submit">Sıfırlama Bağlantısı Gönder</Button>

                <div class="mt-6 text-center">
                    <Link :href="route('login')" class="font-medium text-primary hover:underline"
                        >Giriş ekranına dön
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
