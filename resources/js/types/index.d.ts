export interface AuthUser {
    id: string;
    name: string;
    email: string;
}

export interface Notification {
    level: 'success' | 'error' | 'warning' | 'info';
    message: string;
}

export interface SharedPageProps {
    [key: string]: unknown;
    auth: {
        user: AuthUser | null;
    };
    flash: {
        notification: Notification | null;
    };
}

declare global {
    const route: typeof import('ziggy-js').route;
}
