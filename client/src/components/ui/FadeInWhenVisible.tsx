"use client";

import { motion } from "framer-motion";
import { NextPage } from "next";

type Props = {
  children: React.ReactNode | React.ReactNode[];
  duration: number;
  y: number;
  x: number;
};
const FadeInWhenVisible: NextPage<Props> = ({ children, duration, y, x }) => {
  let boxVariants = {};
  if (typeof window !== "undefined") {
    const isMobile = window.innerWidth < 768;
    if (!isMobile) {
      boxVariants = {
        visible: { opacity: 1, y: 0, x: 0, scale: 1 },
        hidden: { opacity: 0, y: y, x: x, scale: 1 },
      };
    }
  }

  return (
    <motion.div
      initial="hidden"
      whileInView="visible"
      viewport={{ once: true }}
      transition={{ duration: duration }}
      variants={boxVariants}
    >
      {children}
    </motion.div>
  );
};
export default FadeInWhenVisible;
