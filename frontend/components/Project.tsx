export default function Project() {
    return (
        <a href="#" className="project bg-zinc-900 p-2">
            <div className="flex justify-between items-center project-header">
                <img src="/lod_logo.svg" alt="lod logo" className="w-10 avatar project-avatar bg-zinc-700 rounded-full p-1" />
                <div className="flex justify-center flex-col project-infos">
                    <h2 className="font-bold text-2xl project-title">Project title</h2>
                    <h3 className="font-bold text-xl project-author">Nicso</h3>
                </div>
            </div>

            <img src="https://placecats.com/neo/300/200" alt="thumbnail" className="w-full object-contain project-thumbnail " />

            <div className="tags flex flex-wrap gap-2 mt-4 project-tags">
                <div className="tag bg-zinc-800 text-zinc-500 px-2 py-1 rounded-lg project-tag">
                    #tag1
                </div>
                <div className="tag bg-zinc-800 text-zinc-500 px-2 py-1 rounded-lg project-tag">
                    #tag2
                </div>
                <div className="tag bg-zinc-800 text-zinc-500 px-2 py-1 rounded-lg project-tag">
                    #tag3
                </div>
            </div>

        </a>
    );
};