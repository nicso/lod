import { useState, useEffect } from 'react';
import { Badge } from "@/components/ui/badge";
import { X } from 'lucide-react';

interface Tag {
    id: number;
    name: string;
}

interface TagFilterProps {
    selectedTags: number[];
    onTagsChange: (tags: number[]) => void;
}

export const TagFilter = ({ selectedTags, onTagsChange }: TagFilterProps) => {
    const [tags, setTags] = useState<Tag[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const fetchTags = async () => {
            try {
                const response = await fetch('http://localhost:8000/api/tags');
                const data = await response.json();

                if (data.success) {
                    setTags(data.tags);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                setError(error instanceof Error ? error.message : 'Erreur lors de la récupération des tags');
            } finally {
                setIsLoading(false);
            }
        };

        fetchTags();
    }, []);

    const toggleTag = (tagId: number) => {
        if (selectedTags.includes(tagId)) {
            onTagsChange(selectedTags.filter(id => id !== tagId));
        } else {
            onTagsChange([...selectedTags, tagId]);
        }
    };

    if (isLoading) return <div>Chargement des tags...</div>;
    if (error) return <div>Erreur: {error}</div>;

    return (
        <div className="flex flex-wrap gap-2 mb-4 tags-filter">
            {tags.map(tag => (
                <Badge
                    key={tag.id}
                    variant={selectedTags.includes(tag.id) ? "default" : "outline"}
                    className="cursor-pointer hover:bg-primary/80"
                    onClick={() => toggleTag(tag.id)}
                >
                    {tag.name}
                    {selectedTags.includes(tag.id) && (
                        <X className="ml-1 h-3 w-3" onClick={(e) => {
                            e.stopPropagation();
                            toggleTag(tag.id);
                        }} />
                    )}
                </Badge>
            ))}
        </div>
    );
};
