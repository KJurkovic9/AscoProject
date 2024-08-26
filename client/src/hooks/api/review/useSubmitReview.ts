import { ReviewForm } from "@/components/review/ReviewDialog";
import { axios } from "@/lib/axios";
import { useMutation } from "@tanstack/react-query";

export const useSubmitReview = () => {
  return useMutation({
    mutationFn: async ({
      rating,
      text,
      companyId,
    }: ReviewForm & { companyId: string }) => {
      return axios.post(`/review/${companyId}`, {
        rating,
        text,
      });
    },
  });
};

export const useEditReview = () => {
  return useMutation({
    mutationFn: async ({
      rating,
      text,
      reviewId,
    }: ReviewForm & { reviewId: string }) => {
      return axios.patch(`/review/edit`, {
        id: reviewId,
        rating,
        text,
      });
    },
  });
};
