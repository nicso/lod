
'use client';

import { ProjectCard } from "@/components/ProjectCard";
import { useEffect, useState } from "react";
import { LoadingPlaceholder } from "@/components/LoadingPlaceholder";
import { ErrorMessage } from "@/components/ErrorMessage";
import { SearchInput } from "@/components/SearchInput";

interface Project {
    id: number;
    title: string;
    thumbnail?: string;
    viewcount: number;
    is_featured: boolean;
}

export default function Home() {
    const [projects, setProjects] = useState<Project[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchProjects = async () => {
            try {
                const response = await fetch('http://localhost:8000/api/projects');
                const data = await response.json();

                if (data.success) {
                    setProjects(data.projects);
                } else {
                    throw new Error(data.message || 'Erreur lors de la récupération des projets');
                }
            } catch (error) {
                setError(error instanceof Error ? error.message : 'Une erreur est survenue');
            } finally {
                setIsLoading(false);
            }
        };

        fetchProjects();
    }, []);

    if (isLoading) {
        return (
            <div className="container mx-auto p-8">
                <LoadingPlaceholder />
            </div>
        );
    }

    if (error) {
        return (
            <div className="container mx-auto p-8">
                <ErrorMessage message={error} />
            </div>
        );
    }

    return (
        <div className="main-container">
            <SearchInput />
            <div className="projects-wrapper">
                    {projects.map(project => (
                        <ProjectCard
                        key={project.id}
                        projectId={project.id}
                        fields={[
                            'title',
                            'tags',
                            'thumbnail',
                            'project_date',
                            'last_modification_date',
                            'viewcount',
                            'is_featured',
                            'author'
                        ]}
                        />
                    ))}
                    
            </div>
        </div>
    );
}
