<script lang="ts" setup>
import CheckboxField from '@/Components/Form/CheckboxField.vue';

const model = defineModel<boolean>({required: true});
</script>

<template>
    <div>
        <CheckboxField v-model="model" :value="true" label="Aktif" name="activity" />
        <p class="mt-2">
            <small
                >Kullanıcılar sadece aktif verileri görüntüleyebilir ve onlarla işlem yapabilir. Bu
                verinin tüm işlemlere açık olması için bu seçeneği seçiniz diğer durumda veri sadece
                yöneticilere görünecektir. Veri bütünlüğünü sağlamak için veriyi silmek yerine pasif
                hale getirebilirsiniz.</small
            >
        </p>
    </div>
</template>
