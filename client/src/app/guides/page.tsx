import LargeHeading from "@/components/ui/LargeHeading";
import { Button } from "@/components/ui/button";
import { NextPage } from "next";

type Props = {};

const page: NextPage<Props> = ({}) => {
  return (
    <div className="w-full h-full max-[320px]:p-2 p-8 lg:p-20 ">
      <div className="flex flex-col space-y-20 items-center">
        <LargeHeading className="lg:text-center">
          Vjerojatno imate pitanja kao što su: Zašto bih ovo radio?, Koliko će
          trajati sve ovo?, Hoće li mi se isplatiti?
        </LargeHeading>
        <LargeHeading className="lg:text-center">
          Sve odgovore možete pronaći na jednom mjestu.
        </LargeHeading>
        <Button variant="outline" size="xl">
          &apos;ic&apos; Pretraži sve što te zanima
        </Button>
        <div className="border-b-2 border-neutral-400 w-1/2" />
      </div>
      <div className="h-full mt-20 flex flex-col items-center">
        <LargeHeading className="lg:text-center">
          Osnovni tijek ugradnje solarnih panela.
        </LargeHeading>
      </div>
    </div>
  );
};
export default page;
