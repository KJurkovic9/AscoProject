import { cn } from "@/lib/utils";
import { VariantProps, cva } from "class-variance-authority";

const t = cva("", {
  variants: {
    size: {
      default: "text-2xl font-semibold",
      sm: "text-3xl font-semibold",
    },
  },
});

interface TitleProps extends VariantProps<typeof t> {
  children?: React.ReactNode | React.ReactNode[];
  className?: string;
}

export const Title = ({
  children,
  className,
  size = "default",
}: TitleProps) => {
  return (
    <div
      className={cn(
        t({
          size: size,
        }),
        className,
      )}
    >
      {children}
    </div>
  );
};
