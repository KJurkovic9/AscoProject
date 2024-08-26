"use client";

import { NewProjectDialog } from "@/components/CreateProjectDialog";
import { Layout } from "@/components/Layout";
import { LoadingScreen } from "@/components/Loading";
import { Projects } from "@/components/Projects";
import { useProfile } from "@/hooks/api/useProfile";
import { useProjects } from "@/hooks/api/useProjects";
import { useAuthGuard } from "@/hooks/useAuthGuard";
import { formatDate } from "@/lib/utils";
import { useRouter } from "next/navigation";

interface DashboardProps {}

const Dashboard = ({}: DashboardProps) => {
  useAuthGuard();

  const router = useRouter();
  const profile = useProfile();
  const projects = useProjects();

  if (profile.isLoading) {
    return <LoadingScreen />;
  }
  const profile1 = profile?.data?.user?.userProfile;

  if (!profile1) {
    router.replace("/profile");
  }

  return (
    <Layout>
      <div className={`mb-40 flex flex-col gap-12 md:gap-16`}>
        <NewProjectDialog />
        <div className={`flex justify-between`}>
          <div className={`flex flex-col`}>
            <span className={`text-2xl font-semibold leading-10`}>
              {profile1?.firstName} {profile1?.lastName}
            </span>
            <span>
              Broj projekata: {profile.data?.projectCount}, korisnik od{" "}
              {formatDate(profile.data?.user.timeCreated)}
            </span>
          </div>
        </div>
        <Projects projects={projects.data?.projects} />
      </div>
    </Layout>
  );
};

export default Dashboard;
