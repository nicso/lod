import { useProject } from '../hooks/useProjects';
import { LoadingPlaceholder } from './LoadingPlaceholder';
import { ErrorMessage } from './ErrorMessage';
import { ProjectTitle } from './Project/ProjectTitle';
import { ProjectThumbnail } from './Project/ProjectThumbnail';
import { ProjectDates } from './Project/ProjectDates';
import { ProjectContent } from './Project/ProjectContent';
import { ProjectMeta } from './Project/ProjectMeta';
import { ProjectAuthor } from './Project/ProjectAuthor';
import type { Project } from './Types';
import { ProjectTags } from './Project/ProjectTags';
import './Project/project.css';
import Modal from "@/components/ui/Modal/Modal";


type ProjectCardProps = {
    projectId: number;
    fields?: Array<keyof Project>;
};

const defaultFields: Array<keyof Project> = ['title', 'author'];

export const ProjectCard = ({ projectId, fields = defaultFields }: ProjectCardProps) => {
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
    const modalContent = (
        <ProjectContent
        key={projectData.id}
        projectId={projectData.id}
        fields={[
            'title',
            'tags',
            'content',
            'thumbnail',
            'project_date',
            'last_modification_date',
            'viewcount',
            'is_featured',
            'author' ]}
            />
    );

    return (
        <Modal className="project-card" data={modalContent}>

                {projectData.tags && <ProjectTags tags={projectData.tags} />}
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


        </Modal>
    );
};
