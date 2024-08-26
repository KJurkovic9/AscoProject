import { cn } from "@/lib/utils";
import { StarSolid } from "iconoir-react";

interface StatProps {
  children?: React.ReactNode | React.ReactNode[];
  className?: string;
}

export const Stat = ({ children, className = "" }: StatProps) => {
  return (
    <div className={`flex items-center gap-1 ${className}`}>{children}</div>
  );
};

interface StatTextProps {
  children?: React.ReactNode | React.ReactNode[];
  className?: string;
}

export const StatText = ({ children, className = "" }: StatTextProps) => {
  return <div className={cn(`font-medium`, className)}>{children}</div>;
};

interface StatIconProps {
  children?: React.ReactNode | React.ReactNode[];
}

export const StatIcon = ({ children }: StatIconProps) => {
  return { children };
};

interface RatingProps {
  rating: number;
}

export const Rating = ({ rating }: RatingProps) => {
  return (
    <div className={`flex items-center gap-[3px] pt-px`}>
      <StarSolid height={16} width={16} strokeWidth={1} className={`mb-[px]`} />
      <div className={`font-medium`}>{rating}/5</div>
    </div>
  );
};
