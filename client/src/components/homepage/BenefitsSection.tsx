"use client";

import { NextPage } from "next";
import Image from "next/image";
import { Accordion } from "../ui/accordion";
import BenefitCard from "./BenefitCard";

interface BenefitsSectionProps {}

const benefitItems = [
  {
    title: "Ušteda na troškovima električne energije",
    description:
      "Investicija u solarne panele ne samo da smanjuje vaše mjesečne račune za struju, već osigurava dugoročne uštede i stabilnost u financijskom planiranju. Kroz vlastitu proizvodnju električne energije, postajete manje osjetljivi na promjene tržišnih cijena energije, pružajući vam kontrolu nad vašim budžetom i dugoročnom sigurnošću.",
    svg: (
      <svg
        width="45px"
        height="45px"
        strokeWidth="1.2"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        color="#000000"
      >
        <path
          d="M14.5 8.5C13.7193 8.29761 12.6344 8 11.7647 8C7.47636 8 4 10.6676 4 13.9583C4 15.8493 5.14794 17.5345 6.93824 18.6261L6.45318 20.2259C6.33635 20.6112 6.62471 21 7.02736 21H8.79147C8.92135 21 9.04773 20.9579 9.15161 20.8799L10.5462 19.8333H12.9831L14.3777 20.8799C14.4816 20.9579 14.608 21 14.7379 21H16.502C16.9046 21 17.193 20.6112 17.0761 20.2259L16.5911 18.6261C17.6577 17.9758 18.4963 17.1147 19 16.125"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
          strokeLinejoin="round"
        ></path>
        <path
          d="M14.5 8.5L19 7L18.916 10.6283L21 11.5V15L19.0741 16"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
          strokeLinejoin="round"
        ></path>
        <path
          d="M15.5 13C15.2239 13 15 12.7761 15 12.5C15 12.2239 15.2239 12 15.5 12C15.7761 12 16 12.2239 16 12.5C16 12.7761 15.7761 13 15.5 13Z"
          fill="black"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
          strokeLinejoin="round"
        ></path>
        <path
          d="M2 10C2 10 2 12.4 4 13"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
          strokeLinejoin="round"
        ></path>
        <path
          d="M12.8008 7.75296C12.9298 7.38131 13 6.98136 13 6.56472C13 4.59598 11.433 3 9.5 3C7.567 3 6 4.59598 6 6.56472C6 7.50638 6.35849 8.36275 6.94404 9"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinejoin="round"
        ></path>
      </svg>
    ),
  },
  {
    title: "Sigurnost opskrbe električnom energijom",
    description:
      "Solarni paneli osiguravaju kontinuiranu opskrbu električnom energijom, čak i u slučaju prekida mrežnog napajanja. Ovo osigurava vašu sigurnost i udobnost, bez obzira na vanjske uvjete ili probleme s opskrbom energijom.",
    svg: (
      <svg
        width="45px"
        height="45px"
        strokeWidth="1.2"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        color="#000000"
      >
        <path
          d="M10 13.1538V21"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
        ></path>
        <path
          d="M15 8.38452V11.1538C15 12.2583 14.1046 13.1538 13 13.1538H7C5.89543 13.1538 5 12.2583 5 11.1538V8.38452C5 7.27995 5.89543 6.38452 7 6.38452H13C14.1046 6.38452 15 7.27995 15 8.38452Z"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
        ></path>
        <path
          d="M13.3334 6.38462V3"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
        ></path>
        <path
          d="M6.66663 6.38462V3"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
        ></path>
        <path
          d="M16.6667 16L15 19H19L17.3333 22"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
          strokeLinejoin="round"
        ></path>
      </svg>
    ),
  },
  {
    title: "Neovisnost od promjena cijena električne energije",
    description:
      "Korištenjem solarnih panela postajete manje osjetljivi na promjene cijena električne energije na tržištu. Proizvodnja vlastite električne energije pruža vam stabilnost i predvidljivost u financijskom planiranju, osiguravajući da nećete biti pogođeni naglim porastima cijena energije.",
    svg: (
      <svg
        width="45px"
        height="45px"
        strokeWidth="1.2"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        color="#000000"
      >
        <path
          d="M23 10V14"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
          strokeLinejoin="round"
        ></path>
        <path
          d="M1 16V8C1 6.89543 1.89543 6 3 6H18C19.1046 6 20 6.89543 20 8V16C20 17.1046 19.1046 18 18 18H3C1.89543 18 1 17.1046 1 16Z"
          stroke="#000000"
          strokeWidth="1.2"
        ></path>
        <path
          d="M10.1667 9L8.5 12H12.5L10.8333 15"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
          strokeLinejoin="round"
        ></path>
      </svg>
    ),
  },
  {
    title: "Povećanje vrijednosti nekretnine",
    description:
      "Instalacija solarnih panela ne samo da smanjuje vaše troškove i osigurava ekološku održivost, već može povećati vrijednost vaše nekretnine. Potencijalni kupci cijene energetski učinkovite domove s niskim troškovima održavanja, što čini vašu nekretninu atraktivnijom na tržištu i donosi dodatnu vrijednost vašoj imovini.",
    svg: (
      <svg
        width="45px"
        height="45px"
        viewBox="0 0 24 24"
        strokeWidth="1.2"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        color="#000000"
      >
        <path
          d="M10 18V15C10 13.8954 10.8954 13 12 13V13C13.1046 13 14 13.8954 14 15V18"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
          strokeLinejoin="round"
        ></path>
        <path
          d="M2 8L11.7317 3.13416C11.9006 3.04971 12.0994 3.0497 12.2683 3.13416L22 8"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
          strokeLinejoin="round"
        ></path>
        <path
          d="M20 11V19C20 20.1046 19.1046 21 18 21H6C4.89543 21 4 20.1046 4 19V11"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
          strokeLinejoin="round"
        ></path>
      </svg>
    ),
  },
  {
    title: "Smanjenje emisija stakleničkih plinova",
    description:
      "Korištenjem solarne energije umanjite negativan utjecaj na okoliš smanjujući emisije stakleničkih plinova. Ova održiva praksa pridonosi zaštiti okoliša, pomažući u očuvanju prirodnih resursa i smanjenju globalnog zagrijavanja.",
    svg: (
      <svg
        width="45px"
        height="45px"
        strokeWidth="1.2"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        color="#000000"
      >
        <path
          d="M18.2785 7C19.7816 7 21 8.11929 21 9.5C21 10.8807 19.7816 12 18.2785 12H3"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
          strokeLinejoin="round"
        ></path>
        <path
          d="M17.9375 20C19.0766 20 20.5 19.5 20.5 17.5C20.5 15.5 19.0766 15 17.9375 15H3"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
          strokeLinejoin="round"
        ></path>
        <path
          d="M10.4118 4C11.8412 4 13 5.11929 13 6.5C13 7.88071 11.8412 9 10.4118 9H3"
          stroke="#000000"
          strokeWidth="1.2"
          strokeLinecap="round"
          strokeLinejoin="round"
        ></path>
      </svg>
    ),
  },
  {
    title: "Minimalna potreba za održavanjem",
    description:
      "Solarni paneli zahtijevaju minimalno održavanje, smanjujući napore potrebne za njihovu brigu. Ovo znači manje vremena i novca uloženog u održavanje sustava, omogućujući vam da se fokusirate na uživanje u prednostima solarnih panela bez suvišnih poteškoća.",
    svg: (
      <svg
        width="45px"
        height="45px"
        strokeWidth="1.2"
        viewBox="0 0 24 24"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
        color="#000000"
      >
        <path
          d="M3 20.4V3.6C3 3.26863 3.26863 3 3.6 3H20.4C20.7314 3 21 3.26863 21 3.6V20.4C21 20.7314 20.7314 21 20.4 21H3.6C3.26863 21 3 20.7314 3 20.4Z"
          stroke="#000000"
          strokeWidth="1.2"
        ></path>
        <path d="M3 16.5H21" stroke="#000000" strokeWidth="1.2"></path>
        <path d="M3 12H21" stroke="#000000" strokeWidth="1.2"></path>
        <path d="M21 7.5H3" stroke="#000000" strokeWidth="1.2"></path>
        <path d="M12 21V3" stroke="#000000" strokeWidth="1.2"></path>
      </svg>
    ),
  },
];

const BenefitsSection: NextPage<BenefitsSectionProps> = ({}) => {
  return (
    <div className="w-full h-full bg-white p-0 lg:p-10 xl:p-20">
      <div className="w-full h-full bg-background flex flex-col min-[940px]:flex-row p-10 lg:p-20 rounded-3xl min-[940px]:space-x-20">
        <div className="w-full lg:w-3/5 h-full flex flex-col items-center">
          <div className="text-left font-bold tracking-tighter text-xl md:text-3xl lg:text-3xl">
            ISTRAŽITE BENEFITE KOJE VAM DONOSE OBNOVLJIVI IZVORI ENERGIJE
          </div>
          <div className="w-full h-64 sm:h-96 relative">
            <Image src="/images/benefits.svg" priority fill alt="asd" />
          </div>
        </div>
        <div className="w-full">
          <Accordion type="single" collapsible>
            {benefitItems.map((item, i) => (
              <BenefitCard
                key={i}
                title={item.title}
                description={item.description}
                itemValue={`item-${i}`}
              >
                {item.svg}
              </BenefitCard>
            ))}
          </Accordion>
        </div>
      </div>
    </div>
  );
};

export default BenefitsSection;
