import Link from "next/link";

interface ExternalLinkProps {
  children?: React.ReactNode | React.ReactNode[];
  href?: string;
}

export const ExternalLink = ({ children, href }: ExternalLinkProps) => {
  if (!href) {
    return <>{children}</>;
  }
  return (
    <Link
      href={href}
      className={`flex items-end underline-offset-2 transition-all duration-100 hover:underline`}
      target="_blank"
      rel="noopener noreferrer"
    >
      {children}
    </Link>
  );
};
