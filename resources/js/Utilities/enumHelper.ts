import {AppointmentState, PaymentMethod, TransactionType, UserType} from '@/types/enums';

export function userType(method: UserType): {
    severity: string;
} {
    switch (method) {
        case UserType.PATIENT:
            return {
                severity: 'success',
            };
        case UserType.DOCTOR:
            return {
                severity: 'info',
            };
        case UserType.ADMIN:
            return {
                severity: 'danger',
            };
    }
}

export function transactionType(method: TransactionType): {
    severity: string;
    icon: string;
} {
    switch (method) {
        case TransactionType.INCOME:
            return {
                severity: 'success',
                icon: 'pi pi-arrow-down',
            };
        case TransactionType.EXPENSE:
            return {
                severity: 'danger',
                icon: 'pi pi-arrow-up',
            };
    }
}

export function paymentMethod(method: PaymentMethod): {
    severity: string;
    icon: string;
} {
    switch (method) {
        case PaymentMethod.CASH:
            return {
                severity: 'success',
                icon: 'pi pi-money-bill',
            };
        case PaymentMethod.CARD:
            return {
                severity: 'info',
                icon: 'pi pi-credit-card',
            };
        case PaymentMethod.TRANSFER:
            return {
                severity: 'warn',
                icon: 'pi pi-receipt',
            };
    }
}

export function appointmentState(state: AppointmentState): {
    color: string;
    class: string;
    severity: string;
    icon: string;
} {
    switch (state) {
        case AppointmentState.PENDING:
            return {
                color: 'var(--p-surface-500)',
                class: 'bg-secondary',
                severity: 'secondary',
                icon: 'pi pi-hourglass',
            };
        case AppointmentState.CONFIRMED:
            return {
                color: 'var(--p-sky-600)',
                class: 'bg-info',
                severity: 'info',
                icon: 'pi pi-calendar-clock',
            };
        case AppointmentState.COMPLETED:
            return {
                color: 'var(--p-green-600)',
                class: 'bg-success',
                severity: 'success',
                icon: 'pi pi-calendar',
            };
        case AppointmentState.RESCHEDULED:
            return {
                color: 'var(--p-orange-600)',
                class: 'bg-warn',
                severity: 'warn',
                icon: 'pi pi-calendar-plus',
            };
        case AppointmentState.CANCELED:
            return {
                color: 'var(--p-red-600)',
                class: 'bg-danger',
                severity: 'danger',
                icon: 'pi pi-calendar-times',
            };
    }
}
