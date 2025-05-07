import {AppointmentState} from '@/types/enums';
import {Appointment} from '@/types/model';

export function appointmentActive(appointment: Appointment) {
    return (
        appointment.state !== AppointmentState.COMPLETED &&
        appointment.state !== AppointmentState.CANCELED
    );
}
