type ProjectAuthorProps = {
    author: {
        userName: string;
        profile_picture?: string | null;
    };
};

export const ProjectAuthor = ({ author }: ProjectAuthorProps) => (
    <div className="flex items-center gap-2 mb-4">
        {author.profile_picture && (
            <img 
                src={author.profile_picture} 
                alt={author.userName}
                className="w-8 h-8 rounded-full" 
            />
        )}
        <span className="text-sm text-zinc-400">
            By {author.userName}
        </span>
    </div>
);