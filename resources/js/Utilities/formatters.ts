export function currencyFormat(price: number | string | null): string {
    if (price !== 0 && !price) return '';

    if (typeof price === 'string') {
        price = Number.parseFloat(price);
    }

    return price.toLocaleString('tr-TR', {
        style: 'currency',
        currency: 'TRY',
        unitDisplay: 'short',
        maximumFractionDigits: 2,
    });
}

export function dateFormat(date: unknown): string {
    if (typeof date === 'string') {
        return new Date(date).toLocaleString('tr-TR', {dateStyle: 'long'});
    } else if (date instanceof Date) {
        return date.toLocaleString('tr-TR', {dateStyle: 'long'});
    }

    return '';
}

export function timeFormat(date: unknown): string {
    if (typeof date === 'string') {
        return new Date(date).toLocaleString('tr-TR', {timeStyle: 'short'});
    } else if (date instanceof Date) {
        return date.toLocaleString('tr-TR', {timeStyle: 'short'});
    }

    return '';
}

export function dateTimeFormat(date: unknown): string {
    if (typeof date === 'string') {
        return new Date(date).toLocaleString('tr-TR', {dateStyle: 'long', timeStyle: 'short'});
    } else if (date instanceof Date) {
        return date.toLocaleString('tr-TR', {dateStyle: 'long', timeStyle: 'short'});
    }

    return '';
}
