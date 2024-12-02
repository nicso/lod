type ProjectContentProps = {
    content: string;
};

export const ProjectContent = ({ content }: ProjectContentProps) => (
    <div className="prose max-w-none mb-4 project-content">
            {content}
    </div>
);