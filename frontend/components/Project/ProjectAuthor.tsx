type ProjectAuthorProps = {
    author: {
        userName: string;
        profile_picture?: string | null;
    };
};

export const ProjectAuthor = ({ author }: ProjectAuthorProps) => (
    <>
        {author.profile_picture && (
            <img
                src={author.profile_picture}
                alt={author.userName}
                className="w-8 h-8 rounded-full project-avatar"
            />
        )}
        <span className="text-sm text-zinc-200 project-author">
            By {author.userName}
        </span>
    </>
);
