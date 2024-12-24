// CrepeEditor.tsx
import React, { useEffect, useRef, useState } from 'react';
import { Crepe } from '@milkdown/crepe';
import "@milkdown/crepe/theme/common/style.css";
import "@milkdown/crepe/theme/frame.css";

const CrepeEditor = ({ defaultValue = '# Hello, Milkdown!' }) => {
    const containerRef = useRef<HTMLDivElement | null>(null);
    const [editor, setEditor] = useState<Crepe | null>(null);
    const initializedRef = useRef(false);

    useEffect(() => {
        // Vérifier si déjà initialisé
        if (initializedRef.current) return;
        initializedRef.current = true;

        const newEditor = new Crepe({
            root: containerRef.current,
            defaultValue: defaultValue,
        });

        newEditor.create().then(() => {
            console.log('Editor created');
            setEditor(newEditor);
        });

        return () => {
            if (editor) {
                editor.destroy();
                setEditor(null);
                initializedRef.current = false;
            }
        };
    }, []); // Supprimer defaultValue des dépendances

    return <div ref={containerRef} />;
};

export default CrepeEditor;
