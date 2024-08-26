import { animate, useInView, useMotionValue } from "framer-motion";
import { useEffect, useRef } from "react";

export default function Counter({
  value,
  direction = "up",
  currency = false,
}: {
  value: number;
  currency?: boolean;
  direction?: "up" | "down";
}) {
  const ref = useRef<HTMLSpanElement>(null);
  const motionValue = useMotionValue(direction === "down" ? value : 0);
  const isInView = useInView(ref, { once: true, margin: "-100px" });

  useEffect(() => {
    if (isInView) {
      motionValue.set(direction === "down" ? 0 : value);
    }
  }, [motionValue, isInView, direction, value]);

  useEffect(() => {
    const cc = Intl.NumberFormat("hr-HR", {
      style: "currency",
      currency: "EUR",
      maximumFractionDigits: 0,
      compactDisplay: "short",
    });

    const v = animate(motionValue, value, {
      duration: 1,

      onUpdate: (v) => {
        if (ref.current) {
          if (currency) {
            ref.current.textContent = cc.format(v);
            return;
          }
          ref.current.textContent = v.toFixed(0);
        }
      },
    });

    return () => {
      v.stop();
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [motionValue, value]);

  return <span ref={ref} />;
}
