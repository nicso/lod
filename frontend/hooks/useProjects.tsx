import { useState, useEffect } from 'react';
import { Project , ProjectResponse } from '../components/Types';

export const useProject = (projectId: number, fields: Array<keyof Project> = ['title']) => {
    const [projectData, setProjectData] = useState<Partial<Project> | null>(null);
    const [isLoading, setIsLoading] = useState<boolean>(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchProjectData = async () => {
            try {
                const response = await fetch(
                    `http://localhost:8000/api/projects/${projectId}?fields=${fields.join(',')}`
                );

                if (!response.ok) {
                    throw new Error('Erreur lors de la récupération des données');
                }

                const data: ProjectResponse = await response.json();

                if (!data.success || !data.project) {
                    throw new Error(data.message  || 'Données invalides');
                }

                setProjectData(data.project);
            } catch (err) {
                setError(err instanceof Error ? err.message : 'Une erreur est survenue');
            }finally {
                setIsLoading(false);
            }
        };
        fetchProjectData();
    }, [projectId, fields]);
    return { projectData, isLoading, error };
};