import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuPortal,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown";
import { Menu } from "lucide-react";

interface MobileNavProps {
  children?: React.ReactNode | React.ReactNode[];
  dropdownOpened?: boolean;
  onDropdownChanged?: (value: boolean) => void;
}

export const MobileNav = ({
  children,
  dropdownOpened,
  onDropdownChanged,
}: MobileNavProps) => {
  return (
    <div className="flex md:hidden">
      <DropdownMenu open={dropdownOpened} onOpenChange={onDropdownChanged}>
        <DropdownMenuTrigger>
          <Menu
            size={30}
            strokeWidth={1.5}
            onClick={() => onDropdownChanged?.(true)}
          />
        </DropdownMenuTrigger>

        <DropdownMenuPortal>
          <DropdownMenuContent className="z-[99999] -mt-10 flex h-screen w-screen flex-col justify-between pt-20">
            {children}
          </DropdownMenuContent>
        </DropdownMenuPortal>
      </DropdownMenu>
    </div>
  );
};
