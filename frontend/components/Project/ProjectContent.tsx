import type { Project } from '../Types';
import { useProject } from '../../hooks/useProjects';

import  CrepeEditor  from '@/components/CrepeEditor'

// import './style.css'


type projectContentProps = {
    projectId: number;
    fields?: Array<keyof Project>;
};

const defaultFields: Array<keyof Project> = ['title', 'content'];

export const ProjectContent = ({ projectId, fields = defaultFields }: projectContentProps) => {

    const { projectData, isLoading, error } = useProject(projectId, fields);
    if (!projectData) {
        return <div>Aucune donnée disponible</div>;
    }

    return (

        <div className="project-content">
            <h2>{projectData.title}</h2>

            <CrepeEditor key={projectId} defaultValue={projectData.content}/>
        </div>
    );

};
