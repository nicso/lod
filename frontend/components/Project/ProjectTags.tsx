import Link from 'next/link';

type Tag = {
    id: number;
    name: string;
};


type ProjectTagsProps = {
    tags: Tag[];
};


export const ProjectTags = ({ tags }: ProjectTagsProps) => (
    <div className="mb-4 project-tags">
        {tags.map((tag, index) => (
            <Link
                href={'/tags/' + tag.id}
                key={tag.id}
                className="tag bg-zinc-800 text-zinc-500 px-2 py-1 rounded-lg project-tag">
                {tag.name}
            </Link>
        ))}
    </div>
);
