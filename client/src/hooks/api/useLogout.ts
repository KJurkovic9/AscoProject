import { axios } from "@/lib/axios";
import { queryClient } from "@/providers";
import { useMutation } from "@tanstack/react-query";
import { useRouter } from "next/navigation";
import { toast } from "sonner";

export const useLogout = () => {
  const router = useRouter();

  const r = useMutation({
    mutationFn: async () => {
      try {
        await axios.delete("/logout");
        queryClient.clear();
        setTimeout(() => {
          router.replace("/");
        }, 200);
      } catch (error) {
        toast.error("Greška prilikom odjave");
      }
    },
  });

  return r;
};
