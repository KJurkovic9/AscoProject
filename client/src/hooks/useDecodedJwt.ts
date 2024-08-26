import { jwtDecode } from "jwt-decode";
import { useMemo } from "react";

export const useDecodedJwt = <T>(token?: string | null) => {
  return useMemo(() => {
    if (!token) return null;
    try {
      return jwtDecode(token) as T;
    } catch {
      return null;
    }
  }, [token]);
};
