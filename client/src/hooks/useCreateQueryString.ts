import { useSearchParams } from "next/navigation";
import { useCallback } from "react";

export const useCreateQueryString = () => {
  const searchParams = useSearchParams();
  const t = useCallback(
    (name: string, value: string) => {
      const params = new URLSearchParams(searchParams.toString());
      params.set(name, value);

      return params.toString();
    },
    [searchParams],
  );

  return t;
};
