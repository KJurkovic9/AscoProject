import { roofOrientations, roofPitch } from "@/lib/const";
import { getValues } from "@/lib/enum";
import { numberError } from "@/lib/utils";
import { z } from "zod";
import { create } from "zustand";
import { persist } from "zustand/middleware";

export const calculateFormSchema = z.object({
  roofPitch: z.enum(getValues(roofPitch)),
  roofSurface: z.coerce
    .number({
      invalid_type_error: numberError("Površina krova"),
    })
    .optional()
    .refine((value) => value !== undefined, {
      message: "Površina krova je obavezna",
    }),
  roofOrientation: z.enum(getValues(roofOrientations)),
  yearlyConsumption: z.coerce
    .number({
      invalid_type_error: numberError("Godišnja potrošnja"),
    })
    .optional()
    .refine((value) => value !== undefined, {
      message: "Godišnja potrošnja je obavezna",
    }),
  budget: z.coerce.number().optional(),
  lifespan: z.coerce.number().optional(),
  lat: z.coerce.number().optional(),
  lng: z.coerce.number().optional(),
  location: z.string().refine((value) => value !== "", {
    message: "Adresa je obavezna",
  }),
});

export type CalculateForm = z.infer<typeof calculateFormSchema>;

interface CalculateState {
  formData: z.infer<typeof calculateFormSchema>;
  setFormData: (data: any) => void;
  formState: "calc" | "result";
  setFormState: (state: "calc" | "result") => void;
  _hasHydrated: boolean;
  setHasHydrated: (state: boolean) => void;
  resetFormData: (state: boolean) => void;
  hasProjectToCreate?: number;
  setProjectToCreate: (state?: number) => void;
  clearProjectToCreate: () => void;
}

export const useCalculatorState = create<CalculateState>()(
  persist(
    (set) => ({
      _hasHydrated: false,
      setHasHydrated: (state: boolean) => set({ _hasHydrated: state }),
      formData: {
        roofPitch: "0",
        roofOrientation: "J",
        location: "",
        budget: 0,
        lifespan: 0,
        lat: 15.9763612,
        lng: 45.8049505,
      },
      setFormData: (data: any) => set({ formData: data }),
      formState: "calc",
      setFormState: (state) => set({ formState: state }),
      hasProjectToCreate: undefined,
      setProjectToCreate: (state) => set({ hasProjectToCreate: state }),
      clearProjectToCreate: () => {
        set({ hasProjectToCreate: undefined, formState: "calc" });
      },
      resetFormData: () => {
        set({
          formData: {
            roofPitch: "0",
            roofOrientation: "J",
            location: "",
            budget: 0,
            lifespan: 0,
            lat: 15.9763612,
            lng: 45.8049505,
          },
          formState: "calc",
          hasProjectToCreate: undefined,
        });
      },
    }),
    {
      name: "calcState",
      onRehydrateStorage: () => (state) => {
        state?.setHasHydrated(true);
      },
    },
  ),
);
