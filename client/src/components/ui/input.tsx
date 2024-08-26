import * as React from "react";

import { cn } from "@/lib/utils";

export interface InputProps
	extends React.InputHTMLAttributes<HTMLInputElement> {
	trailingIcon?: React.ReactNode;
}

const Input = React.forwardRef<HTMLInputElement, InputProps>(
	({ className, type, trailingIcon, ...props }, ref) => {
		const hasTrailingIcon = Boolean(trailingIcon);

		return (
			<div className={`relative w-full`}>
				<input
					type={type}
					className={cn(
						"flex h-10 w-full border border-input rounded-md bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50",
						// if hasTrailingIcon is true, add padding to the right and only round the left side of the input
						className
					)}
					ref={ref}
					{...props}
				/>
				{hasTrailingIcon && (
					<div
						className={cn(
							`absolute top-0 right-0 h-full flex items-center justify-center px-3 border border-input rounded-r-md select-none bg-white`
						)}
					>
						{trailingIcon}
					</div>
				)}
			</div>
		);
	}
);
Input.displayName = "Input";

export { Input };
