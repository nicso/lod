type ProjectThumbnailProps = {
    src: string;
    alt: string;
};

export const ProjectThumbnail = ({ src, alt }: ProjectThumbnailProps) => (
    <img
        src={src}
        alt={alt}
        className="project-thumbnail"
    />
);
