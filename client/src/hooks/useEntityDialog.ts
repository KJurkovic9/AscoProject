import { useRef, useState } from "react";

export const useEntityDialog = <T>() => {
  const idRef = useRef<T | null>(null);

  const [isOpen, setIsOpen] = useState(false);

  const openDialog = (id: T) => {
    idRef.current = id;
    setIsOpen(true);
  };

  return [isOpen, openDialog, setIsOpen, idRef] as const;
};
