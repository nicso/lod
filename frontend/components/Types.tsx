export type User = {
    id: number;
    userName: string;
    firstName?: string;
    lastName?: string;
    profile_picture?: string;
    email: string;
};

export type Project = {
    id: number;
    title: string;
    content: string;
    tags: string[];
    thumbnail: string | null;
    project_date: string;
    last_modification_date: string;
    viewcount: number;
    is_featured: boolean;
    id_category: number;
    status: number;
    author?: {
        id: number;
        userName: string;
        firstName?: string | null;
        lastName?: string | null;
        profile_picture?: string | null;
        email: string;
    };
};

export type ProjectResponse = {
    success: boolean;
    project?: Project;
    message?: string;
};
