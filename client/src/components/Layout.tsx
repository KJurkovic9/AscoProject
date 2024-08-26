interface LayoutProps {
  children?: React.ReactNode | React.ReactNode[];
  full?: boolean;
}

export const Layout = ({ children, full }: LayoutProps) => {
  return (
    <div className={`flex grow`}>
      <div
        className={`flex flex-col ${
          full ? "" : "max-w-5xl mt-10 gap-1 px-6"
        } w-full mx-auto h-full  `}
      >
        {children}
      </div>
    </div>
  );
};
