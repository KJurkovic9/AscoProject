<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SectionRepository;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: SectionRepository::class)]
class Section
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["section"])]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Groups(["section"])]
    private string $title;

    #[ORM\Column(length: 1024)]
    #[Groups(["section"])]
    private string $text;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["section"])]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'sections')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["section_guide"])]
    private Guide $guide;

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getGuide(): Guide
    {
        return $this->guide;
    }

    public function setGuide(Guide $guide): static
    {
        $this->guide = $guide;

        return $this;
    }
}
