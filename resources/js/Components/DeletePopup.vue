<script lang="ts" setup>
import {router} from '@inertiajs/vue3';
import {Button, useConfirm} from 'primevue';

const props = defineProps<{
    url: string;
}>();

const confirm = useConfirm();

const showConfirm = (event: Event) => {
    confirm.require({
        target: <HTMLElement>event.currentTarget,
        message:
            'Silme işlemi, bu veri ile bağlantılı olan verileride silecektir ve bu işlem geri alınamaz. Silmek istediğinizden emin misiniz?',
        icon: 'pi pi-exclamation-triangle',
        position: 'bottomright',
        rejectProps: {
            label: 'İptal',
            severity: 'secondary',
            outlined: true,
        },
        acceptProps: {
            label: 'Sil',
            severity: 'danger',
        },
        accept: () => {
            router.post(props.url);
        },
    });
};
</script>

<template>
    <Button
        class="size-12"
        icon="pi pi-trash"
        severity="danger"
        title="Sil"
        @click="showConfirm($event)"
    />
</template>
