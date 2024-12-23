'use client';

import { useEffect, useState } from 'react';
import { ProjectCard } from '@/components/ProjectCard';
import { LoadingPlaceholder } from '@/components/LoadingPlaceholder';
import { ErrorMessage } from '@/components/ErrorMessage';
import { use } from 'react';

interface Project {
    id: number;
    title: string;
    thumbnail?: string;
    viewcount: number;
    is_featured: boolean;
}

// Ajoutez cette interface pour les props
interface TagPageProps {
    params: {
        tagId: string;
    };
}

// Modifiez la signature de la fonction pour accepter les props
export default function TagPage({ params }: TagPageProps) {
    const [projects, setProjects] = useState<Project[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [tagName, setTagName] = useState<string>('');

    const resolvedParams = use(params);
    const tagId  = resolvedParams.tagId;

    useEffect(() => {
        const fetchProjectsByTag = async () => {
            if (!tagId) return;

            setIsLoading(true);
            setError(null);

            try {
                const response = await fetch(`http://localhost:8000/api/projects/tag/${tagId}`);
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Une erreur est survenue');
                }

                if (data.success) {
                    setProjects(data.projects);
                    if (data.tagName) {
                        setTagName(data.tagName);
                    }
                }
            } catch (error) {
                setError(error instanceof Error ? error.message : 'Une erreur est survenue');
                console.error('Erreur lors de la récupération des projets:', error);
            } finally {
                setIsLoading(false);
            }
        };

        fetchProjectsByTag();
    }, [tagId]);

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

    if (projects.length === 0) {
        return (
            <div className="container mx-auto p-8">
                <div className="text-center text-gray-500">
                    Aucun projet trouvé pour ce tag
                </div>
            </div>
        );
    }

    return (
        <div className="container mx-auto p-8">
            <h1 className="text-2xl font-bold mb-8">
                {tagName ? `Projets avec le tag "${tagName}"` : 'Projets'}
                <span className="text-gray-500 text-sm ml-2">
                    ({projects.length} projet{projects.length > 1 ? 's' : ''})
                </span>
            </h1>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {projects.map(project => (
                    <ProjectCard
                        key={project.id}
                        projectId={project.id}
                        fields={[
                            'title',
                            'tags',
                            'thumbnail',
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
