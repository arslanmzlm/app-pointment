import {Toast} from '@/types/component';
import {User} from '@/types/model';

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
    toasts: Toast[];
};
