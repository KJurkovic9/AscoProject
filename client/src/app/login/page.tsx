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
import { prefetchLoggedIn, useLoggedIn } from "@/hooks/api/useLoggedin";
import { axios } from "@/lib/axios";
import { useMutation } from "@tanstack/react-query";
import { AxiosError } from "axios";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { useEffect } from "react";

const loginShema = z.object({
  rememberMe: z.boolean().default(true),
  email: z.string().email("Unesite ispravnu email adresu"),
  password: z.string().min(8, "Lozinka mora imati minimalno 8 znakova"),
});
interface LoginProps {}

const Login = ({}: LoginProps) => {
  const loggedIn = useLoggedIn();
  const router = useRouter();

  useEffect(() => {
    if (loggedIn.data) {
      router.replace("/app/dashboard");
    }
  }, [loggedIn, router]);

  const loginForm = useForm<z.infer<typeof loginShema>>({
    resolver: zodResolver(loginShema),
    defaultValues: {
      email: "",
      password: "",
      rememberMe: true,
    },
  });

  const loginMutation = useMutation({
    mutationFn: async (values: z.infer<typeof loginShema>) => {
      try {
        await axios.post("/login", {
          email: values.email,
          password: values.password,
          rememberMe: values.rememberMe,
        });
        return values;
      } catch (error) {
        return error as AxiosError;
      }
    },
  });

  async function onSubmit(values: z.infer<typeof loginShema>) {
    const r = await loginMutation.mutateAsync(values);
    if (r instanceof Error) {
      loginForm.setError("email", {
        message: "Pogrešan email ili lozinka",
      });
      loginForm.setError("password", {
        message: "Pogrešan email ili lozinka",
      });

      return;
    }

    setTimeout(() => {
      prefetchLoggedIn();
    }, 400);

    setTimeout(() => {
      router.replace("/app/dashboard");
    }, 1200);
  }

  return (
    <div className={`-mt-16 flex w-full grow items-center justify-center`}>
      <div className={`flex max-w-sm grow flex-col gap-1`}>
        <h1 className={`text-2xl font-medium`}>Prijavite se</h1>
        <div className={`flex gap-1`}>
          <p className={`text-black text-opacity-60`}>Nemate Asco račun?</p>
          <p className={`hover:underline`}>
            <Link href="/register">Registriraj se</Link>
          </p>
        </div>
        <Form {...loginForm}>
          <form
            onSubmit={loginForm.handleSubmit(onSubmit)}
            className="flex flex-col gap-5 pt-10"
          >
            <FormField
              control={loginForm.control}
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
              control={loginForm.control}
              name="password"
              render={({ field }) => (
                <FormItem className={``}>
                  <FormLabel>Lozinka</FormLabel>
                  <FormControl>
                    <Input placeholder="lozinka" type="password" {...field} />
                  </FormControl>
                  <FormMessage />
                </FormItem>
              )}
            />

            <FormField
              control={loginForm.control}
              name="rememberMe"
              render={({ field }) => (
                <FormItem className={`flex flex-col gap-3`}>
                  <div className={`flex items-center gap-2`}>
                    <FormControl>
                      <Checkbox
                        checked={field.value}
                        onCheckedChange={field.onChange}
                      />
                    </FormControl>
                    <FormLabel>Zapamti me</FormLabel>
                  </div>
                  <FormMessage />
                </FormItem>
              )}
            />

            <Button
              disabled={loginMutation.isPending}
              type="submit"
              className={`rounded-md bg-primary p-2 text-white`}
            >
              Prijavi se
            </Button>
          </form>
        </Form>
      </div>
    </div>
  );
};
export default Login;
