"use client";

import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import {
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import { Textarea } from "@/components/ui/textarea";
import {
  useEditReview,
  useSubmitReview,
} from "@/hooks/api/review/useSubmitReview";
import { queryKeys } from "@/lib/queryKeys";
import { queryClient } from "@/providers";
import { Review } from "@/types/api";
import { zodResolver } from "@hookform/resolvers/zod";
import { Star, StarSolid } from "iconoir-react";
import { useEffect } from "react";
import { useForm } from "react-hook-form";
import ReactStars from "react-rating-star-with-type";
import { toast } from "sonner";
import { z } from "zod";

const reviewFormSchema = z.object({
  rating: z.number().int().min(1).max(5),
  text: z.string().min(10).max(500),
});

export type ReviewForm = z.infer<typeof reviewFormSchema>;

interface ReviewDialogProps {
  children?: React.ReactNode | React.ReactNode[];
  companyId: string;
  projectId: string;
  isOpen: boolean;
  // if reviewId is provided, it will be used to edit the review
  review?: Review;
  setIsOpen: React.Dispatch<React.SetStateAction<boolean>>;
}

export const ReviewDialog = ({
  companyId,
  isOpen,
  projectId,
  setIsOpen,
  review,
}: ReviewDialogProps) => {
  const addReview = useSubmitReview();
  const editReview = useEditReview();

  const isEditing = !!review;

  const form = useForm({
    resolver: zodResolver(reviewFormSchema),
    defaultValues: review || {
      rating: 2,
      text: "",
    },
  });

  const invalidateQueries = () => {
    queryClient.invalidateQueries({
      queryKey: queryKeys.review.check(companyId),
    });
    queryClient.invalidateQueries({
      queryKey: queryKeys.project.id(projectId),
    });
  };

  const onSubmit = async (data: ReviewForm) => {
    try {
      if (!companyId) {
        return;
      }
      await addReview.mutateAsync({ ...data, companyId });
      invalidateQueries();
      toast.success("Recenzija je uspješno dodana.");
      setIsOpen(false);
    } catch (error) {
      toast.error((error as any).message || "Došlo je do greške.");
    }
  };

  const onEditReview = async (data: ReviewForm) => {
    try {
      if (!review) {
        throw new Error("Review ID is required for editing.");
      }
      await editReview.mutateAsync({ ...data, reviewId: review.id.toString() });
      invalidateQueries();
      toast.success("Recenzija je uspješno ažurirana.");
      setIsOpen(false);
    } catch (error) {
      toast.error((error as any).message || "Došlo je do greške.");
    }
  };

  useEffect(() => {
    if (isOpen) {
      if (review) {
        form.reset(review);
      }
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [isOpen]);

  const iconSize = 26;

  return (
    <Dialog open={isOpen} onOpenChange={setIsOpen}>
      <DialogContent className="sm:max-w-lg">
        <Form {...form}>
          <form
            onSubmit={form.handleSubmit(isEditing ? onEditReview : onSubmit)}
          >
            <DialogHeader>
              <DialogTitle>
                {isEditing ? "Uredi recenziju" : "Ocijeni izvođača"}
              </DialogTitle>
              <DialogDescription>
                Napišite recenziju za izvođača, je li cijena odgovarajuća,
                kvaliteta usluge, brzina isporuke, itd.
              </DialogDescription>
            </DialogHeader>
            <div className="mt-4 flex flex-col">
              <FormField
                control={form.control}
                name="rating"
                render={({
                  field: { onChange, value },
                  fieldState: { error },
                }) => (
                  <FormItem className={`basis-1/2`}>
                    <FormLabel>Ocjena</FormLabel>
                    <FormControl>
                      <ReactStars
                        count={5}
                        isEdit={true}
                        value={value}
                        onChange={onChange}
                        emptyIcon={<Star width={iconSize} height={iconSize} />}
                        filledIcon={
                          <StarSolid
                            width={iconSize}
                            height={iconSize}
                            className={`text-accent-1`}
                          />
                        }
                      ></ReactStars>
                    </FormControl>
                    <FormDescription>
                      Ocjena od 1 do 5, 1 je najgora, 5 je najbolja.
                    </FormDescription>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={form.control}
                name="text"
                render={({ field }) => (
                  <FormItem className={`mt-3 basis-1/2`}>
                    <FormLabel>Detalji</FormLabel>
                    <FormControl>
                      <Textarea {...field} placeholder="Napišite recenziju" />
                    </FormControl>
                    <FormDescription>
                      Detaljna recenzija kompanije.
                    </FormDescription>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>
            <DialogFooter className="mt-4 justify-end">
              <DialogClose asChild>
                <Button type="button" variant="secondary">
                  Zatvori
                </Button>
              </DialogClose>
              <Button type="submit" variant="default">
                {isEditing ? "Spremi" : "Pošalji"}
              </Button>
            </DialogFooter>
          </form>
        </Form>
      </DialogContent>
    </Dialog>
  );
};
