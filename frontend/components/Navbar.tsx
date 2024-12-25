'use client';

import { useAuth } from '@/components/auth/AuthContext';
import Link from 'next/link';

export default function Navbar() {
  const { user, logout } = useAuth();

  const handleLogout = async () => {
    await logout();
    // La redirection sera automatique grâce au contexte d'authentification
  };

  return (
    <header className="fixed w-full z-10 top-0 border-b border-zinc-800 bg-zinc-900 h-16">
      <nav className="flex items-center justify-between p-4 mx-auto">
        <div className="flex items-center">
          <Link href="/" className="text-xl font-bold flex items-center gap-2 relative">
            <object id="logo" data="/lod_logo.svg" type="image/svg+xml" className="w-16 absolute top-0"></object>
            <span className="text-zinc-300 text-3xl ml-20">LIGHTS ON DEVS</span>
          </Link>
        </div>

        <div className="flex items-center gap-4">
          <Link href="#" className="text-zinc-300">
            Explore
          </Link>
          <Link href="#" className="text-zinc-300">
            Jobs
          </Link>
        </div>

        <div className="flex items-center gap-4 flex-end">
          {user ? (
            <div className="flex items-center gap-4">
              <span className="text-zinc-300">
                {user.name}
              </span>
              <button
                onClick={handleLogout}
                className="text-zinc-300 bg-amber-500 hover:bg-amber-400 p-2 rounded-3xl px-5"
              >
                Déconnexion
              </button>
            </div>
          ) : (
            <Link href="/auth" className="text-amber-500">
              <button className="text-zinc-300 bg-amber-500 hover:bg-amber-400 p-2 rounded-3xl px-5">
                connect
              </button>
            </Link>
          )}
        </div>
      </nav>
    </header>
  );
}
