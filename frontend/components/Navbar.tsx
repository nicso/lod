

export default function Navbar() {
  return (
    <header className="fixed w-full z-10 top-0 border-b border-zinc-800 bg-zinc-900 h-16">
      <nav className="flex items-center justify-between p-4 mx-auto">
        <div className="flex items-center">
          <a href="/" className="text-xl font-bold flex items-center gap-2 relative">
            <object id="logo" data="/lod_logo.svg" type="image/svg+xml" className="w-16 absolute top-0"></object>
            <span className="text-zinc-300 text-3xl ml-20">LIGHTS ON DEVS</span>
          </a>
        </div>
        <div className="flex items-center gap-4">
          <a href="#" className="text-zinc-300">
            Explore
          </a>
          <a href="#" className="text-zinc-300">
            Jobs
          </a>
        </div>

        <div className="flex items-center gap-4 flex-end ">
          <a href="/auth" className="text-amber-500">
            <button className="text-zinc-300 bg-amber-500 hover:bg-amber-400 p-2 rounded-3xl px-5">
              connect
            </button>            
          </a>
        </div>
      </nav>
    </header>
  );
}