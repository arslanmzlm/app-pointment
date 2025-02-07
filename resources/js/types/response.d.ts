import {FieldInput} from '@/types/enums';
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

export interface TransactionReport {
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
