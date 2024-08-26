"use client";
import { ExternalLink } from "@/components/ExternalLink";
import { ProjectConfirmChoose } from "@/components/project/ProjectConfirmChoose";
import { Rating, Stat, StatText } from "@/components/project/Stat";
import { statusIcon } from "@/components/project/StatusIcon";
import { ReviewDialog } from "@/components/review/ReviewDialog";
import { Button } from "@/components/ui/button";
import { Checkbox } from "@/components/ui/checkbox";
import { Label } from "@/components/ui/label";
import { Title } from "@/components/ui/title";
import { useSelectOffer } from "@/hooks/api/form/useSelectOffer";
import { useCheckReview } from "@/hooks/api/review/useCheckReview";
import { useProject } from "@/hooks/api/useProject";
import { useEntityDialog } from "@/hooks/useEntityDialog";
import { useFilterOffers } from "@/hooks/useFillterOffers";
import { Status, status, statusBorderColor, statusColor } from "@/lib/const";
import { queryKeys } from "@/lib/queryKeys";
import { cn, currencyFormat, formatDate } from "@/lib/utils";
import { queryClient } from "@/providers";
import { Review, isOfferFulfilled } from "@/types/api";
import { AtSign, IconoirProvider, Phone, StarSolid } from "iconoir-react";
import Link from "next/link";
import { useMemo, useState } from "react";
import { toast } from "sonner";

interface OfferTabProps {
  children?: React.ReactNode | React.ReactNode[];
  projectId: string;

  // context :(
  setTab: (tab: any) => void;
}

export const OfferTab = ({ children, projectId, setTab }: OfferTabProps) => {
  const { data, isLoading } = useProject(projectId);

  const hasConfirmedProject = useMemo(() => {
    return data?.offers.some((offer) => offer.state === "CHOSEN");
  }, [data]);

  const confirmProject = useSelectOffer();

  const [
    isConfirmDialogOpen,
    openConfirmDialog,
    onConfirmDialogChange,
    confirmOfferId,
  ] = useEntityDialog<string>();

  const isConfirmDisabled = hasConfirmedProject || confirmProject.isPending;

  const [
    isRatingDialogOpen,
    openRatingDialog,
    onRatingDialogChange,
    companyId,
  ] = useEntityDialog<string>();

  // edit dialog
  const [isEditDialogOpen, openEditDialog, onEditDialogChange, review] =
    useEntityDialog<Review>();

  const onConfirm = async (offerId: string) => {
    await confirmProject.mutateAsync(offerId);
    toast.success("Projekt potvrđen, obavijest poslana izvođaču.");
    queryClient.invalidateQueries({
      queryKey: queryKeys.project.id(projectId),
    });
  };

  const [onlyFulfilled, setOnlyFulfilled] = useState<boolean>(false);

  const filteredOffers = useFilterOffers({
    projectResponse: data,
    onlyFulfilled,
  });

  const chosenOffer = data?.offers.find((offer) => offer.state === "CHOSEN");
  const checkForReview = useCheckReview(chosenOffer?.company.id, !!chosenOffer);
  return (
    <>
      <ProjectConfirmChoose
        open={isConfirmDialogOpen}
        onOpenChange={onConfirmDialogChange}
        onConfirm={async () => {
          confirmOfferId.current && (await onConfirm(confirmOfferId.current));
        }}
      />
      <ReviewDialog
        projectId={projectId}
        companyId={companyId.current!}
        isOpen={isRatingDialogOpen || isEditDialogOpen}
        setIsOpen={(v) => {
          onRatingDialogChange(v);
          onEditDialogChange(v);
        }}
        review={review.current || undefined}
      />
      <div className={`flex items-center justify-between pb-4`}>
        <Title className={``}>Ponude</Title>
        <div className={`flex items-center gap-1`}>
          <Checkbox
            id="onlyFulfilled"
            checked={onlyFulfilled}
            onCheckedChange={(c) => {
              setOnlyFulfilled(c === true);
            }}
          />
          <Label htmlFor="onlyFulfilled">Samo odgovorene</Label>
        </div>
      </div>
      <div className={`flex flex-col gap-4`}>
        {data?.offers.length === 0 && (
          <div
            className={`my-44 flex w-full items-center justify-center gap-0.5 text-center`}
          >
            Nema ponuda za ovaj projekt.{"  "}
            <button
              className={`underline duration-200 hover:text-accent-1`}
              onClick={() => setTab("comp")}
            >
              Pošalji ponudu
            </button>
          </div>
        )}
        {filteredOffers.map((offer) => {
          return (
            <div
              className={cn(
                `flex rounded-md border border-border p-4 ${statusBorderColor[offer.state]}`,
                {},
              )}
              key={offer.id}
            >
              <div className={`flex w-full flex-col gap-3`}>
                <div
                  className={`flex grow flex-col justify-between sm:flex-row`}
                >
                  <IconoirProvider
                    iconProps={{
                      width: 16,
                      height: 16,
                      strokeWidth: 1.8,
                    }}
                  >
                    <div>
                      <div
                        className={`flex justify-between gap-5 sm:justify-normal`}
                      >
                        <ExternalLink href={offer.company.url}>
                          <div className={`text-lg font-semibold`}>
                            {offer.company.name}
                          </div>
                        </ExternalLink>
                        {offer.company.reviewAverage === 0 ? (
                          <div className={`font-medium`}>0 recenzija</div>
                        ) : (
                          <Rating rating={offer.company.reviewAverage} />
                        )}
                      </div>
                      <div
                        className={`flex flex-col text-black/60 sm:flex-row sm:gap-2`}
                      >
                        <Link href={`mailto:${offer.company.email}`}>
                          <Stat>
                            <AtSign className={`mt-[3px]`} />
                            <StatText className={`font-normal`}>
                              {offer.company.email}
                            </StatText>
                          </Stat>
                        </Link>
                        <Link href={`tel:${offer.company.mobile}`}>
                          <Stat>
                            <Phone className={`mt-[1px]`} />
                            <StatText className={`font-normal`}>
                              {offer.company.mobile}
                            </StatText>
                          </Stat>
                        </Link>
                      </div>
                    </div>
                  </IconoirProvider>
                  <div
                    className={`mt-3 flex flex-col items-start sm:mt-0 sm:items-end`}
                  >
                    {isOfferFulfilled(offer) && (
                      <>
                        <div className={`text-3xl font-medium`}>
                          {currencyFormat(offer.price)}
                        </div>
                      </>
                    )}
                    <Stat className={`${statusColor[offer.state]}`}>
                      <StatText>{status[offer?.state as Status]}</StatText>
                      <IconoirProvider
                        iconProps={{
                          width: 20,
                          height: 20,
                          strokeWidth: 1.8,
                        }}
                      >
                        {statusIcon(offer?.state as Status)}
                      </IconoirProvider>
                    </Stat>
                  </div>
                </div>
                {isOfferFulfilled(offer) && offer.state !== "DECLINED" && (
                  <div className={`flex flex-col gap-y-3 md:flex-row`}>
                    <div className={`grow`}>
                      <div className={`pb-1 text-lg font-medium`}>
                        Detalji ponude
                      </div>
                      <div className={`md:max-w-[77%]`}>
                        {/* replace \n with real new lines */}
                        {offer.description
                          ?.split("\\n")
                          .map((line, i) => <div key={i}>{line}</div>)}
                      </div>

                      <div className={`pt-2 text-sm text-black/60`}>
                        Ugradnja dostupna od: {formatDate(offer.offerDate)}
                      </div>
                    </div>
                    {offer.state === "CHOSEN" ? (
                      <>
                        {checkForReview.data?.review ? (
                          <Button
                            className={`mt-auto gap-1`}
                            onClick={() => {
                              if (!checkForReview.data.review) return;
                              companyId.current = offer.company.id.toString();
                              openEditDialog(checkForReview.data.review);
                            }}
                          >
                            Uredi recenziju
                          </Button>
                        ) : (
                          <Button
                            disabled={
                              checkForReview.isFetching ||
                              checkForReview.data?.review === null
                            }
                            className={`mt-auto gap-1`}
                            onClick={() => {
                              review.current = null;
                              openRatingDialog(offer.company.id.toString());
                            }}
                          >
                            <StarSolid height={16} strokeWidth={1} />
                            Recenziraj
                          </Button>
                        )}
                      </>
                    ) : isConfirmDisabled ? (
                      <></>
                    ) : (
                      <Button
                        disabled={isConfirmDisabled}
                        className={`mt-auto`}
                        onClick={() => {
                          openConfirmDialog(offer.id.toString());
                        }}
                      >
                        Odaberi ponudu
                      </Button>
                    )}
                  </div>
                )}
              </div>
            </div>
          );
        })}
      </div>
    </>
  );
};
