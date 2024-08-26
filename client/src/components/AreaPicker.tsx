import { Button } from "@/components/ui/button";
import { mapboxAccessToken } from "@/lib/const";
import { cn } from "@/lib/utils";
import MapboxDraw from "@mapbox/mapbox-gl-draw";
import "@mapbox/mapbox-gl-draw/dist/mapbox-gl-draw.css";
import mapboxgl, { Map } from "mapbox-gl";
import "mapbox-gl/dist/mapbox-gl.css";
import { useCallback, useEffect, useRef, useState } from "react";
import Turf from "turf";

interface AreaPickerProps {
  onSelected: (area: number) => void;
  initialLocation?: [number, number] | null;
  open: boolean;
  onOpenChange: (open: boolean) => void;
}

const compareLocations = (
  a?: [number, number] | null,
  b?: [number, number] | null,
) => {
  if (!a || !b) return false;
  return a[0] === b[0] && a[1] === b[1];
};

export const AreaPicker = ({
  initialLocation,
  onSelected,
  open,
  onOpenChange,
}: AreaPickerProps) => {
  const map = useRef<Map | null>(null);
  const [zoom, setZoom] = useState(15);
  const [area, setArea] = useState<number | null>(null);

  const drawRef = useRef<MapboxDraw | null>(null);

  const updateArea = useCallback(() => {
    const data = drawRef.current?.getAll();

    if (data) {
      const area = Turf.area(data);
      setArea(area);
    }
  }, []);

  const oldLocation = useRef(initialLocation);

  useEffect(() => {
    setTimeout(() => {
      const mapContainer = document.getElementById("map");
      // console.log("useEffect", map.current, mapContainer, open);
      if (!map.current && mapContainer && open) {
        map.current = new mapboxgl.Map({
          container: mapContainer,
          accessToken: mapboxAccessToken,
          style: "mapbox://styles/mapbox/satellite-v9",
          center: initialLocation || [17.8081, 44.7722],
          zoom: zoom,
        });
        const draw = new MapboxDraw({
          displayControlsDefault: false,

          // Select which mapbox-gl-draw control buttons to add to the map.
          controls: {
            polygon: true,
            trash: true,
          },
          // Set mapbox-gl-draw to draw by default.
          // The user does not have to click the polygon control button first.
          defaultMode: "draw_polygon",
          touchEnabled: true,
        });
        drawRef.current = draw;
        map.current.addControl(draw);

        map.current.on("draw.create", updateArea);
        map.current.on("draw.delete", updateArea);
        map.current.on("draw.update", updateArea);
      } else {
        if (
          map.current &&
          initialLocation &&
          !compareLocations(oldLocation.current, initialLocation)
        ) {
          oldLocation.current = initialLocation;
          map.current.setCenter(initialLocation);

          drawRef.current?.deleteAll();
        }
      }
    }, 100);
  }, [initialLocation, open, updateArea, zoom]);

  return (
    <>
      <div
        onClick={() => onOpenChange(false)}
        className={cn(
          `inset-0 z-[99999] bg-black/80 data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0`,
          `${open ? "fixed" : "hidden"}`,
        )}
      ></div>
      <div
        className={cn(
          `left-[50%] top-[50%] z-[999999] grid w-full max-w-full translate-x-[-50%] translate-y-[-50%] gap-4 border bg-background p-6 px-3 shadow-lg sm:rounded-lg md:max-w-2xl md:px-6`,
          `${open ? "fixed" : "hidden"}`,
        )}
      >
        <div className={`flex flex-col space-y-1.5 text-center sm:text-left`}>
          <div className={`text-lg font-semibold leading-none tracking-tight`}>
            Izmjeri površinu
          </div>
          <div className={`text-sm text-black/60`}>
            Pritisnite na mapu kako biste označili točke i izmjerili površinu,
            pritisnite 2 puta za završetak.
          </div>
        </div>

        <div className={`text-2xl font-medium`}>
          <span className={`text-sm`}>Površina: </span>
          {area ? area.toFixed(2) : "0"} m²
        </div>
        <div id="map" className={`aspect-video w-full`}></div>

        <div
          className={`flex flex-col-reverse gap-y-2 sm:flex-row sm:justify-end sm:gap-x-2 sm:gap-y-0`}
        >
          <Button onClick={() => onOpenChange(false)} variant={"outline"}>
            Zatvori
          </Button>
          <Button
            onClick={() => {
              onSelected(parseFloat(area?.toFixed(2) || "0"));
              onOpenChange(false);
            }}
          >
            Ispuni
          </Button>
        </div>
      </div>
    </>
  );
};
