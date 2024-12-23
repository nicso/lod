type ProjectMetaProps = {
    viewcount?: number;
    isFeatured?: boolean;
};

export const ProjectMeta = ({ viewcount, isFeatured }: ProjectMetaProps) => (
    <div className="flex flex-wrap gap-2 mt-4 project-meta">
        {viewcount && (
            <div className="tag bg-zinc-800 text-zinc-500 px-2 py-1 rounded-lg project-tag">
                {viewcount} views
            </div>
        )}
        {isFeatured && (
            <div className="tag bg-zinc-800 text-zinc-500 px-2 py-1 rounded-lg project-tag">
                Featured
            </div>
        )}
    </div>
);
