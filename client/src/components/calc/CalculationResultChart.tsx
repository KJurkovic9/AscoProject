import { months } from "@/lib/const";
import { useMemo } from "react";
import {
  Bar,
  BarChart,
  ResponsiveContainer,
  Text,
  Tooltip,
  XAxis,
} from "recharts";

// @link https://github.com/recharts/recharts/issues/3615
const error = console.error;
console.error = (...args: any) => {
  if (/defaultProps/.test(args[0])) return;
  error(...args);
};

interface MonthlyBarChartProps {
  children?: React.ReactNode | React.ReactNode[];
  data?: { [key: string]: number }[];
}

export const MonthlyBarChart = ({ children, data }: MonthlyBarChartProps) => {
  // map data to object with key and value
  const dataMapped = useMemo(() => {
    return Object.entries(data || {}).map(([key, value]) => ({
      month: months[parseInt(key) - 1],
      savings: value,
    }));
  }, [data]);

  return (
    <div className="mt-4 h-[220px]">
      <ResponsiveContainer width="100%" height="100%">
        <BarChart data={dataMapped}>
          <Bar
            dataKey="savings"
            style={
              {
                fill: "var(--foreground)",
                opacity: 1,
              } as React.CSSProperties
            }
          />
          <XAxis
            interval={"preserveStartEnd"}
            tick={(e) => {
              const {
                payload: { value },
              } = e;
              return (
                <Text {...e} className={`text-xs font-medium text-foreground`}>
                  {value}
                </Text>
              );
            }}
            dataKey="month"
          />
          <Tooltip
            cursor={{ fill: "#00000000" }}
            formatter={(value) => {
              return [
                `${(parseInt(value as string) / 100).toFixed(2)} €`,
                "Mjesečna ušteda",
              ];
            }}
            wrapperClassName="bg-background text-foreground font-medium border border-foreground rounded-md"
          />
        </BarChart>
      </ResponsiveContainer>
    </div>
  );
};
