import { axios } from "@/lib/axios";
import { queryKeys } from "@/lib/queryKeys";
import { Review } from "@/types/api";
import { useQuery } from "@tanstack/react-query";

export const useCheckReview = (companyId?: number, enabled?: boolean) => {
  return useQuery({
    queryKey: queryKeys.review.check(companyId),
    queryFn: async () => {
      const r = await axios.get(`/review/${companyId}`);
      return r.data as { review?: Review };
    },
    enabled: enabled,
  });
};
