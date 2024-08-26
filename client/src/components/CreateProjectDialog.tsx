"use client";
import { Button } from "@/components/ui/button";
import { Dialog, DialogContent, DialogFooter } from "@/components/ui/dialog";
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
import { useCalculatorState } from "@/hooks/useCalculatorState";
import { axios } from "@/lib/axios";
import { queryClient } from "@/providers";
import { ProjectResponse } from "@/types/api";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation } from "@tanstack/react-query";
import { useRouter } from "next/navigation";
import { useCallback, useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { toast } from "sonner";
import { z } from "zod";

const projectSchema = z.object({
  name: z.string(),
  id: z.number(),
});

type ProjectForm = z.infer<typeof projectSchema>;

interface NewProjectDialogProps {
  children?: React.ReactNode | React.ReactNode[];
}

export const NewProjectDialog = ({ children }: NewProjectDialogProps) => {
  const calculatorStore = useCalculatorState();
  const resetFormData = useCalculatorState((state) => state.resetFormData);

  const [isOpen, setIsOpen] = useState(false);

  const form = useForm({
    resolver: zodResolver(projectSchema),
    defaultValues: {
      name: "",
      id: 0,
    },
  });

  const newProjectMutation = useMutation({
    mutationFn: (project: ProjectForm) => {
      return axios.post(`/project/${project.id}`, {
        name: project.name,
      }) as Promise<ProjectResponse>;
    },
  });

  const router = useRouter();

  const closeAndClear = useCallback(() => {
    setIsOpen(false);
    calculatorStore.clearProjectToCreate();
  }, [calculatorStore]);

  const handleCreateProject = useCallback(async () => {
    try {
      console.log("Creating project");
      const values = form.getValues();
      await newProjectMutation.mutateAsync(values);
      queryClient.invalidateQueries({
        queryKey: ["projects"],
      });
      resetFormData(true);
      router.push(`/app/dashboard`);
      closeAndClear();
    } catch (error) {
      toast.error("Greška prilikom kreiranja projekta");
      console.error(error);
    }
  }, [form, newProjectMutation, resetFormData, router, closeAndClear]);

  useEffect(() => {
    if (calculatorStore.hasProjectToCreate) {
      // setIsOpen(true);
      form.setValue("id", calculatorStore.hasProjectToCreate || 0);
      form.setValue(
        "name",
        calculatorStore.formData.location.split(",").slice(0, 1).join(" - ") ||
          "",
      );
      handleCreateProject();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return (
    <Dialog
      defaultOpen={isOpen}
      open={isOpen}
      onOpenChange={() => {
        closeAndClear();
      }}
    >
      <DialogContent className="z-[99999] grow sm:max-w-[425px]">
        <Form {...form}>
          <form
            onSubmit={form.handleSubmit(handleCreateProject, (e) => {
              console.log(e);
            })}
          >
            <h2 className={`pb-2 text-2xl font-semibold`}>Novi projekt</h2>
            <p>
              Projekt će biti kreiran za kalkulaciju koju ste upravo izračunali,
              nakon kreiranja projekta moći ćete kontaktirati naše partnere za
              ponudu.
            </p>
            <FormField
              control={form.control}
              name="name"
              render={({ field }) => (
                <FormItem className={`mt-8 basis-1/2`}>
                  <FormLabel>Ime</FormLabel>
                  <FormControl>
                    <Input {...field} placeholder="Kućni krov" />
                  </FormControl>
                  <FormDescription>Deskriptivno ime</FormDescription>
                  <FormMessage />
                </FormItem>
              )}
            />
            <DialogFooter>
              <div className={`mt-32 flex grow gap-3`}>
                <Button
                  type="button"
                  disabled={newProjectMutation.isPending}
                  onClick={closeAndClear}
                  className={`basis-1/2`}
                  variant={"destructive"}
                >
                  Odustani
                </Button>
                <Button
                  disabled={newProjectMutation.isPending}
                  type="submit"
                  className={`basis-1/2`}
                >
                  Kreiraj projekt
                </Button>
              </div>
            </DialogFooter>
          </form>
        </Form>
      </DialogContent>
    </Dialog>
  );
};
