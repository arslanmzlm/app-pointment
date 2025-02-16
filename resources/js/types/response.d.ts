import {AppointmentState, FieldInput} from '@/types/enums';
import {Model} from '@/types/model';

export interface PaginateResponse<TModel extends Model> {
    current_page: number | string;
    data: TModel[];
    first_page_url: string;
    from: number | string;
    last_page: number | string;
    last_page_url: string;
    links: {
        active?: boolean;
        label: string;
        url?: string;
    }[];
    next_page_url: string | null;
    path: string;
    per_page: number | string;
    prev_page_url: string | null;
    to: number | string;
    total: number | string;
}

export interface EnumResponse {
    name: string;
    label: string;
    value: number;
}

export interface FieldInputEnumResponse {
    name: string;
    label: string;
    value: number;
    hasValues: boolean;
}

export interface FieldValueResponse {
    id: number | null;
    field_id: number;
    value: any;
}

export interface PatientFieldResponse {
    name: string;
    input: FieldInput;
    value: any;
    order: number | null;
}

export interface TransactionReportItem {
    total: number;
    income: number;
    expense: number;
    income_cash: number;
    income_card: number;
    income_transfer: number;
    expense_cash: number;
    expense_card: number;
    expense_transfer: number;
}

export interface TransactionReportResponse {
    [hospital_id: number]: TransactionReportItem;
}

export interface PatientReportResponse {
    total: number;
    old_patient: number;
    registered_own: number;
    per_gender: {
        gender: number;
        count: number;
    }[];
    per_province: {
        province_id: number;
        count: number;
    }[];
}

export interface TreatmentReportResponse {
    [hospital_id: number]: {
        per_doctor: {
            doctor_id: number;
            full_name: string;
            branch: string;
            count: number;
            total: number;
        }[];
        per_service: {
            service_id: number;
            code: string;
            name: string;
            count: number;
            total: number | string;
        }[];
    };
}

export interface PerProductReportResponse {
    product_id: number;
    code: string;
    name: string;
    count: number;
    count_total: string;
    total: number;
}

export interface ProductStockResponse {
    hospital_id: number;
    hospital_name: string;
    stock: number | null;
}

export interface AppointmentReportResponse {
    per_state: {
        state: AppointmentState;
        count: number;
        state_label: string;
    }[];
    per_patient: number;
    per_doctor: {
        [hospital_id: number]: {
            [doctor_id: number]: {
                hospital_id: number;
                doctor_id: number;
                state: AppointmentState;
                full_name: string;
                branch: string;
                count: number;
                state_label: string;
            }[];
        }[];
    };
}
