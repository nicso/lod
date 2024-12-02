type ProjectThumbnailProps = {
    src: string;
    alt: string;
};

export const ProjectThumbnail = ({ src, alt }: ProjectThumbnailProps) => (
    <img 
        src={src}
        alt={alt} 
        className="w-full object-cover w-full h-full project-thumbnail" 
    />
);
