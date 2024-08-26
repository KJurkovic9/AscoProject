import BenefitsSection from '@/components/homepage/BenefitsSection';
import BuildTogetherSection from '@/components/homepage/BuildTogetherSection';
import Footer from '@/components/homepage/Footer';
import LandingSection from '@/components/homepage/LandingSection';
import OurOffersSection from '@/components/homepage/OurOffersSection';

export default function Home() {
  return (
    <>
      <LandingSection />
      <OurOffersSection />
      <BuildTogetherSection />
      <BenefitsSection />
      <Footer />
    </>
  );
}
