/* eslint-disable @next/next/no-img-element */
import {
  Result,
  ResultStatSmall,
  ResultTitle,
} from "@/components/calc/CalculationResults";
import { useMapImg } from "@/hooks/useMapImg";
import { roofOrientationR } from "@/lib/const";
import { Calculation } from "@/types/api";

interface CalculationInfoProps {
  calculation?: Calculation;
  disableEdit?: boolean;
}

export const CalculationInfo = ({
  calculation,
  disableEdit = false,
}: CalculationInfoProps) => {
  const mapImage = useMapImg(calculation?.lat, calculation?.lng);

  return (
    <div
      className={`flex flex-col overflow-hidden rounded-md border border-border`}
    >
      <div className={`grid grid-cols-2 gap-0.5 p-4 md:max-w-[90%]`}>
        <Result className={`col-span-2 md:col-span-1`}>
          <ResultTitle>Lokacija</ResultTitle>
          <ResultStatSmall>{calculation?.location}</ResultStatSmall>
        </Result>
        {/* <Stat className={`col-span-2 md:col-span-1`}>
          <MapPin height={18} strokeWidth={2} />
          <StatText>{calculation?.location}</StatText>
        </Stat> */}
        <Result className={`col-span-2 md:col-span-1`}>
          <ResultTitle>Godišnja potrošnja</ResultTitle>
          <ResultStatSmall>
            {calculation?.yearlyConsumption} kWh
          </ResultStatSmall>
        </Result>
        <Result className={`xs:col-span-1 xs:basis-full col-span-2`}>
          <ResultTitle>Površina objekta</ResultTitle>
          <ResultStatSmall>{calculation?.roofSurface} m²</ResultStatSmall>
        </Result>

        <Result className={`xs:col-span-1 xs:basis-full col-span-2`}>
          <ResultTitle>Orjentacija krova i nagib</ResultTitle>
          <ResultStatSmall>
            {
              roofOrientationR[
                calculation?.roofOrientation as keyof typeof roofOrientationR
              ]
            }
            {", "}
            {calculation?.roofPitch}°
          </ResultStatSmall>
        </Result>
      </div>

      <div className={`relative h-80 overflow-hidden`}>
        {!disableEdit && (
          <div className={`absolute bottom-4 right-4 z-50`}>
            {/* <Button disabled onClick={() => {}}>
              Uredi izračun
            </Button> */}
          </div>
        )}
        <div
          className={`absolute left-0 top-0 z-30 h-10 w-full bg-gradient-to-b from-background to-transparent`}
        />
        <div
          className={`absolute left-0 top-0 z-30 h-full w-10 bg-gradient-to-r from-background to-transparent`}
        />
        <div
          className={`absolute bottom-0 left-0 z-30 h-10 w-full bg-gradient-to-t from-background to-transparent`}
        />
        <div
          className={`absolute right-0 top-0 z-30 h-full w-10 bg-gradient-to-l from-background to-transparent`}
        />
        <img
          src={mapImage}
          alt=""
          className={`z-40 h-full w-full scale-150 object-cover md:scale-100`}
        />
      </div>
    </div>
  );
};
