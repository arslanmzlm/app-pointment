import {Toast} from '@/types/component';
import {Appointment, Hospital, User} from '@/types/model';

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
        hospital: Hospital;
    };
    toasts: Toast[];
    activeAppointment: Appointment | null;
};
