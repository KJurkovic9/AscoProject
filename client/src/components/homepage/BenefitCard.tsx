import { NextPage } from "next";
import Image from "next/image";
import { Plus, Minus } from "lucide-react";
import {
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "../ui/accordion";

interface BenefitCardProps {
  title: string;
  description: string;
  children: React.ReactNode;
  itemValue: string;
}

const BenefitCard: NextPage<BenefitCardProps> = ({
  title,
  description,
  children,
  itemValue,
}) => {
  return (
    <AccordionItem value={itemValue}>
      <AccordionTrigger>
        <div className="flex items-center space-x-0 sm:space-x-5">
          <div>{children}</div>
          <h3 className="text-base font-semibold sm:text-lg">{title}</h3>
        </div>
        <div>
          <Plus
            strokeWidth={1.2}
            size={40}
            className="group-data-[state=closed]:flex group-data-[state=open]:hidden"
          />
          <Minus
            strokeWidth={1.2}
            size={40}
            className="group-data-[state=open]:flex group-data-[state=closed]:hidden"
          />
        </div>
      </AccordionTrigger>
      <AccordionContent className="mb-3">{description}</AccordionContent>
    </AccordionItem>
  );
};

export default BenefitCard;
