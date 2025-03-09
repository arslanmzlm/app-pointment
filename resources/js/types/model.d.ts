import {
    AppointmentState,
    FieldInput,
    Gender,
    PaymentMethod,
    TransactionType,
    UserType,
} from '@/types/enums';

interface Model {
    id: number;
    created_at: string;
    updated_at: string;
}

export interface User extends Model {
    type: UserType;
    hospital_id: number | null;
    doctor_id: number | null;
    patient_id: number | null;
    username: string;
    name: string;
    phone: string;
    email: string;
    deleted_at: string | null;

    type_label: string;
}

export interface Province extends Model {
    name: string;
    code: number;
}

export interface Hospital extends Model {
    name: string;
    province_id: number;
    start_work: string;
    end_work: string;
    duration: number;
    disabled_days: number[];
    title: string | null;
    logo: string | null;
    description: string | null;
    owner: string | null;
    phone: string | null;
    email: string | null;
    address: string | null;
    address_link: string;

    logo_src: string | null;
}

export interface Doctor extends Model {
    hospital_id: number;
    name: string;
    surname: string;
    full_name: string;
    branch: string;
    title: string | null;
    phone: string;
    email: string | null;
    resume: string | null;
    certificate: string | null;
    deleted_at: string | null;

    hospital?: Hospital;
    avatar_src: string | null;
}

export interface Patient extends Model {
    province_id: number;
    old: boolean;
    name: string;
    surname: string;
    full_name: string;
    phone: string;
    email: string | null;
    gender: Gender;
    birthday: string | null;
    notification: boolean;
    created_by: number | null;
    deleted_at: string | null;

    gender_label: string;
    province?: Province;
}

export interface Field extends Model {
    name: string;
    input: FieldInput;
    description: string | null;
    order: number | null;

    values?: FieldValue[];
    input_label: string;
}

export interface FieldValue extends Model {
    field_id: number;
    value: string;
}

export interface Service extends Model {
    hospital_id: number;
    active: boolean;
    name: string;
    code: string | null;
    price: number | null;
}

export interface Product extends Model {
    active: boolean;
    category: string;
    brand: string;
    name: string;
    slug: string | null;
    code: string | null;
    price: number | null;
    description: string;

    stocks: ProductStock[];
    images: ProductImage[];
}

export interface ProductStock extends Model {
    hospital_id: number;
    product_id: number;
    stock: number;

    hospital_name: string;
}

export interface ProductImage extends Model {
    product_id: number;
    file: string;
    order: number;

    file_src: string;
}

export interface Treatment extends Model {
    user_id: number;
    hospital_id: number;
    doctor_id: number;
    patient_id: number;
    total: number;
    note: string | null;

    hospital?: Hospital;
    doctor: Doctor;
    patient: Patient;
    services: TreatmentService[];
    products: TreatmentProduct[];
}

export interface TreatmentService extends Model {
    treatment_id: number;
    service_id: number;
    price: number;

    service?: Service;
}

export interface TreatmentProduct extends Model {
    treatment_id: number;
    product_id: number;
    count: number;
    price: number;
    total: number;

    product?: Product;
}

export interface Transaction extends Model {
    type: TransactionType;
    method: PaymentMethod;
    total: number;
    user_id: number;
    doctor_id: number | null;
    patient_id: number | null;
    treatment_id: number | null;
    description: string | null;

    type_label: string;
    method_label: string;
    patient?: Patient;
}

export interface AppointmentType extends Model {
    name: string;
}

export interface Appointment extends Model {
    appointment_type_id: number;
    doctor_id: number;
    patient_id: number;
    state: AppointmentState;
    start_date: string;
    due_date: string;
    duration: number;
    title: string | null;
    note: string | null;
    treatment_id: number | null;

    type_name: string;
    state_label: string;
    hospital?: Hospital;
    doctor?: Doctor;
    patient?: Patient;
}

export interface PassiveDate extends Model {
    doctor_id: number;
    start_date: string;
    due_date: string;
    description: string | null;
}
