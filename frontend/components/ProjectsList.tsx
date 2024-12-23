// components/ProjectsList.tsx
'use client';

import { ProjectCard } from '@/components/ProjectCard';
import { Project } from '@/components/Types';
import { useCallback, useMemo } from 'react';

type ProjectsListProps = {
    projects: Project[];
    tagId: string;
};

export function ProjectsList({ projects, tagId }: ProjectsListProps) {
    const tagName = useMemo(() => {
        return projects[0]?.tags?.find(
            (t: {id: number}) => t.id === Number(tagId)
        )?.name || 'Inconnu';
    }, [projects, tagId]);

    const renderProjects = useCallback(() => {
        if (projects.length === 0) {
            return (
                <div className="text-center py-8">
                    Aucun projet trouvé avec ce tag
                </div>
            );
        }

        return (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {projects.map((project: Project) => (
                    <ProjectCard
                        key={project.id}
                        project={project}
                    />
                ))}
            </div>
        );
    }, [projects]);

    return (
        <>
            <h2 className="text-2xl font-bold mb-6">
                Projets avec le tag {tagName}
            </h2>
            {renderProjects()}
        </>
    );
}
