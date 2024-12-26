'use client';

import { useAuth } from '@/components/auth/AuthContext';
import Link from 'next/link';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { User, Settings, LogOut } from 'lucide-react';
import './Navbar.css';

export default function Navbar() {
  const { user, logout } = useAuth();

  const handleLogout = async () => {
    await logout();
  };

  return (
    <>

    <header className="fixed w-full z-10 top-0 border-b border-zinc-800 bg-zinc-900 h-16">
      <nav className="flex items-center justify-between p-4 mx-auto">

        <div className="flex items-center">
          <Link href="/" className="text-xl font-bold flex items-center gap-2 relative ">
            {/* <object id="logo" data="/lod_logo.svg" type="image/svg+xml" className="w-16 absolute top-0  "></object> */}

            <embed id="logo" src="/lod_logo.svg" type="image/svg+xml" className="w-16 absolute top-0  " />


            <span className="text-zinc-300 text-3xl ml-20 logo-txt">LIGHTS ON DEVS</span>
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

        <div className="flex items-center gap-4">
          {user ? (
            <DropdownMenu>
              <DropdownMenuTrigger asChild>
                <button className="text-zinc-300 bg-amber-500 hover:bg-amber-400 p-2 rounded-3xl px-5 flex items-center gap-2">
                  <User className="w-4 h-4" />
                  {user.name}
                </button>
              </DropdownMenuTrigger>
              <DropdownMenuContent align="end" className="w-48">
                <DropdownMenuItem>
                  <Link href="/profile" className="flex items-center w-full">
                    <User className="mr-2 h-4 w-4" />
                    <span>Profil</span>
                  </Link>
                </DropdownMenuItem>
                <DropdownMenuItem>
                  <Link href="/settings" className="flex items-center w-full">
                    <Settings className="mr-2 h-4 w-4" />
                    <span>Paramètres</span>
                  </Link>
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem onClick={handleLogout} className="text-red-500">
                  <LogOut className="mr-2 h-4 w-4" />
                  <span>Se déconnecter</span>
                </DropdownMenuItem>
              </DropdownMenuContent>
            </DropdownMenu>
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
    <div className="illumination"></div>

    </>
  );
}
