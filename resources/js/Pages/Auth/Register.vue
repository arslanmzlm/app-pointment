<script lang="ts" setup>
import {Head, Link, useForm} from '@inertiajs/vue3';
import {Button} from 'primevue';
import GuestLayout from '@/Layouts/GuestAuthLayout.vue';
import InputField from '@/Components/Form/InputField.vue';

const form = useForm({
    username: '',
    name: '',
    phone: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('register.store'));
}
</script>

<template>
    <Head title="Kayıt Ol" />

    <GuestLayout>
        <div class="mx-auto bg-white p-10 shadow-md sm:max-w-xl sm:rounded-lg">
            <h2 class="sm:text-title-xl2 mb-9 text-2xl font-bold text-black dark:text-white">
                Kaydol
            </h2>

            <form class="space-y-6" @submit.prevent="submit">
                <InputField
                    v-model="form.username"
                    :error="form.errors.username"
                    class="col-span-full lg:col-span-1"
                    label="Kullanıcı Adı"
                    required
                />

                <InputField
                    v-model="form.name"
                    :error="form.errors.name"
                    class="col-span-full lg:col-span-1"
                    label="Ad"
                    required
                />

                <InputField
                    v-model="form.phone"
                    :error="form.errors.phone"
                    class="col-span-full lg:col-span-1"
                    label="Telefon Numarası"
                    required
                />

                <InputField
                    v-model="form.email"
                    :error="form.errors.email"
                    class="col-span-full lg:col-span-1"
                    label="E-Posta Adresi"
                    required
                    type="email"
                />

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

                <Button class="btn-block" type="submit">Üyeliği Oluştur</Button>

                <div class="mt-6 text-center">
                    <p class="font-medium">
                        Zaten hesabın var mı?
                        <Link :href="route('login')" class="text-primary">Giriş yap</Link>
                    </p>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
