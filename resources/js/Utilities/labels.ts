import {Patient, Product, Service} from '@/types/model';

export function serviceLabel(service: Service) {
    let label = service.name;
    if (service.code) label = `${service.code} - ${label}`;
    return label;
}

export function productLabel(product: Product) {
    let label = product.name;
    if (product.code) label = `${product.code} - ${label}`;
    return label;
}

export function patientLabel(patient: Patient) {
    return `${patient.full_name} - ${patient.phone}`;
}
