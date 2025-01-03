import { useRef } from 'react';
import type { CrepeEditorHandle } from '@/components/CrepeEditor';
import type { Project } from '../Types';
import { useProject } from '../../hooks/useProjects';
import  CrepeEditor  from '@/components/CrepeEditor'

type projectContentProps = {
    projectId: number;
    fields?: Array<keyof Project>;
};

const defaultFields: Array<keyof Project> = ['title', 'content'];

export const ProjectContent = ({ projectId, fields = defaultFields }: projectContentProps) => {

    const editorRef = useRef<CrepeEditorHandle>(null);
    const { projectData, isLoading, error } = useProject(projectId, fields);

    const handleSave = () => {
        const markdown = editorRef.current?.getMarkdown();
        console.log('Markdown content:', markdown);
        // Ici vous pouvez ajouter la logique pour sauvegarder le contenu
    };

    if (!projectData) {
        return <div>Aucune donnée disponible</div>;
    }

    return (

        <div className="project-content">
            <h2>{projectData.title}</h2>

            <CrepeEditor
                key={projectId}
                defaultValue={projectData.content}
                ref={editorRef}
            />

            <button onClick={handleSave}>save</button>
        </div>
    );

};
