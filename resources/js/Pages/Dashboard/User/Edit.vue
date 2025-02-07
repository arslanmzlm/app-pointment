<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import {computed} from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import UserForm from '@/Forms/UserForm.vue';
import {UserFormType} from '@/types/form';
import {Hospital, User} from '@/types/model';

const props = defineProps<{
    user: User;
    hospitals: Hospital[];
}>();

const user = props.user;
const pageTitle = computed(() => `Kullanıcıyı Düzenle #${user.id}`);
const breadcrumbs = [{label: 'Kullanıcılar', url: route('dashboard.user.list')}];

const form = useForm<UserFormType>({
    hospital_id: user.hospital_id,
    username: user.username,
    name: user.name,
    phone: user.phone,
    email: user.email,
    password: '',
    password_confirmation: '',
});

function submit() {
    form.post(route('dashboard.user.update', {id: user.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs :title="pageTitle">
        <UserForm :form :hospitals @submit.prevent="submit" />
    </DashboardLayout>
</template>
