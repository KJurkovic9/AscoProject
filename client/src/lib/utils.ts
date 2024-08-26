import { mapboxAccessToken } from "@/lib/const";
import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export const isClient = typeof window !== "undefined";

export const numberError = (message: string) => `${message} mora biti broj!`;

export const currencyFormat = (value?: number) => {
  if (!value) value = 0;
  return new Intl.NumberFormat("hr-HR", {
    style: "currency",
    currency: "EUR",
    compactDisplay: "short",
  }).format(value / 100);
};

export const formatFullName = (firstName?: string, lastName?: string) => {
  return `${firstName} ${lastName}`;
};

export const mapboxImg = (lat: number, lng: number, zoom = 14) => {
  return `https://api.mapbox.com/styles/v1/rrrr44442/cltujkjnh008b01qshwd6dmkk/static/pin-s-m+1e1e1e(${lat},${lng})/${lat},${lng},15/1200x1200?access_token=${mapboxAccessToken}`;
};

export const formatDate = (date?: string) => {
  return new Date(date ?? new Date()).toLocaleDateString("hr-HR", {
    year: "numeric",
    month: "long",
    day: "numeric",
  });
};
