<script lang="ts" setup>
import {Head, Link, useForm} from '@inertiajs/vue3';
import axios from 'axios';
import {isObject} from 'lodash';
import {Button} from 'primevue';
import {useHospitalStore} from '@/Stores/hospital';
import GuestAuthLayout from '@/Layouts/GuestAuthLayout.vue';
import InputField from '@/Components/Form/InputField.vue';

const form = useForm({
    username: '',
    password: '',
});

const hospitalStore = useHospitalStore();

function submit() {
    form.post(route('login.store'), {
        onSuccess: () => {
            axios.get(route('dashboard.hospital.info')).then((response) => {
                console.log(response.data);
                if (response.data !== null && isObject(response.data)) {
                    hospitalStore.update(response.data);
                }
            });
        },
    });
}
</script>

<template>
    <Head title="Giriş Yap" />

    <GuestAuthLayout>
        <div class="mx-auto w-full max-w-xl bg-white p-10 shadow-md sm:rounded-lg">
            <h2 class="mb-9 text-2xl font-bold text-black dark:text-white">Giriş Yap</h2>

            <form class="grid grid-cols-1 gap-4" @submit.prevent="submit">
                <InputField
                    v-model="form.username"
                    :error="form.errors.username"
                    label="Kullanıcı Adı"
                    name="username"
                />

                <InputField
                    v-model="form.password"
                    :error="form.errors.password"
                    label="Şifre"
                    name="password"
                    type="password"
                />

                <div v-if="false">
                    Şifreni mi unuttun?
                    <Link :href="route('password.request')" class="text-primary hover:underline">
                        Şifreni sıfırla
                    </Link>
                </div>

                <Button type="submit">Giriş Yap</Button>

                <div v-if="false" class="mt-2 text-center">
                    <p class="text-surface-700">
                        Hesabın yok mu?
                        <Link :href="route('register')" class="text-primary hover:underline"
                            >Hemen kaydol
                        </Link>
                    </p>
                </div>
            </form>
        </div>
    </GuestAuthLayout>
</template>
