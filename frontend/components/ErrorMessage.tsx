type ErrMessageProps = {
    message: string;
};

export const ErrorMessage = ({ message }: ErrMessageProps) => (
    <div className="text-red-500 text-sm">
        Erreur de chargement: {message}
    </div>
);