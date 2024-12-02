type LoadingPlaceholderProps = {
    className?: string;
};

export const LoadingPlaceholder = ({ className = "h-8 w-full max-w-2xl" }: LoadingPlaceholderProps) => (
    <div className={`${className} bg-zinc-800 animate-pulse rounded`}></div>
);