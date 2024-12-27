import { Loader2 } from 'lucide-react';

interface SearchInputProps {
    value: string;
    onChange: (value: string) => void;
    isLoading?: boolean;
    placeholder?: string;
}

export const SearchInput = ({
    value,
    onChange,
    isLoading = false,
    placeholder = "Rechercher un projet"
}: SearchInputProps) => {
    return (
        <div className="flex items-center gap-2 mb-4 relative search-input">
            <input
                type="text"
                value={value}
                onChange={(e) => onChange(e.target.value)}
                placeholder={placeholder}
                className="p-2"
            />
            {isLoading && (
                <div className="absolute right-3">
                    <Loader2 className="h-4 w-4 animate-spin text-gray-500" />
                </div>
            )}
        </div>
    );
};
