export enum UserType {
    PATIENT = 1,
    DOCTOR = 2,
    ADMIN = 3,
}

export enum Gender {
    MALE = 1,
    FEMALE = 2,
}

export enum FieldInput {
    TEXT = 1,
    NUMBER = 2,
    DATE = 3,
    SELECT = 4,
    RADIO = 5,
    CHECKBOX = 6,
    RADIO_TEXT = 7,
}

export enum PaymentMethod {
    CASH = 1,
    CARD = 2,
    TRANSFER = 3,
}

export enum TransactionType {
    INCOME = 1,
    EXPENSE = 2,
}

export enum AppointmentState {
    PENDING = 1,
    CONFIRMED = 2,
    COMPLETED = 3,
    RESCHEDULED = 4,
    CANCELED = 5,
}
