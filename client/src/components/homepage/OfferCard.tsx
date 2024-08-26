"use client";

import { NextPage } from "next";
import Image from "next/image";
import { DotLottiePlayer } from "@dotlottie/react-player";
import FadeInWhenVisible from "../ui/FadeInWhenVisible";

interface OfferCardProps {
  title: string;
  description: string;
  animation: string;
  alt: string;
}

const OfferCard: NextPage<OfferCardProps> = ({
  title,
  description,
  animation,
  alt,
}) => {
  return (
    <FadeInWhenVisible duration={1} y={70} x={0}>
      <div className="bg-background border w-full h-full relative xl:grid xl:grid-rows-2 flex flex-col rounded-xl p-5">
        <div className="min-w-40 h-40 md:h-56 relative flex justify-center items-center">
          <DotLottiePlayer
            src={animation}
            autoplay
            loop
            speed={0.5}
          ></DotLottiePlayer>
        </div>
        <div className="grid grid-row-2 space-y-5">
          <h2 className="text-center font-bold max-[320px]:text-xl text-2xl">
            {title}
          </h2>
          <p className="text-center font-medium max-[320px]:text-[15px] text-lg">
            {description}
          </p>
        </div>
      </div>
    </FadeInWhenVisible>
  );
};

export default OfferCard;
