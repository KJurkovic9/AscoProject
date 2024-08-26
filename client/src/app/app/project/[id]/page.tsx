"use client";
/* eslint-disable @next/next/no-img-element */
import { Layout } from "@/components/Layout";
import { LoadingScreen } from "@/components/Loading";
import { CalculationInfo } from "@/components/calc/CalculationInfo";
import { CalculationResults } from "@/components/calc/CalculationResults";
import { CompanyTab } from "@/components/project/CompanyTab";
import { OfferTab } from "@/components/project/OfferTab";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Title } from "@/components/ui/title";
import { useProfile } from "@/hooks/api/useProfile";
import { useProject } from "@/hooks/api/useProject";
import { useAuthGuard } from "@/hooks/useAuthGuard";
import { useTabState } from "@/hooks/useTabState";
import { formatFullName } from "@/lib/utils";
import Link from "next/link";
import { useMemo } from "react";

interface ProjectProps {
  params: {
    id: string;
  };
}

const tabs = {
  offers: "Ponude izvođača",
  comp: "Izvođači",
  calc: "Izračun",
};

const smTabs = {
  offers: "Ponude",
  comp: "Izvođači",
  calc: "Izračun",
};

type Tab = keyof typeof tabs;

const Project = ({ params }: ProjectProps) => {
  useAuthGuard();

  const usr = useProfile();
  const [tab, setTab] = useTabState<Tab>("offers");

  const { data, isLoading } = useProject(params.id);

  const hasConfirmedProject = useMemo(() => {
    return data?.offers.some((offer) => offer.state === "CHOSEN");
  }, [data]);

  if (isLoading) {
    return <LoadingScreen />;
  }

  return (
    <Layout>
      <Tabs onValueChange={(t) => setTab(t as Tab)} value={tab}>
        <div className={`flex flex-col gap-4`}>
          <div
            className={`flex grow flex-col justify-between gap-y-4 lg:flex-row lg:items-center`}
          >
            <Title>
              <Link href="/app/dashboard">
                <span className={`text-black/60`}>
                  {formatFullName(
                    usr?.data?.user.userProfile.firstName,
                    usr?.data?.user.userProfile.lastName,
                  ) || "Korisnik"}
                </span>{" "}
              </Link>
              / {data?.project.name}
            </Title>

            <TabsList className="grid w-full grid-cols-3 gap-3 lg:max-w-[430px]">
              {Object.entries(tabs).map(([key, value]) => (
                <TabsTrigger key={key} value={key as Tab}>
                  <span className={`hidden sm:block`}>{value}</span>
                  <span className={`sm:hidden`}>{smTabs[key as Tab]}</span>
                </TabsTrigger>
              ))}
            </TabsList>
          </div>
          <TabsContent className={`mb-32`} value="offers">
            <OfferTab projectId={params.id} setTab={setTab} />
          </TabsContent>
          <TabsContent value="comp" className={``}>
            <CompanyTab
              hasConfirmedProject={hasConfirmedProject}
              projectId={params.id}
              project={data}
            />
          </TabsContent>
          <TabsContent value="calc">
            <CalculationInfo calculation={data?.project.calculation} />
            <Title className={`pb-2 pt-6`}>Rezultati izračuna</Title>
            <div className={`rounded-md border border-border p-4`}>
              <CalculationResults
                onlyResults
                calculation={data?.project?.calculation}
              />
            </div>
            <div className={`h-80`}></div>
          </TabsContent>
        </div>
      </Tabs>
    </Layout>
  );
};

export default Project;
