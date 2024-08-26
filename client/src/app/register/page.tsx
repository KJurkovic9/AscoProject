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
import { Checkbox } from "@/components/ui/checkbox";
import { axios } from "@/lib/axios";
import { useMutation } from "@tanstack/react-query";
import Link from "next/link";
import { useRouter } from "next/navigation";
interface RegisterProps {}

const registerShema = z
  .object({
    rememberMe: z.boolean().default(true),
    email: z.string().email("Unesite ispravnu email adresu"),
    password: z.string().min(8, "Lozinka mora imati minimalno 8 znakova"),
    dataSharing: z.boolean().refine((data) => !!data, {
      message: "Morate prihvatiti uvjete korištenja",
    }),
    passwordConfirmation: z
      .string()
      .min(8, "Lozinka mora imati minimalno 8 znakova"),
  })
  .refine((data) => data.password === data.passwordConfirmation, {
    message: "Lozinke se ne poklapaju",
    path: ["passwordConfirmation"],
  });

const Register = ({}: RegisterProps) => {
  const registerForm = useForm<z.infer<typeof registerShema>>({
    resolver: zodResolver(registerShema),
    defaultValues: {
      email: "",
      password: "",
      dataSharing: false,
      passwordConfirmation: "",
      rememberMe: true,
    },
  });

  const router = useRouter();

  const registerMutation = useMutation({
    mutationFn: async (values: z.infer<typeof registerShema>) => {
      try {
        await axios.post("/register", {
          email: values.email,
          password: values.password,
        });
        return values;
      } catch (error) {
        return error as Error;
      }
    },
  });

  async function onSubmit(values: z.infer<typeof registerShema>) {
    const r = await registerMutation.mutateAsync(values);
    if (r instanceof Error) {
      registerForm.setError("email", {
        message: "Korisnik s ovim emailom već postoji",
      });
    } else {
      router.replace("/profile");
    }
  }

  return (
    <div className={`-mt-16 flex w-full grow items-center justify-center`}>
      <div className={`flex max-w-sm grow flex-col gap-1`}>
        <h1 className={`text-2xl font-medium`}>Registrirajte se</h1>
        <div className={`flex gap-1`}>
          <p className={`text-black text-opacity-60`}> Već imate Asco račun?</p>
          <p className={`hover:underline`}>
            <Link href="/login">Prijavite se</Link>
          </p>
        </div>
        <Form {...registerForm}>
          <form
            onSubmit={registerForm.handleSubmit(onSubmit)}
            className="flex flex-col gap-5 pt-10"
          >
            <FormField
              control={registerForm.control}
              name="email"
              render={({ field }) => (
                <FormItem className={`w-full`}>
                  <FormLabel>Email</FormLabel>
                  <FormControl>
                    <Input
                      placeholder="moj@email.com"
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
              name="password"
              render={({ field }) => (
                <FormItem className={``}>
                  <FormLabel>Loznika</FormLabel>
                  <FormControl>
                    <Input placeholder="Lozinka" type="password" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />
            <FormField
              control={registerForm.control}
              name="passwordConfirmation"
              render={({ field }) => (
                <FormItem className={``}>
                  <FormLabel>Potvrdi lozinku</FormLabel>
                  <FormControl>
                    <Input
                      placeholder="Potvrdi lozinku"
                      type="password"
                      {...field}
                    />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={registerForm.control}
              name="dataSharing"
              render={({ field }) => (
                <FormItem className={`flex flex-col gap-3`}>
                  <div className={`flex items-center gap-2`}>
                    <FormControl>
                      <Checkbox
                        checked={field.value}
                        onCheckedChange={field.onChange}
                      />
                    </FormControl>
                    <FormLabel>
                      Slažem se s uvjetima korištenja i privatnosti
                    </FormLabel>
                  </div>
                  <FormMessage />
                </FormItem>
              )}
            />

            <Button
              disabled={registerMutation.isPending}
              type="submit"
              className={`rounded-md bg-primary p-2 text-white`}
            >
              Registrirajte se
            </Button>
          </form>
        </Form>
      </div>
    </div>
  );
};
export default Register;
