import { env } from "@/lib/env";
export const API_URL = env.NEXT_PUBLIC_API_URL;

// validation
export const requiredError = (value: string) => `Polje ${value} je obavezno`;

export const roofOrientations = {
  Sjever: "S",
  Jug: "J",
  Istok: "I",
  Zapad: "Z",
  Sjeveroistok: "SI",
  Sjeverozapad: "SZ",
  Jugoistok: "JI",
  Jugozapad: "JZ",
} as const;

export const roofOrientationR = {
  S: "Sjever",
  J: "Jug",
  I: "Istok",
  Z: "Zapad",
} as const;

export const roofPitch = {
  0: "0",
  15: "15",
  30: "30",
  34: "34",
  45: "45",
  60: "60",
  90: "90",
} as const;

export const months = [
  "Siječanj",
  "Veljača",
  "Ožujak",
  "Travanj",
  "Svibanj",
  "Lipanj",
  "Srpanj",
  "Kolovoz",
  "Rujan",
  "Listopad",
  "Studeni",
  "Prosinac",
];

//  "SENT" | "REJECTED" | "DONE" | "ACCEPTED" | "CHOSEN"
export const status = {
  SENT: "Ponuda zatražena",
  REJECTED: "Izvođač odbio ponudu",
  DONE: "Izvođač ispunio ponudu",
  CHOSEN: "Odabrano",
  DECLINED: "Odbijeno",
  // not userd
  ACCEPTED: "Ponuda prihvaćena",
};

export const statusBorderColor = {
  SENT: "border-border",
  REJECTED: "border-destructive",
  DONE: "border-border",
  CHOSEN: "border-accent-1",
  ACCEPTED: "border-border",
  DECLINED: "border-destructive",
};

export const statusColor = {
  SENT: "text-border",
  REJECTED: "text-destructive",
  DONE: "text-border",
  CHOSEN: "text-accent-1",
  ACCEPTED: "text-border",
  DECLINED: "text-destructive",
};

export type Status = keyof typeof status;

export const mapboxAccessToken =
  "pk.eyJ1IjoicnJycjQ0NDQyIiwiYSI6ImNrems4dGF0ZzIydW4ydW9jb3Z2N3p2bDgifQ.IH8mYILhqN99BhNTcBRoLg";
