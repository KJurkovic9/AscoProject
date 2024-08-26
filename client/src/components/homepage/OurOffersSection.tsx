import { NextPage } from "next";
import LargeHeading from "../ui/LargeHeading";
import OfferCard from "./OfferCard";

interface OurOffersSectionProps {}

const cardItems = [
  {
    title: "Jednostavna i brza procjena vašeg projekta",
    description:
      "Naša platforma procjenjuje isplativost ulaganja u solarne panele, nudeći detaljnu analizu potencijalnih ušteda i povrata investicije.",
    animation: "/assets/project-planning.lottie",
    alt: "asd",
  },
  {
    title: "Mogućnost odabira iz široke ponude kvalificiranih instalatera",
    description:
      "Pružamo pristup raznovrsnoj ponudi instalatera solarnih panela. Pregledajte profile instalatera, pročitajte recenzije drugih korisnika i odaberite onog koji najbolje odgovara vašim potrebama i budžetu.",
    animation: "/assets/choose-option.lottie",
    alt: "nesto2",
  },
  {
    title: "Automatizirana komunikacija s instalaterima solarnih panela",
    description:
      "Imamo brzu i jednostavnu interakciju s instalaterima te nam omogućuje automatski proces dogovora i suradnje na projektima.",
    animation: "/assets/automate-process.lottie",
    alt: "nesto3",
  },
  {
    title: "Savjeti za sufinanciranja iz nekih fondova",
    description:
      "Pružamo savjete o sufinanciranju iz različitih fondova za maksimalno iskorištavanje financijskih potpora i smanjenje troškova projekta.",
    animation: "/assets/funds-rising.lottie",
    alt: "nesto4",
  },
];

const OurOffersSection: NextPage<OurOffersSectionProps> = ({}) => {
  return (
    <div className="flex w-full flex-col items-center space-y-14 bg-white p-0 pb-10 pt-10 lg:p-20">
      <LargeHeading size="sm" className="text-4xl font-bold">
        ŠTO VAM NUDIMO?
      </LargeHeading>
      <div className="grid h-full w-11/12 grid-cols-1 grid-rows-1 gap-10 sm:w-10/12 md:grid-cols-2 md:grid-rows-2">
        {cardItems.map((item, i) => (
          <OfferCard
            key={i}
            title={item.title}
            description={item.description}
            animation={item.animation}
            alt={item.alt}
          />
        ))}
      </div>
    </div>
  );
};

export default OurOffersSection;
