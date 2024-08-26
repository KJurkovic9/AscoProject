"use client";
import { z } from "zod";

const schema = z.object({
  NEXT_PUBLIC_API_URL: z.string(),
});

export const env = schema.parse({
  NEXT_PUBLIC_API_URL: process.env.NEXT_PUBLIC_API_URL,
});
