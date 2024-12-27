import React, { useState, useEffect, useRef } from 'react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Button } from '@/components/ui/button';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Label } from '@/components/ui/label';
import { TagInput } from '@/components/ui/tag-input';
import CrepeEditor, { CrepeEditorHandle } from '@/components/CrepeEditor';

const CreateProjectPage = () => {
  const editorRef = useRef<CrepeEditorHandle>(null);
  const [formData, setFormData] = useState({
    title: '',
    thumbnail: '',
    id_category: '',
    status: 0,
  });
  const [selectedTags, setSelectedTags] = useState([]);
  const [categories, setCategories] = useState([]);
  const [error, setError] = useState('');
  const [success, setSuccess] = useState(false);
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    const fetchCategories = async () => {
      try {
        const response = await fetch('http://localhost:8000/api/categories');
        if (!response.ok) throw new Error('Failed to fetch categories');
        const data = await response.json();
        setCategories(data.categories);
      } catch (err) {
        setError('Failed to load categories');
      }
    };

    fetchCategories();
  }, []);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    setError('');

    try {
      const currentDate = new Date().toISOString().slice(0, 19).replace('T', ' ');
      const categoryId = parseInt(formData.id_category, 10);

      if (isNaN(categoryId)) {
        throw new Error('Catégorie invalide');
      }

      // Récupérer le contenu Markdown depuis l'éditeur
      const markdownContent = editorRef.current?.getMarkdown() || '';

      const projectData = {
        title: formData.title,
        content: markdownContent, // Utiliser le contenu de l'éditeur Crepe
        thumbnail: formData.thumbnail || '',
        project_date: currentDate,
        last_modification_date: currentDate,
        viewcount: 0,
        is_featured: false,
        id_category: categoryId,
        status: parseInt(formData.status.toString(), 10),
        selectedTags: selectedTags
      };

      const response = await fetch('http://localhost:8000/api/projects', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(projectData),
        credentials: 'include'
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
      }

      const data = await response.json();

      if (data.success) {
        setSuccess(true);
        setFormData({
          title: '',
          thumbnail: '',
          id_category: '',
          status: 0,
        });
        setSelectedTags([]);
      } else {
        throw new Error(data.message || 'Erreur lors de la création du projet');
      }
    } catch (err) {
      console.error('Erreur:', err);
      setError(err instanceof Error ? err.message : 'Une erreur est survenue');
    } finally {
      setIsLoading(false);
    }
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
  };

  return (
    <div className="container mx-auto p-6 mt-20">
      <Card className="w-full max-w-4xl mx-auto">
        <CardHeader>
          <CardTitle>Create New Project</CardTitle>
        </CardHeader>
        <CardContent>
          <form onSubmit={handleSubmit} className="space-y-6">
            <div className="space-y-2">
              <Label htmlFor="title">Title</Label>
              <Input
                id="title"
                name="title"
                value={formData.title}
                onChange={handleInputChange}
                required
                className="w-full"
                placeholder="Project title"
              />
            </div>

            <div className="space-y-2">
              <Label>Content</Label>
              <div className="min-h-96 border rounded-md">
                <CrepeEditor ref={editorRef} defaultValue="" readonly={false} />
              </div>
            </div>

            <div className="space-y-2">
              <Label htmlFor="thumbnail">Thumbnail URL</Label>
              <Input
                id="thumbnail"
                name="thumbnail"
                value={formData.thumbnail}
                onChange={handleInputChange}
                className="w-full"
                placeholder="https://example.com/image.jpg"
              />
            </div>

            <div className="space-y-2">
              <Label htmlFor="category">Category</Label>
              <select
                id="category"
                name="id_category"
                value={formData.id_category}
                onChange={handleInputChange}
                required
                className="w-full p-2 border rounded-md"
              >
                <option value="">Select a category</option>
                {categories.map((category) => (
                  <option key={category.id} value={category.id}>
                    {category.name}
                  </option>
                ))}
              </select>
            </div>

            <div className="space-y-2">
              <Label htmlFor="status">Status</Label>
              <select
                id="status"
                name="status"
                value={formData.status}
                onChange={handleInputChange}
                className="w-full p-2 border rounded-md"
              >
                <option value={0}>Draft</option>
                <option value={1}>Published</option>
              </select>
            </div>

            <div className="space-y-2">
              <Label htmlFor="tags">Tags</Label>
              <TagInput
                selectedTags={selectedTags}
                onTagsChange={setSelectedTags}
              />
            </div>

            {error && (
              <Alert variant="destructive">
                <AlertDescription>{error}</AlertDescription>
              </Alert>
            )}

            {success && (
              <Alert>
                <AlertDescription>Project created successfully!</AlertDescription>
              </Alert>
            )}

            <Button
              type="submit"
              className="w-full"
              disabled={isLoading}
            >
              {isLoading ? 'Creating...' : 'Create Project'}
            </Button>
          </form>
        </CardContent>
      </Card>
    </div>
  );
};

export default CreateProjectPage;
