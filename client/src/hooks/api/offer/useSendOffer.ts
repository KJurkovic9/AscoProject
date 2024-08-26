import { axios } from "@/lib/axios";
import { useMutation } from "@tanstack/react-query";

export const useSendOffer = (projectId?: string) => {
  return useMutation({
    mutationFn: async (data: number) => {
      const r = await axios.post(`/offer/create`, {
        projectId: projectId,
        companies: [data],
      });
      return r.data;
    },
  });
};
