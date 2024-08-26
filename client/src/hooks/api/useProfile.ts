import { axios } from "@/lib/axios";
import { queryKeys } from "@/lib/queryKeys";
import { User } from "@/types/api";
import { useQuery } from "@tanstack/react-query";

export type Resp = {
  message: string;
  user: User;
  reviewCount: number;
  projectCount: number;
  offerCount: number;
};

export const useProfile = () => {
  return useQuery({
    queryKey: queryKeys.profile,
    queryFn: async () => {
      return (await axios.get("/user-profile/get"))?.data as Resp;
    },
  });
};
