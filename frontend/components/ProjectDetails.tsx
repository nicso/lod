import { useProject } from '../hooks/useProjects';
import { LoadingPlaceholder } from './LoadingPlaceholder';
import { ErrorMessage } from './ErrorMessage';
import { ProjectTitle } from './ProjectTitle';
import { ProjectThumbnail } from './ProjectThumbnail';
import { ProjectDates } from './ProjectDates';
import { ProjectContent } from './ProjectContent';
import { ProjectMeta } from './ProjectMeta';
import { ProjectAuthor } from './ProjectAuthor';
import type { Project } from './Types';

import './project.css';


type ProjectDetailProps = {
    projectId: number;
    fields?: Array<keyof Project>;
};

const defaultFields: Array<keyof Project> = ['title', 'author'];

export const ProjectDetail = ({ projectId, fields = defaultFields }: ProjectDetailProps) => {
    const { projectData, isLoading, error } = useProject(projectId, fields);

    if (isLoading) {
        return <LoadingPlaceholder />;
    }
    
    if (error) {
        return <ErrorMessage message={error} />;
    }

    if (!projectData) {
        return <div>Aucune donnée disponible</div>;
    }

    return (
        <div className="project-detail ">
            {projectData.title && <ProjectTitle title={projectData.title} />}

            {projectData.author && <ProjectAuthor author={projectData.author} />}
            
            {projectData.thumbnail && (
                <ProjectThumbnail 
                    src={projectData.thumbnail} 
                    alt={projectData.title || ''} 
                />
            )}
            
            {/* <ProjectDates 
                projectDate={projectData.project_date}
                lastModificationDate={projectData.last_modification_date}
            /> */}
            
            {/* {projectData.content && <ProjectContent content={projectData.content} />} */}
            
            <ProjectMeta 
                viewcount={projectData.viewcount}
                isFeatured={projectData.is_featured}
            />
        </div>
    );
};