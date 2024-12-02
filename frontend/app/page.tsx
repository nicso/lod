'use client';
import Project from "@/components/Project/Project";
import { ProjectDetail } from "@/components/ProjectDetails";


export default function Home() {
    return (
    <div className="grid grid-rows-[20px_1fr_20px] items-center justify-items-center min-h-screen p-8 pb-20 gap-16 sm:p-20 font-[family-name:var(--font-geist-sans)]">
      
      <main className="grid grid-cols-3 gap-2 row-start-2 items-center sm:items-start">
        <ProjectDetail 
          projectId={1} 
          fields={['title', 'thumbnail', 'project_date', 'last_modification_date', 'viewcount', 'is_featured', 'id_category', 'status']}
        />
      </main>
    </div>
  );
}
