import React, { useEffect, useRef, useState, forwardRef, useImperativeHandle } from 'react';
import { Crepe } from '@milkdown/crepe';
import "@milkdown/crepe/theme/common/style.css";
import "@milkdown/crepe/theme/frame.css";

// Définir l'interface des méthodes exposées
export interface CrepeEditorHandle {
    getMarkdown: () => string | undefined;
}

interface CrepeEditorProps {
    defaultValue: string;
    readonly?: boolean;
}

const CrepeEditor = forwardRef<CrepeEditorHandle, CrepeEditorProps >(({ defaultValue , readonly = true }, ref) => {

    const containerRef = useRef<HTMLDivElement | null>(null);
    const [editor, setEditor] = useState<Crepe | null>(null);
    const initializedRef = useRef(false);

    useImperativeHandle(ref, () => ({
        getMarkdown: () => editor?.getMarkdown()
    }));

    useEffect(() => {
        if (initializedRef.current) return;
        initializedRef.current = true;

        const newEditor = new Crepe({
            root: containerRef.current,
            defaultValue: defaultValue,
        });

        newEditor.setReadonly(readonly);

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
    }, []);

    return <div ref={containerRef} />;
});

export default CrepeEditor;
