"use client";
import { FormFooter } from "@/components/FormInfo";
import { Layout } from "@/components/Layout";
import { LoadingScreen } from "@/components/Loading";
import { CalculationInfo } from "@/components/calc/CalculationInfo";
import { Button } from "@/components/ui/button";
import { Calendar } from "@/components/ui/calendar";
import {
  Form,
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover";
import { Textarea } from "@/components/ui/textarea";
import { Title } from "@/components/ui/title";
import { useCheckFormState } from "@/hooks/api/form/useCheckFormState";
import { useDecodedJwt } from "@/hooks/useDecodedJwt";
import { axios } from "@/lib/axios";
import { cn } from "@/lib/utils";
import { queryClient } from "@/providers";
import { FormJwt } from "@/types/api";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { format } from "date-fns";
import { hr } from "date-fns/locale";
import { CalendarIcon } from "lucide-react";
import { useSearchParams } from "next/navigation";
import { useForm } from "react-hook-form";
import { toast } from "sonner";
import { z } from "zod";

const offerFormSchema = z.object({
  price: z.coerce.number().optional(),
  description: z.string(),
  offerDate: z.date(),
});

type OfferForm = z.infer<typeof offerFormSchema>;

interface FromProps {}

const From = ({}: FromProps) => {
  const p = useSearchParams();
  const data = useDecodedJwt<FormJwt>(p.get("token"));
  const { data: formStateData, isLoading } = useCheckFormState(p.get("token"));

  const form = useForm<OfferForm>({
    resolver: zodResolver(offerFormSchema),
    defaultValues: {
      offerDate: new Date(),
      price: 0,
      description: "",
    },
  });

  const offerMutation = useMutation({
    mutationFn: (formData: OfferForm) => {
      return axios.post(`/offer/edit`, {
        JWT: p.get("token"),
        price: (formData?.price || 0) * 100,
        description: formData.description,
        offerDate: format(formData.offerDate, "yyyy-MM-dd") + "T00:00:00",
        id: data?.offerID,
        state: "DONE",
      });
    },
  });

  const rejectMutation = useMutation({
    mutationFn: () => {
      return axios.post(`/offer/edit`, {
        JWT: p.get("token"),
        id: data?.offerID,
        state: "REJECTED",
        price: null,
        description: null,
        offerDate: null,
      });
    },
  });

  const invalidate = () => {
    queryClient.invalidateQueries({
      queryKey: ["checkFormState", p.get("token")],
    });
  };

  const onSubmit = async (data: OfferForm) => {
    await offerMutation.mutateAsync(data);
    invalidate();
    toast.success("Ponuda poslana");
  };

  if (isLoading) {
    return <LoadingScreen />;
  }

  if (formStateData?.done === true) {
    return (
      <Layout>
        <Title>Ponuda</Title>
        <p className={`py-10 text-center`}>Ponuda je već poslana</p>

        <Title>Detalji kalkulacije</Title>
        <CalculationInfo disableEdit calculation={data?.calculation} />
        <p className={`text-sm`}>
          *Dana je okvirna lokacija, prava se otkriva pri dogovoru s klijentom
        </p>
      </Layout>
    );
  }

  return (
    <Layout>
      <Title className={`mb-3`}>Detalji kalkulacije</Title>
      <CalculationInfo disableEdit calculation={data?.calculation} />
      <p className={`mb-3 text-sm`}>
        *Dana je okvirna lokacija, prava se otkriva pri dogovoru s klijentom
      </p>

      <Title>Ponuda</Title>

      <Form {...form}>
        <form className={`mb-28`} onSubmit={form.handleSubmit(onSubmit)}>
          <div className={`flex grow flex-col gap-4 md:flex-row`}>
            <FormField
              control={form.control}
              name="price"
              render={({ field }) => (
                <FormItem className={`w-full basis-1/2`}>
                  <FormLabel>Cijena</FormLabel>

                  <FormControl>
                    <Input
                      trailingIcon={<span>€</span>}
                      placeholder="100"
                      className={`w-full`}
                      {...field}
                    />
                  </FormControl>
                  <FormFooter>
                    <FormDescription>
                      Vaša okvirna cijena za izvedbu radova + materijal
                    </FormDescription>
                  </FormFooter>
                  <FormMessage />
                </FormItem>
              )}
            />
            <FormField
              control={form.control}
              name="offerDate"
              render={({ field }) => (
                <FormItem className="mt-2.5 flex basis-1/2 flex-col">
                  <FormLabel className={``}>Datum početka radova</FormLabel>
                  <Popover>
                    <PopoverTrigger asChild>
                      <FormControl>
                        <Button
                          variant={"outline"}
                          className={cn(
                            "pl-3 text-left font-normal",
                            !field.value && "text-muted-foreground",
                          )}
                        >
                          {field.value ? (
                            format(field.value, "PPP", {
                              locale: hr,
                            })
                          ) : (
                            <span>Pick a date</span>
                          )}
                          <CalendarIcon className="ml-auto h-4 w-4 opacity-50" />
                        </Button>
                      </FormControl>
                    </PopoverTrigger>
                    <PopoverContent className="w-auto p-0" align="start">
                      <Calendar
                        mode="single"
                        selected={field.value}
                        onSelect={field.onChange}
                        disabled={(date) => date < new Date()}
                        initialFocus
                      />
                    </PopoverContent>
                  </Popover>
                  <FormDescription>
                    Unesite datum najranijeg mogućeg početka radova
                  </FormDescription>
                  <FormMessage />
                </FormItem>
              )}
            />
          </div>

          <FormField
            control={form.control}
            name="description"
            render={({ field }) => (
              <FormItem className={`w-full basis-1/2`}>
                <FormLabel>Opis</FormLabel>
                <FormControl>
                  <Textarea
                    placeholder="Unesite opis ponude"
                    className={`w-full`}
                    {...field}
                  />
                </FormControl>
                <FormFooter>
                  <FormDescription>
                    Unesite opis ponude, uključujući sve dodatne informacije
                  </FormDescription>
                </FormFooter>
                <FormMessage />
              </FormItem>
            )}
          />

          <div className={`mt-4 flex gap-4`}>
            <Button
              disabled={rejectMutation.isPending}
              variant={"destructive"}
              type="button"
              className={`basis-1/2`}
              onClick={async () => {
                await rejectMutation.mutateAsync();
                invalidate();
              }}
            >
              Odbij
            </Button>
            <Button type="submit" className={`w-full basis-1/2`}>
              Pošalji ponudu
            </Button>
          </div>
          <div className={`text-sm text-black/60`}>
            *Odbijanjem ponude, korisniku će biti poslana obavijest o odbijanju
          </div>
        </form>
      </Form>
    </Layout>
  );
};
export default From;
