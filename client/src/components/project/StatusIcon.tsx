import { Status } from "@/lib/const";
import { CheckCircle, MailIn, SendDiagonal, XmarkCircle } from "iconoir-react";

export const statusIcon = (status: Status) => {
  switch (status) {
    case "SENT":
      return <SendDiagonal />;
    case "DECLINED":
      return <XmarkCircle />;
    case "REJECTED":
      return <XmarkCircle />;
    case "DONE":
      return <MailIn className={`ml-1`} />;
    case "ACCEPTED":
      return <MailIn className={`ml-1`} />;
    case "CHOSEN":
      return <CheckCircle />;
    default:
      return <SendDiagonal />;
  }
};
