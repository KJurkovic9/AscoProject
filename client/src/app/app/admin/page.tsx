"use client";

import { NextPage } from "next";
import { useProfile } from "@/hooks/api/useProfile";
import { useAuthGuard } from "@/hooks/useAuthGuard";
import { Loading } from "@/components/Loading";
import { useRouter } from "next/navigation";
import { Layout } from "@/components/Layout";
import LargeHeading from "@/components/ui/LargeHeading";
import Paragraph from "@/components/ui/Paragraph";

type Props = {};
const Page: NextPage<Props> = ({}) => {
  useAuthGuard();

  const router = useRouter();
  const profile = useProfile();
  console.log(profile.data?.user.role);
  if (profile.isLoading) {
    return (
      <div className={`flex h-full w-full items-center justify-center`}>
        <Loading />
      </div>
    );
  }

  const adminCheck = profile?.data?.user?.role;

  if (adminCheck !== "ROLE_ADMIN") {
    router.replace("/");
  }

  return (
    <Layout>
      <div className="flex h-full w-full flex-col items-center space-y-5">
        <LargeHeading className="lg:text-center">Admin Page</LargeHeading>
        <Paragraph className="lg:text-center">
          Ovdje dodajemo guidove i radimo ostale admin stvari
        </Paragraph>
      </div>
    </Layout>
  );
};
export default Page;
