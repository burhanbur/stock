export interface AuthUser {
    id: string;
    name: string;
    email: string;
}

export interface Notification {
    level: 'success' | 'error' | 'warning' | 'info';
    message: string;
}

export interface GlossaryTermData {
    slug: string;
    term: string;
    full_name: string | null;
    simple_definition: string;
}

export interface SharedPageProps {
    [key: string]: unknown;
    auth: {
        user: AuthUser | null;
    };
    flash: {
        notification: Notification | null;
    };
    glossaryTerms: Record<string, GlossaryTermData>;
}

declare global {
    const route: typeof import('ziggy-js').route;
}
