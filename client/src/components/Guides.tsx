import { PlusSquare } from "lucide-react";
import { Button } from "./ui/button";

interface GuideCard {}

export const GuideCard = ({}) => {
  return (
    <div className="group relative flex w-full cursor-pointer">
      <div>title</div>
      <div>paragraph</div>
    </div>
  );
};

interface GuidesProps {}

export const Guides = ({}) => {
  return (
    <div className={`flex flex-col gap-3`}>
      <div className={``}>
        <h1>Guideovi</h1>
        <Button
          className={`gap-2`}
          onClick={() => {
            console.log("open dialog");
          }}
        >
          <PlusSquare width={20} height={20} strokeWidth={2} />
          Novi Guide
        </Button>
      </div>
      <div className={`grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3`}>
        pokjsdasdapooalsdjkllpokokokokokokokokokokokok
      </div>
    </div>
  );
};
