import React, { HTMLAttributes } from 'react';
import { VariantProps, cva } from 'class-variance-authority';
import { cn } from '@/lib/utils';

const paragraphVariants = cva('max-w-prose mb-2 text-center lg:text-start', {
  variants: {
    size: {
      default: 'text-base sm:text-lg',
      lg: 'text-lg lg:text-xl xl:text-2xl',
      md: 'text-base lg:text-lg xl:text-xl',
      sm: 'text-sm, sm:text-base',
    },
  },
  defaultVariants: {
    size: 'default',
  },
});

interface ParagraphProps
  extends HTMLAttributes<HTMLParagraphElement>,
    VariantProps<typeof paragraphVariants> {}

const Paragraph = React.forwardRef<HTMLParagraphElement, ParagraphProps>(
  ({ className, size, children, ...props }, ref) => {
    return (
      <p
        ref={ref}
        {...props}
        className={cn(paragraphVariants({ size, className }))}
      >
        {children}
      </p>
    );
  }
);

Paragraph.displayName = 'Paragraph';

export default Paragraph;
