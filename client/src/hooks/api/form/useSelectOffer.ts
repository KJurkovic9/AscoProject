import { axios } from "@/lib/axios";
import { useMutation } from "@tanstack/react-query";

export const useSelectOffer = () => {
  return useMutation({
    mutationFn: (offerId: string) => {
      return axios.post(`/offer/choose`, {
        offerId: offerId,
      });
    },
  });
};
