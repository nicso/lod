type ProjectTitleProps = {
    title: string;
};

export const ProjectTitle = ({ title }: ProjectTitleProps) => (
    <h2 className="project-title">
        {title}
    </h2>
);
