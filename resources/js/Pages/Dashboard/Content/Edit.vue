<script lang="ts" setup>
import {useForm} from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ContentForm from '@/Forms/ContentForm.vue';
import {ContentFormType} from '@/types/form';
import {Content} from '@/types/model';

const props = defineProps<{
    content: Content;
    sections: string[];
}>();

const content = props.content;
const breadcrumbs = [{label: 'İçerikler', url: route('dashboard.content.list')}];

const form = useForm<ContentFormType>({
    section: content.section,
    active: content.active,
    title: content.title,
    slug: content.slug,
    subtitle: content.subtitle,
    top_title: content.top_title,
    alt_title: content.alt_title,
    image: null,
    mobile_image: null,
    image_alt: content.image_alt,
    icon: null,
    order: content.order,
    link: content.link,
    link_label: content.link_label,
    description: content.description,
    body: content.body ?? '',
});

function submit() {
    form.post(route('dashboard.content.update', {id: content.id}));
}
</script>

<template>
    <DashboardLayout :breadcrumbs title="İçerik Düzenle">
        <ContentForm
            :form
            :icon-src="content.icon_src"
            :image-src="content.image_src"
            :mobile-image-src="content.mobile_image_src"
            :sections
            @submit.prevent="submit"
        />
    </DashboardLayout>
</template>
