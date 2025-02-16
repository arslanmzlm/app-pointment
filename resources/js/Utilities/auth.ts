import {usePage} from '@inertiajs/vue3';

export function isDoctor(): boolean {
    return usePage().props.auth.user.doctor_id !== null;
}
