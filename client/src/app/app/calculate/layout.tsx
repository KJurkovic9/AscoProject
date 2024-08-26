import { Metadata } from "next";
import React from "react";

interface LayoutProps {
  children: React.ReactNode;
}

export const metadata: Metadata = {
  title: "Kalkulacija | Asco",
  description: "Asco",
};

const Layout = ({ children }: LayoutProps) => {
  return <>{children}</>;
};
export default Layout;
