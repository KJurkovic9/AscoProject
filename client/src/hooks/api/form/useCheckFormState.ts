import { axios } from "@/lib/axios";
import { useQuery } from "@tanstack/react-query";

export const useCheckFormState = (jwt?: string | null) => {
  return useQuery({
    queryKey: ["checkFormState", jwt],
    queryFn: async () => {
      return (await axios.get(`/offer/check?token=${jwt}`)).data as {
        done: boolean;
      };
    },
  });
};
