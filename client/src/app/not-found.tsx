"use client";
import { NextPage } from "next";
import { DotLottiePlayer } from "@dotlottie/react-player";

interface NotFoundProps {}

const NotFound: NextPage<NotFoundProps> = ({}) => {
  return (
    <div className="flex h-screen max-h-screen w-full flex-col items-center justify-center">
      <h1 className="mt-5 text-center text-2xl font-medium md:mt-20">
        Stranica je u izradi, vidimo se uskoro...
      </h1>
      <div className="h-full w-full md:w-1/3">
        <DotLottiePlayer
          src="/assets/work-in-progress.lottie"
          autoplay
          loop
          speed={0.5}
        ></DotLottiePlayer>
      </div>
    </div>
  );
};

export default NotFound;
