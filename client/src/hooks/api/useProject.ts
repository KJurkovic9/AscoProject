import { axios } from "@/lib/axios";
import { queryKeys } from "@/lib/queryKeys";
import { ProjectResponse } from "@/types/api";
import { useQuery } from "@tanstack/react-query";

export const useProject = (id: string) => {
  return useQuery({
    queryKey: queryKeys.project.id(id),
    queryFn: async () => {
      const t = (await axios.get(`/project/${id}`)).data as ProjectResponse;
      return t;
    },
  });
};
