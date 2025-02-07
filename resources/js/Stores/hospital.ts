import {defineStore} from 'pinia';
import {ref} from 'vue';
import {getInt} from '@/Utilities/parser';

export const useHospitalStore = defineStore('hospital', () => {
    const start_work = ref(9);
    const end_work = ref(18);
    const duration = ref(60);

    function update(data: {
        start_work?: number | string;
        end_work?: number | string;
        duration?: number | string;
    }) {
        start_work.value = <number>getInt(data.start_work, 9);
        end_work.value = <number>getInt(data.end_work, 18);
        duration.value = <number>getInt(data.duration, 60);
    }

    return {start_work, end_work, duration, update};
});
