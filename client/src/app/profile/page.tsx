"use client";
import {
  Form,
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm } from "react-hook-form";
import { z } from "zod";

import { Button } from "@/components/ui/button";
import { axios } from "@/lib/axios";
import { useMutation } from "@tanstack/react-query";
import { useRouter } from "next/navigation";

const profileShema = z.object({
  name: z.string().min(1, "Ime je obavezno"),
  surname: z.string().min(1, "Prezime je obavezno"),
  mobile: z.string(),
  // .regex(
  //   /^(\+\d{1,2}\s)?\(?\d{3}\)?[\s.-]\d{3}[\s.-]\d{4}$/,
  //   "Unesite ispravan broj mobitela"
  // ),
});

interface RegisterProps {}

const Register = ({}: RegisterProps) => {
  const registerForm = useForm<z.infer<typeof profileShema>>({
    resolver: zodResolver(profileShema),
    defaultValues: {
      mobile: "",
      name: "",
      surname: "",
    },
  });

  const router = useRouter();

  const registerMutation = useMutation({
    mutationFn: async (values: z.infer<typeof profileShema>) => {
      try {
        await axios.post("/user-profile/create", {
          firstName: values.name,
          lastName: values.surname,
          phone_number: values.mobile,
        });
        router.replace("/app/dashboard");
      } catch (error) {
        console.log(error);
      }
      console.log(values);
      return values;
    },
  });

  async function onSubmit(values: z.infer<typeof profileShema>) {
    await registerMutation.mutateAsync(values);
  }

  return (
    <div className={`-mt-16 flex w-full grow items-center justify-center`}>
      <div className={`flex grow flex-col gap-1 px-6 md:max-w-md`}>
        <h1 className={`text-xl font-medium`}>Još par podataka o tebi</h1>
        <div className={`flex gap-1`}>
          <p className={`text-black/60`}>
            Kako bi smo te lakše kontaktirali u vezi novih ponuda
          </p>
        </div>
        <Form {...registerForm}>
          <form
            onSubmit={registerForm.handleSubmit(onSubmit)}
            className="flex flex-col gap-3 pt-10"
          >
            <div className={`flex gap-3`}>
              <FormField
                control={registerForm.control}
                name="name"
                render={({ field }) => (
                  <FormItem className={`basis-1/2`}>
                    <FormLabel>Ime</FormLabel>
                    <FormControl>
                      <Input
                        placeholder="Mirko"
                        className={`w-full`}
                        {...field}
                      />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={registerForm.control}
                name="surname"
                render={({ field }) => (
                  <FormItem className={`basis-1/2`}>
                    <FormLabel>Prezime</FormLabel>
                    <FormControl>
                      <Input placeholder="Mikic" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div>
            {/* <FormField
              control={registerForm.control}
              name="address"
              render={({ field }) => (
                <FormItem className={``}>
                  <FormLabel>Adresa</FormLabel>
                  <FormControl>
                    <Input placeholder="Darkova ulica 5" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            /> */}
            {/* 
            <div className={`flex gap-3`}>
              <FormField
                control={registerForm.control}
                name="city"
                render={({ field }) => (
                  <FormItem className={``}>
                    <FormLabel>Grad</FormLabel>
                    <FormControl>
                      <Input placeholder="Zagreb" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <FormField
                control={registerForm.control}
                name="postalCode"
                render={({ field }) => (
                  <FormItem className={``}>
                    <FormLabel>Poštanski broj</FormLabel>
                    <FormControl>
                      <Input placeholder="10000" {...field} />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                )}
              />
            </div> */}

            <FormField
              control={registerForm.control}
              name="mobile"
              render={({ field }) => (
                <FormItem>
                  <FormLabel>Broj telefona</FormLabel>
                  <FormControl>
                    <Input placeholder="091 123 4567" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <div className={`flex grow pt-8`}>
              <Button
                disabled={registerMutation.isPending}
                type="submit"
                className={`w-full rounded-md bg-primary p-2 text-white`}
              >
                Spremi
              </Button>
            </div>
          </form>
        </Form>
      </div>
    </div>
  );
};
export default Register;
