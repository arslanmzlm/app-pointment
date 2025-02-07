<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import UserForm from '@/Forms/UserForm.vue';
import {UserFormType} from '@/types/form';
import {Hospital} from '@/types/model';

defineProps<{
    hospitals?: Hospital[];
}>();

const breadcrumbs = [{label: 'Kullanıcılar', url: route('dashboard.user.list')}];

const form = useForm<UserFormType>({
    hospital_id: null,
    username: '',
    name: '',
    phone: '',
    email: '',
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('dashboard.user.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="Kullanıcı Oluştur">
        <UserForm :form :hospitals @submit.prevent="submit" />
    </DashboardLayout>
</template>
