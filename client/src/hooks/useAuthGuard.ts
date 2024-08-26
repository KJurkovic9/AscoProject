import { useLoggedIn } from "@/hooks/api/useLoggedin";
import { useRouter } from "next/navigation";
import { useLayoutEffect } from "react";

export const useAuthGuard = () => {
  const router = useRouter();

  const l = useLoggedIn();

  useLayoutEffect(() => {
    if (l.isFetched && !l.data) {
      router.push("/login");
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [l.data, router]);
};
