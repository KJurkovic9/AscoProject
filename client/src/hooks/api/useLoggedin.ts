import { axios } from "@/lib/axios";
import { queryKeys } from "@/lib/queryKeys";
import { queryClient } from "@/providers";
import { BaseUser } from "@/types/api";
import { useQuery } from "@tanstack/react-query";

type CheckSessionResponse = {
  user: BaseUser;
  message: string;
};

export const checkSession = async () => {
  try {
    const d = (await axios.post("/check-session")).data as CheckSessionResponse;
    return d.user;
  } catch (error) {
    return false;
  }
};

export const prefetchLoggedIn = () => {
  queryClient.prefetchQuery({
    queryKey: queryKeys.loggedIn,
    queryFn: checkSession,
  });
};

export const useLoggedIn = () => {
  return useQuery({
    queryKey: queryKeys.loggedIn,
    queryFn: checkSession,
  });
};
