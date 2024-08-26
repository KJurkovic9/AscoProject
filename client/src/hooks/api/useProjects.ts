import { axios } from "@/lib/axios";
import { ProjectsResponse } from "@/types/api";
import { useQuery } from "@tanstack/react-query";

export type Root = {
  message: string;
  projects: Array<{
    id: number;
    name: string;
    calculation: {
      id: number;
      roofSurface: number;
      roofPitch: number;
      roofOrientation: string;
      lat: number;
      lng: number;
      yearlyConsumption: number;
      projectPrice: number;
      profitabiltyYears: number;
      effectiveness: number;
      location: string;
      profitabiltyMonthly: {
        "1": number;
        "2": number;
        "3": number;
        "4": number;
        "5": number;
        "6": number;
        "7": number;
        "8": number;
        "9": number;
        "10": number;
        "11": number;
        "12": number;
      };
      paybackPeroid: number;
      installationPrice: number;
      equipmentPrice: number;
      potentialPower: number;
    };
    user: {
      id: number;
      email: string;
      role: string;
      userProfile: {
        id: number;
        firstName: string;
        lastName: string;
        address: string;
        mobile: string;
        postalCode: string;
        city: {
          name: string;
        };
      };
    };
  }>;
};

export const useProjects = () => {
  return useQuery({
    queryFn: async () => {
      const { data } = await axios.get("/project/get-user-projects");
      return data as ProjectsResponse;
    },
    queryKey: ["projects"],
  });
};
