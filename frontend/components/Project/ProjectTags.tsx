import Link from 'next/link';

type Tag = {
    id: number;
    name: string;
};


type ProjectTagsProps = {
    tags: Tag[];
};


export const ProjectTags = ({ tags }: ProjectTagsProps) => (
    <div className="project-tags">
        {tags.map((tag, index) => (
            <Link
                href={'/tags/' + tag.id}
                key={tag.id}
                className="project-tag">
                {tag.name}
            </Link>
        ))}
    </div>
);
