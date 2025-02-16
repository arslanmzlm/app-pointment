<script lang="ts" setup>
import {camelCase, isArray} from 'lodash';
import {FileUpload} from 'primevue';
import {FileUploadRemoveEvent, FileUploadSelectEvent} from 'primevue/fileupload';
import {computed} from 'vue';
import FormField from '@/Components/Form/FormField.vue';

const props = withDefaults(
    defineProps<{
        name?: string;
        label?: string;
        error?: string | null;
        required?: boolean;
        defaultImg?: string | null;
        maxFileSize?: number;
        fileLimit?: number;
        multiple?: boolean;
    }>(),
    {
        preview: true,
        maxFileSize: 1,
        fileLimit: 1,
    },
);

const model = defineModel<object | null | Array<object>>({required: true});

const id = computed(() => {
    return props.name ? camelCase(`input ${props.name}`) : undefined;
});

const getMaxFileSize = computed(() => {
    return props.maxFileSize * 1024 * 1000;
});

function cleared() {
    model.value = isArray(model.value) ? [] : null;
}

function removed(event: FileUploadRemoveEvent) {
    add(event.files);
}

function selected(event: FileUploadSelectEvent) {
    add(event.files);
}

function add(files: any) {
    if (isArray(model.value)) {
        model.value = files;
    } else {
        model.value = files[0];
    }
}
</script>

<template>
    <FormField :error :for="id" :label :required>
        <FileUpload
            :id
            :file-limit
            :invalid="!!error"
            :max-file-size="getMaxFileSize"
            :multiple
            :preview-width="150"
            :show-upload-button="false"
            accept="image/*"
            @clear="cleared"
            @remove="removed"
            @select="selected"
        >
            <template #empty>
                <div class="flex flex-col items-center justify-center">
                    <img
                        v-if="defaultImg"
                        :src="defaultImg"
                        alt="Görsel önizlemesi"
                        class="max-w-80"
                    />
                    <span
                        v-else
                        class="pi pi-cloud-upload rounded-full border-2 p-8 text-4xl text-muted-color"
                    />
                    <p class="mb-0 mt-6">Yüklemek için dosyaları buraya sürükleyip bırakın.</p>
                </div>
            </template>
        </FileUpload>

        <template v-if="$slots.message" #message>
            <slot name="message"></slot>
        </template>
    </FormField>
</template>
