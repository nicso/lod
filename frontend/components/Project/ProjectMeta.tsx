type ProjectMetaProps = {
    viewcount?: number;
    isFeatured?: boolean;
};

export const ProjectMeta = ({ viewcount, isFeatured }: ProjectMetaProps) => (
    <div className="project-meta">
        {viewcount && (
            <div className="project-views">
                {viewcount} views
            </div>
        )}
        {isFeatured && (
            <div className="project-featured">
                Featured
            </div>
        )}
    </div>
);
