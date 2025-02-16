import {Toast} from '@/types/component';
import {Appointment, User} from '@/types/model';

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
    toasts: Toast[];
    activeAppointment: Appointment | null;
};
