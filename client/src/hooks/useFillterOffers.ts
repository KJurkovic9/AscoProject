import { ProjectResponse, isOfferFulfilled } from "@/types/api";
import { useMemo } from "react";

export const useFilterOffers = ({
  projectResponse,
  onlyFulfilled,
}: {
  projectResponse?: ProjectResponse;
  onlyFulfilled: boolean;
}) => {
  return useMemo(() => {
    const offers = projectResponse?.offers;
    // set the chosen offer to the top of the list
    if (offers?.length) {
      const chosenOffer = offers.find((offer) => offer.state === "CHOSEN");
      if (chosenOffer) {
        return [
          chosenOffer,
          ...offers.filter((offer) => offer !== chosenOffer),
        ];
      }
    }

    return (
      offers?.filter((offer) => {
        if (onlyFulfilled) {
          return isOfferFulfilled(offer) || offer.state === "REJECTED";
        }
        return true;
      }) || []
    );
  }, [projectResponse, onlyFulfilled]);
};
