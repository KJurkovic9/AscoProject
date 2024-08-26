import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from "@/components/ui/tooltip";

interface StatInfoProps {
  children?: React.ReactNode | React.ReactNode[];
}

export const StatInfo = ({ children }: StatInfoProps) => {
  return (
    <TooltipProvider delayDuration={200}>
      <Tooltip>
        <TooltipTrigger type="button" className={`ml-1`}>
          <div
            className={`flex aspect-square h-[16px] w-[16px] cursor-pointer items-center justify-center rounded-[8px] border border-black/15 text-xs font-medium  text-foreground`}
          >
            ?
          </div>
        </TooltipTrigger>
        <TooltipContent className={`block max-w-md p-3`}>
          {children}
        </TooltipContent>
      </Tooltip>
    </TooltipProvider>
  );
};

interface FormFooterProps {
  children?: React.ReactNode | React.ReactNode[];
}

export const FormFooter = ({ children }: FormFooterProps) => {
  return <div className={`flex items-center gap-1`}>{children}</div>;
};
