import { ExternalLink } from "@/components/ExternalLink";
import { Rating, Stat, StatText } from "@/components/project/Stat";
import { Button } from "@/components/ui/button";
import { Title } from "@/components/ui/title";
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from "@/components/ui/tooltip";
import { useSendOffer } from "@/hooks/api/offer/useSendOffer";
import { useCompanies } from "@/hooks/api/useCompanies";
import { useLoggedIn } from "@/hooks/api/useLoggedin";
import { cn } from "@/lib/utils";
import { queryClient } from "@/providers";
import { OfferWithCompany, ProjectResponse } from "@/types/api";
import {
  AtSign,
  IconoirProvider,
  MapPin,
  Phone,
  Send,
  StarSolid,
} from "iconoir-react";
import Link from "next/link";
import { toast } from "sonner";

const hasOfferForCompany = (companyId: number, offers?: OfferWithCompany[]) => {
  return offers?.some((offer) => offer.company.id === companyId);
};

interface CompanyTabProps {
  projectId: string;
  project?: ProjectResponse;
  hasConfirmedProject?: boolean;
}

export const CompanyTab = ({
  projectId,
  project,
  hasConfirmedProject,
}: CompanyTabProps) => {
  const sendOfferMutation = useSendOffer(projectId);
  const companies = useCompanies();

  const user = useLoggedIn();

  return (
    <div className={`mb-32 flex flex-col gap-4`}>
      <Title className={``}>Dostupni izvođači</Title>
      {companies?.data?.companies.map((company) => {
        const isSent = hasOfferForCompany(company.id, project?.offers);
        return (
          <div
            key={company.id}
            className={cn(
              `flex flex-col rounded-sm border border-border p-4`,
              {},
            )}
          >
            <div
              className={`flex flex-col justify-between gap-2 md:flex-row md:gap-0`}
            >
              <div>
                <div
                  className={`flex items-center justify-between gap-5 sm:justify-normal`}
                >
                  <ExternalLink href={company.url}>
                    <div className={`text-lg font-semibold`}>
                      {company.name}
                    </div>
                  </ExternalLink>
                  {company.reviewAverage === 0 ? (
                    <div className={`font-medium`}>0 recenzija</div>
                  ) : (
                    <Rating rating={company.reviewAverage} />
                  )}
                </div>
                <IconoirProvider
                  iconProps={{
                    width: 16,
                    height: 16,
                    strokeWidth: 1.8,
                  }}
                >
                  <div
                    className={`flex flex-col text-black/60 sm:flex-row sm:gap-2`}
                  >
                    <Link href={`mailto:${company.email}`}>
                      <Stat>
                        <AtSign className={`mt-[3px]`} />
                        <StatText className={`font-normal`}>
                          {company.email}
                        </StatText>
                      </Stat>
                    </Link>
                    <Link href={`tel:${company.mobile}`}>
                      <Stat>
                        <Phone className={`mt-[1px]`} />
                        <StatText className={`font-normal`}>
                          {company.mobile}
                        </StatText>
                      </Stat>
                    </Link>
                    <Stat>
                      <MapPin strokeWidth={2} className={`mt-[1px]`} />
                      <StatText className={`font-normal`}>
                        {company.location}
                      </StatText>
                    </Stat>
                  </div>
                </IconoirProvider>
              </div>
              {isSent || hasConfirmedProject ? (
                <DisabledCompanySendButton
                  reason={
                    hasConfirmedProject && !isSent
                      ? `Jedna ponuda je odabrana`
                      : `Ponuda zatražena`
                  }
                  explanation={
                    hasConfirmedProject && !isSent
                      ? `Izvođač je već odabran za ovaj projekt.`
                      : `Ponuda je već poslana ovom izvođaču.`
                  }
                />
              ) : (
                <Button
                  disabled={sendOfferMutation.isPending}
                  onClick={async () => {
                    await sendOfferMutation.mutateAsync(company.id);
                    toast.success(`Zatražena ponuda od ${company.name}`);
                    queryClient.invalidateQueries({
                      queryKey: ["project", projectId],
                    });
                  }}
                  className={`flex gap-2`}
                >
                  Zatraži ponudu
                  <Send strokeWidth={1.6} />
                </Button>
              )}
            </div>
            {company.reviews.length === 0 ? (
              <div className={`pb-1 pt-3 text-black/60`}>
                Još nema recenzija za ovu kompaniju.
              </div>
            ) : (
              <>
                <span className={`pb-2 pt-2 font-medium text-black/60 md:pt-6`}>
                  Recenzije
                </span>
                <div className={`flex max-h-[400px] flex-col gap-2`}>
                  {company.reviews.map((review) => {
                    let isMyReview = false;
                    if (user.data) {
                      isMyReview = review.owner.id === user.data.id;
                    }
                    return (
                      <div
                        key={review.id}
                        className={`flex flex-col gap-1 ${isMyReview ? "text-accent-1" : ""}`}
                      >
                        <div className={`flex grow gap-4`}>
                          <div className={`font-medium`}>
                            {isMyReview
                              ? "Moja recenzija"
                              : "Anoniman korisnik"}
                          </div>
                          <div className={`flex items-center gap-[px]`}>
                            <StarSolid height={14} />
                            <div className={`text-sm font-medium`}>
                              {review.rating}/5
                            </div>
                          </div>
                        </div>
                        <div className={`text-foreground`}>{review.text}</div>
                      </div>
                    );
                  })}
                </div>
              </>
            )}
          </div>
        );
      })}
    </div>
  );
};

interface DisabledCompanySendButtonProps {
  reason?: string;
  explanation?: string;
}

export const DisabledCompanySendButton = ({
  explanation,
  reason,
}: DisabledCompanySendButtonProps) => {
  return (
    <TooltipProvider>
      <Tooltip>
        <TooltipTrigger className={`h-min`}>
          <Button disabled variant={"secondary"} className={`w-full md:w-auto`}>
            <p>{reason}</p>
          </Button>
        </TooltipTrigger>
        <TooltipContent>
          <p>{explanation}</p>
        </TooltipContent>
      </Tooltip>
    </TooltipProvider>
  );
};
