/* eslint-disable @next/next/no-img-element */
import { Button } from "@/components/ui/button";
import { useMapImg } from "@/hooks/useMapImg";
import { Project } from "@/types/api";
import { PlusSquare } from "iconoir-react";
import Link from "next/link";
import { useRouter } from "next/navigation";

interface ProjectCardProps {
  name: string;
  link: string;
  project: Project;
}

export const ProjectCard = ({ name, link, project }: ProjectCardProps) => {
  const img = useMapImg(project?.calculation.lat, project?.calculation.lng);
  return (
    <Link href={link}>
      <div
        className={`group relative flex aspect-video w-full cursor-pointer overflow-hidden rounded-md border p-4 transition-all hover:bg-background hover:shadow-md`}
      >
        {/* gradient from top bg to btm transparent */}
        <div
          className={`absolute bottom-0 left-0 right-0 z-50 h-full w-full bg-gradient-to-t from-transparent to-background `}
        />

        <img
          src={img}
          alt={name}
          className={`absolute bottom-0 left-0 right-0 top-0 z-40 h-full w-full scale-[200%] rounded-md object-cover`}
        />
        <div className={`gap- z-[60] flex flex-col`}>
          <span className={`text-xl font-medium`}>{name}</span>
          <span className={`text-sm`}>{project.calculation.location}</span>
        </div>
      </div>
    </Link>
  );
};

interface ProjectsProps {
  projects?: Project[];
}

export const Projects = ({ projects }: ProjectsProps) => {
  const router = useRouter();
  return (
    <div className={`flex flex-col gap-3`}>
      <div className={`flex grow justify-between`}>
        <h1 className={`text-2xl font-semibold`}>Moji projekti</h1>
        <Button
          className={`gap-2`}
          onClick={() => {
            router.push("/app/calculate");
          }}
        >
          <PlusSquare width={20} height={20} strokeWidth={2} />
          Novi projekt
        </Button>
      </div>
      <div className={`grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3`}>
        {projects?.map((project) => (
          <ProjectCard
            key={project.id}
            project={project}
            name={project.name}
            link={`/app/project/${project.id}`}
          />
        ))}
      </div>
    </div>
  );
};
