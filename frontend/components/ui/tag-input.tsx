import React, { useState, useEffect, useRef } from 'react';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { X } from 'lucide-react';

interface Tag {
  id: number;
  name: string;
}

interface TagInputProps {
  selectedTags: Tag[];
  onTagsChange: (tags: Tag[]) => void;
}

export function TagInput({ selectedTags, onTagsChange }: TagInputProps) {
  const [input, setInput] = useState('');
  const [suggestions, setSuggestions] = useState<Tag[]>([]);
  const [isLoading, setIsLoading] = useState(false);
  const [activeSuggestion, setActiveSuggestion] = useState(-1);
  const suggestionsRef = useRef<HTMLDivElement>(null);
  const inputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (input.trim()) {
      const fetchSuggestions = async () => {
        setIsLoading(true);
        try {
          const response = await fetch(`http://localhost:8000/api/tags/search?q=${encodeURIComponent(input)}`);
          const data = await response.json();
          if (data.success) {
            // Filter out already selected tags
            const filteredSuggestions = data.tags.filter(
              (tag: Tag) => !selectedTags.some(selected => selected.id === tag.id)
            );
            setSuggestions(filteredSuggestions);
          }
        } catch (error) {
          console.error('Error fetching tags:', error);
        } finally {
          setIsLoading(false);
        }
      };

      fetchSuggestions();
    } else {
      setSuggestions([]);
    }
  }, [input, selectedTags]);

  const createNewTag = async (tagName: string) => {
    try {
      const response = await fetch('http://localhost:8000/api/tags', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name: tagName }),
      });
      const data = await response.json();
      if (data.success) {
        return data.tag;
      }
    } catch (error) {
      console.error('Error creating tag:', error);
    }
    return null;
  };

  const addTag = async (tagName: string, existingTag?: Tag) => {
    if (!tagName.trim()) return;

    // Check if tag already exists in selection
    if (selectedTags.some(tag => tag.name.toLowerCase() === tagName.toLowerCase())) {
      setInput('');
      setSuggestions([]);
      return;
    }

    let newTag: Tag;
    if (existingTag) {
      newTag = existingTag;
    } else {
      const createdTag = await createNewTag(tagName);
      if (!createdTag) return;
      newTag = createdTag;
    }

    onTagsChange([...selectedTags, newTag]);
    setInput('');
    setSuggestions([]);
    setActiveSuggestion(-1);
  };

  const removeTag = (tagToRemove: Tag) => {
    onTagsChange(selectedTags.filter(tag => tag.id !== tagToRemove.id));
  };

  const handleKeyDown = (e: React.KeyboardEvent<HTMLInputElement>) => {
    if (e.key === 'Tab' && suggestions.length > 0 && activeSuggestion >= 0) {
      e.preventDefault();
      addTag(suggestions[activeSuggestion].name, suggestions[activeSuggestion]);
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (activeSuggestion >= 0 && suggestions[activeSuggestion]) {
        addTag(suggestions[activeSuggestion].name, suggestions[activeSuggestion]);
      } else if (input.trim()) {
        addTag(input);
      }
    } else if (e.key === 'ArrowDown') {
      e.preventDefault();
      setActiveSuggestion(prev =>
        prev < suggestions.length - 1 ? prev + 1 : prev
      );
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      setActiveSuggestion(prev => prev > 0 ? prev - 1 : -1);
    }
  };

  return (
    <div className="space-y-2">
      <div className="flex flex-wrap gap-2 mb-2">
        {selectedTags.map(tag => (
          <span
            key={tag.id}
            className="inline-flex items-center bg-primary/10 text-primary px-2 py-1 rounded-md text-sm"
          >
            {tag.name}
            <button
              type="button"
              onClick={() => removeTag(tag)}
              className="ml-1 text-primary hover:text-primary/80"
            >
              <X size={14} />
            </button>
          </span>
        ))}
      </div>

      <div className="relative">
        <Input
          ref={inputRef}
          type="text"
          value={input}
          onChange={e => {
            setInput(e.target.value);
            setActiveSuggestion(-1);
          }}
          onKeyDown={handleKeyDown}
          placeholder="Add tags..."
          className="w-full"
        />

        {suggestions.length > 0 && (
          <div
            ref={suggestionsRef}
            className="absolute z-10 w-full mt-1 bg-white border rounded-md shadow-lg max-h-48 overflow-auto"
          >
            {suggestions.map((suggestion, index) => (
              <div
                key={suggestion.id}
                className={`px-3 py-2 cursor-pointer ${
                  index === activeSuggestion
                    ? 'bg-primary/10'
                    : 'hover:bg-primary/5'
                }`}
                onClick={() => addTag(suggestion.name, suggestion)}
                onMouseEnter={() => setActiveSuggestion(index)}
              >
                {suggestion.name}
              </div>
            ))}
          </div>
        )}

        {isLoading && (
          <div className="absolute right-3 top-3 text-gray-400">
            Loading...
          </div>
        )}
      </div>
    </div>
  );
}
