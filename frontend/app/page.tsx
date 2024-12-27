'use client';

import { ProjectCard } from "@/components/ProjectCard";
import { useEffect, useState, useCallback } from "react";
import { LoadingPlaceholder } from "@/components/LoadingPlaceholder";
import { ErrorMessage } from "@/components/ErrorMessage";
import { SearchInput } from "@/components/ui/SearchInput";
import { TagFilter } from "@/components/TagFilter";
import debounce from 'lodash/debounce';

interface Project {
    id: number;
    title: string;
    thumbnail?: string;
    viewcount: number;
    is_featured: boolean;
}

export default function Home() {
    const [projects, setProjects] = useState<Project[]>([]);
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedTags, setSelectedTags] = useState<number[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [isSearching, setIsSearching] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const fetchProjects = async (search?: string, tags?: number[]) => {
        try {
            const queryParams = new URLSearchParams();
            if (search) {
                queryParams.append('search', search);
            }
            if (tags && tags.length > 0) {
                queryParams.append('tags', tags.join(','));
            }

            const response = await fetch(`http://localhost:8000/api/projects?${queryParams}`);
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
            setIsSearching(false);
        }
    };

    // Initial fetch
    useEffect(() => {
        fetchProjects();
    }, []);

    // Debounced search function
    const debouncedSearch = useCallback(
        debounce((search: string, tags: number[]) => {
            fetchProjects(search, tags);
        }, 300),
        []
    );

    // Handle search input change
    const handleSearch = (value: string) => {
        setSearchTerm(value);
        setIsSearching(true);
        debouncedSearch(value, selectedTags);
    };

    // Handle tags change
    const handleTagsChange = (tags: number[]) => {
        setSelectedTags(tags);
        setIsSearching(true);
        debouncedSearch(searchTerm, tags);
    };

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
            <div className="filter-section mb-8">
                <SearchInput
                    value={searchTerm}
                    onChange={handleSearch}
                    isLoading={isSearching}
                />
                <TagFilter
                    selectedTags={selectedTags}
                    onTagsChange={handleTagsChange}
                />
            </div>

            <div className="projects-wrapper">
                {projects.length === 0 ? (
                    <div className="text-center py-8 text-gray-500">
                        Aucun projet ne correspond à votre recherche
                    </div>
                ) : (
                    projects.map(project => (
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
                    ))
                )}
            </div>
        </div>
    );
}
