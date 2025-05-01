<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ContentForm from '@/Forms/ContentForm.vue';
import {ContentFormType} from '@/types/form';

defineProps<{
    sections: string[];
}>();

const breadcrumbs = [{label: 'İçerikler', url: route('dashboard.content.list')}];

const form = useForm<ContentFormType>({
    section: '',
    active: false,
    title: '',
    slug: null,
    subtitle: null,
    top_title: null,
    alt_title: null,
    image: null,
    mobile_image: null,
    image_alt: null,
    icon: null,
    order: null,
    link: null,
    link_label: null,
    description: '',
    body: '',
});

function submit() {
    form.post(route('dashboard.content.store'));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="İçerik Oluştur">
        <ContentForm :form :sections @submit.prevent="submit" />
    </DashboardLayout>
</template>
