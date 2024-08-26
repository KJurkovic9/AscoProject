import { mapboxImg } from "@/lib/utils";
import { useMemo } from "react";

export const useMapImg = (lat?: number, lng?: number, zoom = 14) => {
  return useMemo(() => {
    if (!lat || !lng) {
      return "";
    }

    return mapboxImg(lat, lng);
  }, [lat, lng]);
};
