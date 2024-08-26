import Counter from "@/components/Counter";
import { StatInfo } from "@/components/FormInfo";
import { MonthlyBarChart } from "@/components/calc/CalculationResultChart";
import { Button } from "@/components/ui/button";
import { Title } from "@/components/ui/title";
import { useLoggedIn } from "@/hooks/api/useLoggedin";
import { useCalculatorState } from "@/hooks/useCalculatorState";
import { cn, currencyFormat } from "@/lib/utils";
import { Calculation } from "@/types/api";
import { useRouter } from "next/navigation";

interface CalculationResultsProps {
  calculation?: Calculation;
  onlyResults?: boolean;
}

export const CalculationResults = ({
  calculation,
  onlyResults = false,
}: CalculationResultsProps) => {
  const isLoggedIn = useLoggedIn();
  const router = useRouter();

  const store = useCalculatorState();

  const onUgovori = () => {
    store.setProjectToCreate(calculation?.id);
    if (isLoggedIn.data) {
      router.push("/app/dashboard");
    } else {
      router.push("/register");
    }
  };

  return (
    <>
      <div className={`flex flex-col gap-4`}>
        {!onlyResults && <Title>Rezultati izračuna</Title>}
        <div className={`flex flex-wrap gap-y-3`}>
          <Result>
            <ResultTitle>
              Ukupna cijena
              <StatInfo>
                Instalacija: {currencyFormat(calculation?.installationPrice)},
                oprema: {currencyFormat(calculation?.equipmentPrice)}
              </StatInfo>
            </ResultTitle>
            <ResultStat>
              <Counter
                currency
                value={(calculation?.projectPrice || 0) / 100}
              />
            </ResultStat>
          </Result>
          <Result>
            <ResultTitle>
              Period isplativosti
              <StatInfo>Ukupna cijena projekta / Mjesečna ušteda</StatInfo>
            </ResultTitle>
            <ResultStat>
              <Counter value={calculation?.paybackPeroid || 0} /> god
            </ResultStat>
          </Result>
          <Result>
            <ResultTitle>Potencijalna snaga</ResultTitle>
            <ResultStat>
              <Counter value={calculation?.potentialPower || 0} />
              kW
            </ResultStat>
          </Result>
        </div>
        <div>
          <span className={`font-medium`}>Mjesečna ušteda</span>
          <MonthlyBarChart
            // @ts-ignore
            data={calculation?.profitabiltyMonthly}
          />
        </div>
      </div>
      {!onlyResults && (
        <div className={`mt-auto flex w-full gap-4`}>
          <Button
            variant={"outline"}
            className={`grow basis-1/2`}
            onClick={() => store.setFormState("calc")}
          >
            Nazad
          </Button>
          <Button
            className={`grow basis-1/2`}
            onClick={() => {
              onUgovori();
            }}
          >
            Zatraži ponudu
          </Button>
        </div>
      )}
    </>
  );
};

interface ResultTitleProps {
  children?: React.ReactNode | React.ReactNode[];
}

export const ResultTitle = ({ children }: ResultTitleProps) => {
  return <span className={`font-normal`}>{children}</span>;
};

interface ResultStatProps {
  children?: React.ReactNode | React.ReactNode[];
}

export const ResultStat = ({ children }: ResultStatProps) => {
  return <span className={`text-4xl font-semibold`}>{children}</span>;
};

export const ResultStatSmall = ({ children }: ResultStatProps) => {
  return <span className={`text-lg font-semibold`}>{children}</span>;
};

interface ResultProps {
  children?: React.ReactNode | React.ReactNode[];
  className?: string;
}

export const Result = ({ children, className }: ResultProps) => {
  return (
    <div className={cn(`flex basis-full flex-col xs:basis-1/2`, className)}>
      {children}
    </div>
  );
};
