import {FieldInput, Gender, PaymentMethod, TransactionType} from '@/types/enums';

interface FormDataType {
    [key: string]: any;
}

export interface UserFormType extends FormDataType {
    hospital_id?: number | null;
    username: string;
    name: string;
    phone: string;
    email: string | null;
    password: string;
    password_confirmation: string;
}

export interface HospitalFormType extends FormDataType {
    province_id: number;
    name: string;
    start_work: number;
    end_work: number;
    duration: number;
    title: string | null;
    logo: File | null;
    description: string;
    owner: string | null;
    phone: string;
    email: string | null;
    address: string | null;
    address_link: string | null;
}

export interface DoctorFormType extends FormDataType {
    hospital_id?: number;
    name: string;
    surname: string;
    branch: string;
    avatar: File | null;
    title: string | null;
    phone: string;
    email: string | null;
    resume: string;
    certificate: string;
}

export interface PatientFormType extends FormDataType {
    old: boolean;
    province_id: number;
    name: string;
    surname: string;
    phone: string;
    email: string | null;
    gender: Gender;
    birthday: Date | null;
    notification: boolean;
    fields: {
        id: number | null;
        field_id: number;
        value: any;
    }[];
}

export interface FieldFormType extends FormDataType {
    name: string;
    input: FieldInput;
    description: string | null;
    order: number | null;
    values: {id: number | null; field_id?: number; value: string}[];
}

export interface ServiceFormType extends FormDataType {
    hospital_id?: number;
    active: boolean;
    name: string;
    code: string | null;
    price: number | null;
}

export interface ProductFormType extends FormDataType {
    hospital_id?: number;
    active: boolean;
    category: string;
    name: string;
    code: string | null;
    stock: number | null;
    price: number | null;
}

export interface TreatmentServiceFormType {
    id?: number;
    service_id: number;
    label?: string;
    price: number;
}

export interface TreatmentProductFormType {
    id?: number;
    product_id: number;
    label?: string;
    count: number;
    price: number;
    total?: number;
}

export interface AppointmentItemFormType extends FormDataType {
    start_date: Date | null;
    duration: number;
    title: string | null;
}

export interface TreatmentFormType extends FormDataType {
    doctor_id?: number;
    patient_id?: number;
    note: string | null;
    services: TreatmentServiceFormType[];
    products: TreatmentProductFormType[];
    appointments?: AppointmentItemFormType[];
    payment_method: PaymentMethod;
}

export interface TransactionFormType extends FormDataType {
    hospital_id?: number;
    type: TransactionType;
    method: PaymentMethod;
    total: number | null;
    patient_id?: number | null;
    description: string | null;
}

export interface PassiveDateFromType extends FormDataType {
    doctor_id?: number;
    start_date: Date | null;
    due_date: Date | null;
    description: string | null;
}

export interface AppointmentFormType extends FormDataType {
    patient_id?: number;
    start_date: Date | null;
    duration: number;
    title: string | null;
    note: string | null;
}

export interface AppointmentMultipleFormType extends FormDataType {
    patient_id: number;
    appointments: AppointmentItemFormType[];
}
