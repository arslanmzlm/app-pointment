import {parseInt} from 'lodash';

export function getInt(val: unknown, defaultValue: number | null = null): number | null {
    if (typeof val === 'string') {
        return parseInt(val);
    } else if (typeof val === 'number') {
        return val;
    }

    return defaultValue;
}

export function getBool(val: unknown, defaultValue: boolean | null = null): boolean | null {
    if (typeof val === 'string') {
        return ['1', 'on', 'yes', 'true'].includes(val);
    } else if (typeof val === 'number') {
        return val > 1;
    } else if (typeof val === 'boolean') {
        return val;
    }

    return defaultValue;
}
