"use client";
/* eslint-disable @next/next/no-img-element */
import {
  CalculatePageLayout,
  CalculatePageMain,
  CalculatePageSide,
} from "@/components/calc/CalculatePageLayout";
import { CalculationResults } from "@/components/calc/CalculationResults";
import { StatInfo } from "@/components/FormInfo";
import { LoadingScreen } from "@/components/Loading";
import { Button } from "@/components/ui/button";
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Title } from "@/components/ui/title";
import {
  CalculateForm,
  calculateFormSchema,
  useCalculatorState,
} from "@/hooks/useCalculatorState";
import { useMapImg } from "@/hooks/useMapImg";
import { axios } from "@/lib/axios";
import { roofOrientations, roofPitch } from "@/lib/const";
import { CalculationResult } from "@/types/api";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { Send, Sparks } from "iconoir-react";
import dynamic from "next/dynamic";
import { useEffect, useRef, useState } from "react";
import { useForm } from "react-hook-form";
import { toast } from "sonner";
import { z } from "zod";

const DynamicSearchBox = dynamic(
  () =>
    import("@/components/LocationSearchBox").then(
      (mod) => mod.LocationSearchBox,
    ),
  {
    ssr: false,
    loading: () => <Input placeholder="Avenija Dubrovnik 15" />,
  },
);

const DynamicAreaPicker = dynamic(
  () => import("@/components/AreaPicker").then((mod) => mod.AreaPicker),
  {
    ssr: false,
  },
);

interface CalculatePageProps {}

const CalculatePage = ({}: CalculatePageProps) => {
  const store = useCalculatorState();
  const hasSet = useRef(false);

  const form = useForm<CalculateForm>({
    resolver: zodResolver(calculateFormSchema),
    defaultValues: store?.formData,
  });

  const calculateMutation = useMutation({
    mutationFn: async (data: CalculateForm) => {
      const r = await axios.post<CalculationResult>("/calculation/calculate", {
        lng: data.lng,
        lat: data.lat,
        yearlyConsumption: data.yearlyConsumption,
        roofSurface: data.roofSurface,
        roofPitch: data.roofPitch,
        roofOrientation: data.roofOrientation,
        location: data.location,
      });
      return r;
    },
  });

  useEffect(() => {
    const actionAsync = async () => {
      await calculateMutation.mutateAsync(store.formData);
    };

    if (store._hasHydrated && !hasSet.current) {
      setTimeout(() => {
        form.reset(store.formData);
      }, 22);
      hasSet.current = true;
      if (store.formState === "result") {
        actionAsync();
        return;
      }
    }
  }, [
    calculateMutation,
    form,
    store._hasHydrated,
    store.formData,
    store.formState,
  ]);

  async function onSubmit(values: z.infer<typeof calculateFormSchema>) {
    try {
      store.setFormState("result");
      await calculateMutation.mutateAsync(values);
      store.setFormData(values);
    } catch (error) {
      store.setFormState("calc");
      console.error(error);
      toast.error("Greška prilikom izračuna, molimo pokušajte ponovno.");
    }
  }
  const lat = form.watch("lat");
  const lng = form.watch("lng");

  const mapImage = useMapImg(lat, lng);

  const [areaDialogOpen, setAreaDialogOpen] = useState(false);

  if (store._hasHydrated === false) {
    return <LoadingScreen />;
  }

  if (store.formState === "result") {
    return (
      <CalculatePageLayout>
        <CalculatePageSide>
          {!calculateMutation.isSuccess ? (
            <LoadingScreen />
          ) : (
            <CalculationResults
              calculation={calculateMutation.data?.data?.calculation}
            />
          )}
        </CalculatePageSide>
        <CalculatePageMain>
          <div
            className={`absolute left-0 top-0 h-10 w-full bg-gradient-to-b from-background to-transparent`}
          />
          <div
            className={`absolute left-0 top-0 h-full w-10 bg-gradient-to-r from-background to-transparent`}
          />
          <div
            className={`absolute bottom-0 left-0 h-10 w-full bg-gradient-to-t from-background to-transparent`}
          />
          <img
            src={mapImage}
            alt="map-img"
            className={`h-full max-h-[calc(100vh-64px)] w-full object-cover`}
          />
        </CalculatePageMain>
      </CalculatePageLayout>
    );
  }

  return (
    <CalculatePageLayout>
      <DynamicAreaPicker
        open={areaDialogOpen}
        onOpenChange={setAreaDialogOpen}
        onSelected={(t) => {
          form.setValue("roofSurface", t);
        }}
        initialLocation={[lat, lng] as [number, number]}
      />
      <CalculatePageSide>
        <Form {...form}>
          <form
            onSubmit={form.handleSubmit(onSubmit)}
            className="flex h-full w-full flex-col"
          >
            <div className={`flex flex-col gap-4`}>
              <Title>Izračunajte solarnu isplativost</Title>
              <h2 className={`text-lg font-medium`}>Podatci o površini</h2>
              <FormField
                control={form.control}
                name="location"
                render={({ field }) => {
                  return (
                    <FormItem className={`flex flex-col gap-[1px]`}>
                      <FormLabel className="text-[15px]">
                        Adresa Vašeg objekta
                      </FormLabel>
                      <FormControl>
                        <DynamicSearchBox
                          value={field.value}
                          onRetrieve={(data) => {
                            form.setValue("lat", data[0]);
                            form.setValue("lng", data[1]);
                          }}
                          setValue={field.onChange}
                        />
                      </FormControl>
                      <FormMessage />
                    </FormItem>
                  );
                }}
              />

              <FormField
                control={form.control}
                name="roofPitch"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel className="text-[15px]">
                      Nagib krova u stupnjevima
                    </FormLabel>
                    <StatInfo>
                      Nagib krova u stupnjevima. Ako ne znate nagib krova,
                      izaberite nagib krova prema sljedećim smjernicama:
                      <ul className={`list-inside list-disc`}>
                        <li>15° - 25°: blagi nagib</li>
                        <li>25° - 45°: srednji nagib</li>
                        <li>45° - 60°: strm nagib</li>
                      </ul>
                    </StatInfo>
                    <FormControl>
                      <Select
                        onValueChange={field.onChange}
                        value={field.value}
                      >
                        <SelectTrigger className="">
                          <SelectValue
                            className={`basis-1/2`}
                            placeholder="Izaberi kut"
                          />
                        </SelectTrigger>
                        <SelectContent>
                          {Object.entries(roofPitch).map(([key, value]) => (
                            <SelectItem
                              key={key}
                              value={value}
                              className="text-[15px]"
                            >
                              {key}
                            </SelectItem>
                          ))}
                        </SelectContent>
                      </Select>
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={form.control}
                name="roofSurface"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel className="text-[15px]">
                      Površina krova u m²
                    </FormLabel>
                    <StatInfo>
                      Površina krova u m². Ako ne znate točnu površinu krova,
                      možete ju izračunati tako da pomnožite duljinu i širinu
                      krova.
                    </StatInfo>
                    <FormControl>
                      <Input
                        trailingIcon={<span>m²</span>}
                        placeholder="100"
                        className={`w-full`}
                        {...field}
                      />
                    </FormControl>

                    <Button
                      onClick={() => {
                        setAreaDialogOpen(true);
                      }}
                      type="button"
                      variant={"accent"}
                      size={"link"}
                      className={`mt-2 flex gap-1`}
                    >
                      <Sparks
                        strokeWidth={1.6}
                        className={`fill-current text-accent-1`}
                        width={16}
                        height={16}
                      />
                      Izaberi površinu na karti
                    </Button>

                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={form.control}
                name="roofOrientation"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel className="text-[15px]">
                      Orijentacija postavljanja solarnih panela
                    </FormLabel>
                    <StatInfo>
                      Iskoristite kompas kako bi odredili smjer krova ukoliko
                      niste sigurni.
                    </StatInfo>
                    <FormControl>
                      <Select
                        onValueChange={field.onChange}
                        value={field.value}
                      >
                        <SelectTrigger className="">
                          <SelectValue
                            className={`basis-1/2`}
                            placeholder="Izaberi smjer"
                          />
                        </SelectTrigger>
                        <SelectContent>
                          {Object.entries(roofOrientations).map(
                            ([key, value]) => (
                              <SelectItem key={key} value={value}>
                                {key}
                              </SelectItem>
                            ),
                          )}
                        </SelectContent>
                      </Select>
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>
            <div className={`flex flex-col gap-2`}>
              <h2 className={`pt-6 text-lg font-medium`}>
                Podatci o potrošnji
              </h2>

              <FormField
                control={form.control}
                name="yearlyConsumption"
                render={({ field }) => (
                  <FormItem>
                    <FormLabel className="text-[15px]">
                      Godišnja potrošnja električne energije u kWh
                    </FormLabel>
                    <StatInfo>
                      Godišnja potrošnja električne energije u kWh, možete
                      pronaći na računu za struju. Približna potrošnja za
                      kućanstvo je oko 5000 kWh godišnje.
                    </StatInfo>
                    <FormControl>
                      <Input
                        trailingIcon={<span>kWh</span>}
                        placeholder="5000"
                        className=""
                        {...field}
                      />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>
            <div className={`mt-4 flex grow flex-col justify-end gap-2`}>
              <Button
                disabled={calculateMutation.isPending}
                type="submit"
                className={`flex gap-2`}
              >
                Izračunaj
                <Send strokeWidth={1.6} />
              </Button>
            </div>
          </form>
        </Form>
      </CalculatePageSide>
      <CalculatePageMain>
        <div
          className={`absolute left-0 top-0 h-10 w-full bg-gradient-to-b from-background to-transparent`}
        />
        <div
          className={`absolute left-0 top-0 h-full w-10 bg-gradient-to-r from-background to-transparent`}
        />
        <div
          className={`absolute bottom-0 left-0 h-10 w-full bg-gradient-to-t from-background to-transparent`}
        />

        <img
          src={mapImage}
          alt="map-img"
          className={`h-full max-h-[calc(100vh-64px)] w-full object-cover`}
        />
      </CalculatePageMain>
    </CalculatePageLayout>
  );
};

export default CalculatePage;
