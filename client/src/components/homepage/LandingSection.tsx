"use client";

import { DotLottiePlayer } from "@dotlottie/react-player";
import "@dotlottie/react-player/dist/index.css";
import { NextPage } from "next";
import Link from "next/link";
import FadeInWhenVisible from "../ui/FadeInWhenVisible";
import LargeHeading from "../ui/LargeHeading";
import Paragraph from "../ui/Paragraph";
import { Button } from "../ui/button";

interface LandingSectionProps {}
const LandingSection: NextPage<LandingSectionProps> = ({}) => {
  return (
    <main className="flex h-full min-h-[90vh] w-full flex-col items-center justify-between space-y-10 p-5 lg:flex-row lg:p-20">
      <div className="h-full w-full space-y-16 pt-10 lg:-mt-[100px] lg:w-2/5">
        <FadeInWhenVisible duration={1} y={70} x={0}>
          <LargeHeading size="lg" className="">
            Inovativna energetska rješenja za moderno doba
          </LargeHeading>
          <div className="mt-4 space-y-10">
            <Paragraph size="lg">
              Precizne kalkulacije i personalizirani rejtinzi isplativosti
              solarnih elektrana pružaju vam ključ za energetsku neovisnost i
              održivu budućnost.
            </Paragraph>
            <div className="flex justify-center space-x-2 lg:justify-start">
              <Link href="/guides">
                <Button size="xl" variant="outline">
                  Podijeli iskustva
                </Button>
              </Link>
              <Link href="/app/calculate">
                <Button size="xl">Napravi kalkulaciju</Button>
              </Link>
            </div>
          </div>
        </FadeInWhenVisible>
      </div>
      <div className="borderr h-full w-full lg:w-3/5">
        <div className="h-full w-full lg:min-w-[36rem]">
          <DotLottiePlayer
            src="/assets/landing-animation.lottie"
            autoplay
            loop
            className="ml-2 xl:ml-20"
          ></DotLottiePlayer>
        </div>
      </div>
    </main>
  );
};

export default LandingSection;
