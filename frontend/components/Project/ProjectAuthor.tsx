type ProjectAuthorProps = {
    author: {
        userName: string;
        profile_picture?: string | null;
    };
};

export const ProjectAuthor = ({ author }: ProjectAuthorProps) => (
    <>
        <div className="project-author-container">

        {author.profile_picture && (
            <img
            src={author.profile_picture}
            alt={author.userName}
            className="project-avatar"
            />
        )}
        <span className="project-author">
            By {author.userName}
        </span>
        </div>
    </>
);
