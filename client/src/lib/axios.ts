import { API_URL } from "@/lib/const";
import axioss from "axios";

// create axios instance
export const axios = axioss.create({
  baseURL: API_URL,
  headers: {
    "Content-Type": "application/json",
  },
  withCredentials: true,
});
