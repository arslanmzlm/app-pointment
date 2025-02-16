<script lang="ts" setup>
import axios from 'axios';
import {trim} from 'lodash';
import {IconField, InputIcon, InputText} from 'primevue';
import {ref, watch} from 'vue';
import {patientLabel} from '@/Utilities/labels';
import {Patient} from '@/types/model';

const search = ref<string | null>();
const patients = ref<Patient[]>([]);
const focused = ref(false);

watch(search, getPatients);

function getPatients() {
    const val = search.value ? trim(search.value) : null;

    if (val && val.length > 2) {
        return axios
            .get(route('dashboard.patient.search'), {params: {search: val}})
            .then((response) => {
                if (response.data) {
                    patients.value = response.data;
                }
            });
    } else {
        patients.value = [];
    }
}

function handleFocusOut(event: FocusEvent) {
    const relatedTarget = event.relatedTarget as HTMLElement | null;
    if (!relatedTarget || !relatedTarget.closest('.patients')) {
        focused.value = false;
    }
}
</script>

<template>
    <div class="hidden sm:block">
        <form method="POST" @submit.prevent="getPatients()">
            <div class="relative">
                <IconField>
                    <InputIcon>
                        <span class="pi pi-search"></span>
                    </InputIcon>
                    <InputText
                        v-model.trim="search"
                        class="min-w-80"
                        placeholder="Arama yap"
                        size="small"
                        type="search"
                        @focusin="focused = true"
                        @focusout="handleFocusOut"
                    />
                </IconField>

                <div
                    v-if="focused && patients && patients.length > 0"
                    class="patients surface-default absolute left-0 top-full mt-2 flex flex-col gap-1"
                >
                    <Link
                        v-for="patient in patients"
                        :key="patient.id"
                        :href="route('dashboard.patient.show', {id: patient.id})"
                        class="rounded-sm px-4 py-3 hover:bg-surface-100 dark:hover:bg-surface-700"
                    >
                        {{ patientLabel(patient) }}
                    </Link>
                </div>
            </div>
        </form>
    </div>
</template>

<style>
.patients {
    width: max-content;
    max-width: 600px;
    max-height: 350px;
    overflow-y: auto;
}
</style>
