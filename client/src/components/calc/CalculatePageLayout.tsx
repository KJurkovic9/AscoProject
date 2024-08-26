interface CalculatePageLayoutProps {
  children?: React.ReactNode | React.ReactNode[];
}

export const CalculatePageLayout = ({ children }: CalculatePageLayoutProps) => {
  return (
    <div className={`flex h-full w-full grow`}>
      <div
        className={`flex grow flex-col items-center gap-4 md:flex-row md:items-start md:gap-0`}
      >
        {children}
      </div>
    </div>
  );
};

interface CalculatePageMainProps {
  children?: React.ReactNode | React.ReactNode[];
}

export const CalculatePageMain = ({ children }: CalculatePageMainProps) => {
  return (
    <div className={`relative flex h-full grow-0 md:grow`}>{children}</div>
  );
};

interface CalculatePageSideProps {
  children?: React.ReactNode | React.ReactNode[];
}

export const CalculatePageSide = ({ children }: CalculatePageSideProps) => {
  return (
    <div
      className={`flex h-full w-full max-w-full grow flex-col gap-5 px-6 py-6 md:max-w-xl`}
    >
      {children}
    </div>
  );
};
