interface SidebarMenuItem {
    icon?: any;
    label: string;
    url?: string;
    active?: boolean;
    children?: {
        icon?: any;
        label: string;
        url: string;
        active?: boolean;
    }[];
}

interface SidebarMenuRow {
    name: string;
    items: SidebarMenuItem[];
}

interface DataTableFilter {
    [p: string]: {
        value: any;
        type?: 'number' | 'boolean' | 'date' | 'array' | 'array:number';
    };
}

export interface Toast {
    type: 'success' | 'error' | 'warn' | 'info';
    message: string;
}
