import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog";

interface ProjectConfirmChooseProps {
  onConfirm: () => void;
  open: boolean;
  onOpenChange: (open: boolean) => void;
}

export const ProjectConfirmChoose = ({
  onOpenChange,
  onConfirm,
  open,
}: ProjectConfirmChooseProps) => {
  return (
    <AlertDialog open={open} onOpenChange={onOpenChange}>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Odaberite ovu ponudu?</AlertDialogTitle>
          <AlertDialogDescription>
            Odabirom ove ponude, automatski ćete odbiti sve ostale ponude, te će
            se projekt označiti kao odabran, čak i u slučaju da pristignu nove
            ponude.
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel>Odustani</AlertDialogCancel>
          <AlertDialogAction onClick={onConfirm}>Odaberi</AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
};
