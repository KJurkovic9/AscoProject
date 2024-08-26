import { NextPage } from "next";
import LargeHeading from "../ui/LargeHeading";
import Link from "next/link";

interface FooterProps {}

const footerItems1 = [
  { href: "/", text: "Početna" },
  { href: "/app/calculate", text: "Izračunaj" },
  { href: "/news", text: "Novosti" },
  { href: "/aboutus", text: "O nama" },
];

const footerItems2 = [
  { href: "/contact", text: "Kontakt" },
  { href: "/guides", text: "Kompletan vodič" },
];

const Footer: NextPage<FooterProps> = ({}) => {
  return (
    <div className="flex h-96 w-full flex-col space-y-5 bg-black px-5 py-10 sm:px-20">
      <div className="flex h-full w-full flex-col space-y-5">
        <LargeHeading
          size="sm"
          className="h-full tracking-normal text-neutral-300"
        >
          ASCO
        </LargeHeading>
        <div className="flex h-full flex-col items-center space-y-5 text-neutral-300 sm:flex-row sm:items-start sm:justify-start sm:space-x-14 sm:space-y-0">
          <div className="flex flex-col items-center space-y-5 text-neutral-300 sm:items-start">
            {footerItems1.map((item, i) => (
              <Link
                key={i}
                href={item.href}
                className="text-[15px] tracking-wide"
              >
                {item.text}
              </Link>
            ))}
          </div>
          <div className="flex flex-col items-center space-y-5 text-neutral-300 sm:items-start">
            {footerItems2.map((item, i) => (
              <Link
                key={i}
                href={item.href}
                className="text-[15px] tracking-wide"
              >
                {item.text}
              </Link>
            ))}
          </div>
        </div>
      </div>
      <div className="flex h-20 w-full items-end justify-between text-neutral-500">
        <p>&copy; 2024 Prongs</p>
        <Link href="/zastita-privatnosti" className="text-[15px] tracking-wide">
          Zaštita privatnosti
        </Link>
      </div>
    </div>
  );
};

export default Footer;
