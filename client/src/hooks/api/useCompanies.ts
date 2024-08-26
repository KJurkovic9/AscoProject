import { axios } from "@/lib/axios";
import { CompaniesResponse } from "@/types/api";
import { useQuery } from "@tanstack/react-query";

export const useCompanies = () => {
  return useQuery({
    queryKey: ["companies"],
    queryFn: async () => {
      return (await axios.get("/company/get-all")).data as CompaniesResponse;
    },
  });
};
