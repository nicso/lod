type ProjectDatesProps = {
    projectDate?: string;
    lastModificationDate?: string;
};

export const ProjectDates = ({ projectDate, lastModificationDate }: ProjectDatesProps) => (
    <div className="dates mb-4">
        {projectDate && (
            <div className="text-sm text-zinc-500 mb-2">
                Date of publication : {new Date(projectDate).toLocaleDateString()}
            </div>
        )}
        {lastModificationDate && (
            <div className="text-sm text-zinc-500 mb-2">
                Last modification : {new Date(lastModificationDate).toLocaleDateString()}
            </div>
        )}
    </div>
);