import { Input } from "@/components/ui/input";
import { mapboxAccessToken } from "@/lib/const";
import { SearchBox } from "@mapbox/search-js-react";
import { useCallback, useEffect, useRef } from "react";

interface LocationSearchBoxProps {
  value: string;
  setValue: (value: string) => void;
  onRetrieve?: (coordinates: [number?, number?]) => void;
}

export const LocationSearchBox = ({
  value,
  onRetrieve,
  setValue,
}: LocationSearchBoxProps) => {
  const ref = useRef<HTMLDivElement>(null);
  const lastResult = useRef<string | null>(value);

  const invalidateAddress = useCallback(() => {
    if (lastResult.current !== value) {
      setValue("");
    }
  }, [lastResult, setValue, value]);

  // set all buttons in ref to be type button
  useEffect(() => {
    if (ref.current) {
      const buttons = ref.current.querySelectorAll("button");
      buttons.forEach((button) => {
        button.onclick = (e) => {
          setValue("");
          onRetrieve?.([undefined, undefined]);
          e.preventDefault();
        };
      });
    }
  }, [invalidateAddress, onRetrieve, setValue, value]);

  return (
    <div ref={ref}>
      {/* @ts-ignore */}
      <SearchBox
        placeholder="Avenija Dubrovnik 15"
        value={value}
        accessToken={mapboxAccessToken}
        theme={{
          variables: {
            borderRadius: "0.5rem",
            boxShadow: "none",
            border: "none",
            // colorPrimary: "var(--foreground)",
            colorBackground: "var(--background)",
          },
        }}
        onChange={(e) => {
          invalidateAddress();
        }}
        onRetrieve={(e) => {
          setValue(e.features[0].properties.full_address);
          lastResult.current = e.features[0].properties.full_address;
          if (onRetrieve) {
            onRetrieve(e.features[0].geometry.coordinates as any);
          }
        }}
        options={{
          country: "hr",
          language: "hr",
        }}
      >
        <Input
          type="text"
          onChange={(e) => {
            console.log(e.target.value);
          }}
        />
      </SearchBox>
    </div>
  );
};
