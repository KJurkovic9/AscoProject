import Navbar from "@/components/Navbar";
import { cn } from "@/lib/utils";
import { Providers } from "@/providers";
import "@/styles/globals.css";
import type { Metadata } from "next";

import localFont from "next/font/local";
// const inter = Inter({ subsets: ["latin"] });

const ppFragmentSans = localFont({
  src: "/PPFragment-SansVariable.ttf",
  display: "swap",
});

export const metadata: Metadata = {
  title: "Asco",
  description: "Asco",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body
        className={cn(
          "flex min-h-screen flex-col bg-background text-foreground antialiased",
          ppFragmentSans.className,
          // inter.className
        )}
      >
        <Providers>
          <Navbar />
          {children}
        </Providers>
      </body>
    </html>
  );
}
