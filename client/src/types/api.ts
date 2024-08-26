export type Calculation = {
  id: number;
  roofSurface: number;
  roofPitch: number;
  roofOrientation: string;
  lat: number;
  lng: number;
  lifespan: any;
  budget: any;
  projectPrice: number;
  profitabiltyYears: number;
  effectiveness: number;
  owner: any;
  yearlyConsumption: number;
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

export type CalculationResult = {
  message: string;
  calculation: Calculation;
};

export type UserProfile = {
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

export type BaseUser = {
  id: number;
  email: string;
  role: string;
  timeCreated: string;
};

export type User = BaseUser & {
  userProfile: UserProfile;
};

export type Project = {
  id: number;
  name: string;
  calculation: Calculation;
  user: User;
};

export type ProjectsResponse = {
  message: string;
  projects: Project[];
};

export type Review = {
  id: number;
  text: string;
  rating: number;
};

export type Company = {
  id: number;
  name: string;
  email: string;
  lat: number;
  lng: number;
  radius: number;
  mobile: string;
  location: string;
  reviewAverage: number;
  url?: string;
};

export type ReviewWithOwnerPublic = Review & {
  owner: {
    id: number;
  };
};

export type CompanyWithReviews = Company & {
  reviews: ReviewWithOwnerPublic[];
};

export type CompaniesResponse = {
  message: string;
  companies: CompanyWithReviews[];
};

export type OfferState =
  | "SENT"
  | "REJECTED"
  | "DONE"
  | "ACCEPTED"
  | "CHOSEN"
  | "DECLINED";

export type FulfilledOffer = {
  id: number;
  state: "DONE" | "ACCEPTED" | "CHOSEN" | "DECLINED";
  price: number;
  description: string;
  offerDate: string;
};

export type UnfulfilledOffer = {
  id: number;
  state: "SENT" | "REJECTED";
  price: null;
  description: null;
  offerDate: null;
};

export type Offer = FulfilledOffer | UnfulfilledOffer;

export function isOfferFulfilled(offer: Offer): offer is FulfilledOffer {
  return (
    offer.state === "DONE" ||
    offer.state === "ACCEPTED" ||
    offer.state === "CHOSEN" ||
    offer.state === "DECLINED"
  );
}

export type OfferWithCompany = Offer & {
  company: Company;
};

export type ProjectResponse = {
  message: string;
  project: Project;
  offers: OfferWithCompany[];
};

export type FormJwt = {
  iat: number;
  exp: number;
  projectID: number;
  companyID: number;
  offerID: number;
  calculation: Calculation;
};
