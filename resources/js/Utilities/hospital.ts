import {usePage} from '@inertiajs/vue3';

export function getDuration(def: number | null = null) {
    const hospital = usePage().props.auth.hospital;
    if (hospital) {
        return hospital.duration;
    }

    return def;
}

export function getStartWork(def: string | null = null) {
    const hospital = usePage().props.auth.hospital;
    if (hospital) {
        return hospital.start_work;
    }

    return def;
}

export function getEndWork(def: string | null = null) {
    const hospital = usePage().props.auth.hospital;
    if (hospital) {
        return hospital.end_work;
    }

    return def;
}
