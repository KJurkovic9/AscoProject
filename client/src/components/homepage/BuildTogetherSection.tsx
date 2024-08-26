"use client";

import { Button } from "@/components/ui/button";
import { NextPage } from "next";
import Image from "next/image";
import FadeInWhenVisible from "../ui/FadeInWhenVisible";
import LargeHeading from "../ui/LargeHeading";
import Paragraph from "../ui/Paragraph";
import Link from "next/link";

interface BuildTogetherSectionProps {}

const BuildTogetherSection: NextPage<BuildTogetherSectionProps> = ({}) => {
  return (
    <div className="flex h-[45rem] min-h-[30rem] w-full flex-col items-center bg-background p-5 sm:space-x-0 sm:p-20 md:h-screen md:flex-row">
      <div className="relative h-full w-full lg:h-3/4 lg:w-1/2">
        <Image src="/images/together.svg" fill alt="0" />
      </div>

      <div className="flex w-full flex-col items-center">
        <FadeInWhenVisible duration={1} x={70} y={0}>
          <div className="space-y-5">
            <LargeHeading className="md:text-left">
              Gradimo ASCO zajedno
            </LargeHeading>
            <Paragraph size="md" className="md:text-left">
              ASCO je platforma koja se zalaže za prelazak na obnovljive izvore
              energije, povezujući ljude s certificiranim instalaterima solarnih
              panela i potičući održivost u našim domovima i zajednicama.
              Pozivamo vas da podijelite svoja iskustva s solarnim panelima i
              pridružite nam se u izgradnji održive energetske budućnosti.
            </Paragraph>
            <div className="flex w-full justify-center md:justify-start">
              <Link href="/guides">
                <Button className="w-64" size="xl">
                  Podijelite iskustva
                </Button>
              </Link>
            </div>
          </div>
        </FadeInWhenVisible>
      </div>
    </div>
  );
};

export default BuildTogetherSection;
