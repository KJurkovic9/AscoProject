"use client";
import { Toaster } from "@/components/ui/sonner";
import {
  QueryCache,
  QueryClient,
  QueryClientProvider,
} from "@tanstack/react-query";

interface ProvidersProps {
  children?: React.ReactNode | React.ReactNode[];
}

const queryCache = new QueryCache({});

export const queryClient = new QueryClient({
  defaultOptions: {
    queries: {},
  },
  queryCache: queryCache,
});

export const Providers = ({ children }: ProvidersProps) => {
  return (
    <QueryClientProvider client={queryClient}>
      <Toaster />
      {children}
    </QueryClientProvider>
  );
};
