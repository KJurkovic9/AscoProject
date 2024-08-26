"use client";

import { MobileNav } from "@/components/MobileNav";
import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { useLoggedIn } from "@/hooks/api/useLoggedin";
import { useLogout } from "@/hooks/api/useLogout";
import { LogOut } from "iconoir-react";
import { X } from "lucide-react";
import { NextPage } from "next";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useState } from "react";

type Props = {};

const navItems = [
  { href: "/app/calculate", text: "Napravi kalkulaciju" },
  { href: "/guides", text: "Kompletan vodič" },
  // { href: "/news", text: "Novosti" },
  { href: "/aboutus", text: "O nama" },
];

const Navbar: NextPage<Props> = ({}) => {
  const router = useRouter();
  const isLoggedIn = useLoggedIn();
  const [dropdownOpened, setDropdownOpened] = useState(false);

  const dropdownHandler = () => {
    setDropdownOpened((s) => !s);
  };

  const logout = useLogout();

  return (
    <div className="sticky top-0 z-[99998] -mt-[4px] flex h-16 items-center justify-between px-6">
      <div
        className={`navbar-gradient pointer-events-none absolute inset-0 -bottom-6 z-[-1]`}
      ></div>
      <Link href={"/"}>
        <div className="flex items-center text-2xl font-medium">Asco</div>
      </Link>
      <div className="hidden items-center gap-10 md:flex">
        {navItems.map((item, i) => (
          <Link key={i} href={item.href} className="group relative">
            {item.text}
            <span className="absolute -bottom-1 left-1/2 h-0.5 w-0.5 bg-transparent transition-all duration-500 group-hover:bg-neutral-900"></span>
            <span className="absolute -bottom-1 right-1/2 h-0.5 w-0.5 bg-transparent transition-all duration-500 group-hover:bg-neutral-900"></span>
          </Link>
        ))}
        {isLoggedIn.data ? (
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button className={`w-24`}>Moj profil</Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent>
              <DropdownMenuItem>
                <Link href={"/app/dashboard"} className={`h-full w-full`}>
                  Moji projekti
                </Link>
              </DropdownMenuItem>
              <DropdownMenuItem
                className={`cursor-pointer`}
                onClick={async () => {
                  await logout.mutateAsync();
                }}
              >
                Odjava
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        ) : (
          <Button
            className={`w-24`}
            onClick={() => {
              router.push("/login");
            }}
          >
            Prijava
          </Button>
        )}
      </div>
      <MobileNav
        dropdownOpened={dropdownOpened}
        onDropdownChanged={setDropdownOpened}
      >
        <div className="absolute top-0 -mt-[47px] flex w-full items-center justify-between px-6 py-4">
          <Link href={"/"} onClick={dropdownHandler}>
            <div className="flex items-center text-2xl font-medium">Asco</div>
          </Link>

          <X
            size={30}
            strokeWidth={1.5}
            onClick={dropdownHandler}
            className="cursor-pointer"
          />
        </div>
        <div className="flex w-full flex-col gap-4 px-6">
          {navItems.map((item, i) => (
            <DropdownMenuItem
              key={i}
              className="border-none text-2xl font-medium"
            >
              <Link
                href={item.href}
                className="hover:underline"
                onClick={dropdownHandler}
              >
                {item.text}
              </Link>
            </DropdownMenuItem>
          ))}
          {isLoggedIn.data ? (
            <div className={`flex gap-3`}>
              <Button
                className={`grow`}
                onClick={() => {
                  dropdownHandler();
                  router.push("/app/dashboard");
                }}
              >
                Moj profil
              </Button>
              <Button
                variant={"secondary"}
                className={`p-3`}
                onClick={async () => {
                  dropdownHandler();
                  await logout.mutateAsync();
                }}
              >
                <LogOut width={20} height={20} />
              </Button>
            </div>
          ) : (
            <Button
              onClick={() => {
                dropdownHandler();
                router.push("/login");
              }}
            >
              Prijava
            </Button>
          )}
        </div>
      </MobileNav>
    </div>
  );
};
export default Navbar;
