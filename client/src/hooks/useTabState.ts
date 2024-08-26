import { useCreateQueryString } from "@/hooks/useCreateQueryString";
import { usePathname, useRouter, useSearchParams } from "next/navigation";
import { useState } from "react";

export const useTabState = <T extends string>(initialTab: T) => {
  const router = useRouter();
  const pathname = usePathname();
  const searchP = useSearchParams();
  const createSearch = useCreateQueryString();

  const [tab, setTab] = useState<T>((searchP.get("tab") as T) || initialTab);

  const selectTab = (tab: T) => {
    setTab(tab);
    const search = createSearch("tab", tab);
    router.replace(`${pathname}?${search}`);
  };

  return [tab, selectTab] as const;
};
