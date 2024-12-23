'use client';

import { ProjectCard } from '@/components/ProjectCard';
import { Project } from '@/components/Types';
import { useParams } from 'next/navigation';
import { useEffect, useState } from 'react';

export default function ProjectsByTagPage() {
    const params = useParams();
    const tagId = params.tagId as string;
    const [projects, setProjects] = useState<Project[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchProjects = async () => {
            if (!tagId) return;

            try {
                const response = await fetch(`http://localhost:8000/api/projects/tag/${tagId}`);
                if (!response.ok) throw new Error('Erreur de chargement');
                const data = await response.json();
                if (data.success) {
                    setProjects(data.projects);
                }
            } catch (err) {
                setError(err instanceof Error ? err.message : 'Erreur');
            } finally {
                setIsLoading(false);
            }
        };

        fetchProjects();
    }, [tagId]);

    if (isLoading) return <div>Chargement...</div>;
    if (error) return <div>Erreur: {error}</div>;

    return (
        <div className="container mx-auto px-4 py-8">
            <h2 className="text-2xl font-bold mb-6">
                Projets avec le tag {projects[0]?.tags?.find(t => t.id === Number(tagId))?.name}
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {projects.map(project => (
                    <ProjectCard key={project.id} project={project} />
                ))}
            </div>
            {projects.length === 0 && (
                <div className="text-center py-8">
                    Aucun projet trouvé avec ce tag
                </div>
            )}
        </div>
    );
}
