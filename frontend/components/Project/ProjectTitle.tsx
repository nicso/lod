type ProjectTitleProps = {
    title: string;
};

export const ProjectTitle = ({ title }: ProjectTitleProps) => (
    <h2 className="font-bold text-2xl mb-4 project-title">
        {title}
    </h2>
);