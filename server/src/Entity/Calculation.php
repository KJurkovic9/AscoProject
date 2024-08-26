<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CalculationRepository;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CalculationRepository::class)]
class Calculation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["calculation"])]
    private int $id;

    #[ORM\Column]
    #[Groups(["calculation"])]
    private float $roofSurface;

    #[ORM\Column]
    #[Groups(["calculation"])]
    private int $roofPitch;

    #[ORM\Column(length: 2)]
    #[Groups(["calculation"])]
    private string $roofOrientation;

    #[ORM\Column]
    #[Groups(["calculation"])]
    private float $lat;

    #[ORM\Column]
    #[Groups(["calculation"])]
    private float $lng;

    #[ORM\Column]
    #[Groups(["calculation"])]
    private int $yearlyConsumption;

    #[ORM\Column(nullable: true)]
    #[Groups(["calculation"])]
    private int $lifespan;

    #[ORM\Column(nullable: true)]
    #[Groups(["calculation"])]
    private int $budget;

    #[ORM\Column]
    #[Groups(["calculation"])]
    private int $projectPrice;

    #[ORM\Column]
    #[Groups(["calculation"])]
    private int $profitabiltyYears;

    #[ORM\Column]
    #[Groups(["calculation"])]
    private float $effectiveness;

    #[ORM\Column(length: 255)]
    #[Groups(["calculation"])]
    private string $location;

    #[ORM\Column]
    #[Groups(["calculation"])]
    private array $profitabiltyMonthly = [];

    #[ORM\Column]
    #[Groups(["calculation"])]
    private float $paybackPeroid;

    #[ORM\Column]
    #[Groups(["calculation"])]
    private int $installationPrice;

    #[ORM\Column]
    #[Groups(["calculation"])]
    private int $equipmentPrice;

    #[ORM\Column]
    #[Groups(["calculation"])]
    private int $potentialPower;

    #[ORM\OneToOne(mappedBy: 'calculation', cascade: ['persist', 'remove'])]
    #[Groups(["calculation_project"])]
    private Project $project;

    public function getId(): int
    {
        return $this->id;
    }

    public function getRoofSurface(): float
    {
        return $this->roofSurface;
    }

    public function setRoofSurface(float $roofSurface): static
    {
        $this->roofSurface = $roofSurface;

        return $this;
    }

    public function getRoofPitch(): int
    {
        return $this->roofPitch;
    }

    public function setRoofPitch(int $roofPitch): static
    {
        $this->roofPitch = $roofPitch;

        return $this;
    }

    public function getRoofOrientation(): string
    {
        return $this->roofOrientation;
    }

    public function setRoofOrientation(string $roofOrientation): static
    {
        $this->roofOrientation = $roofOrientation;

        return $this;
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function setLat(float $lat): static
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): float
    {
        return $this->lng;
    }

    public function setLng(float $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getLifespan(): int
    {
        return $this->lifespan;
    }

    public function setLifespan(int $lifespan): static
    {
        $this->lifespan = $lifespan;

        return $this;
    }

    public function getBudget(): int
    {
        return $this->budget;
    }

    public function setBudget(int $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getProjectPrice(): int
    {
        return $this->projectPrice;
    }

    public function setProjectPrice(int $projectPrice): static
    {
        $this->projectPrice = $projectPrice;

        return $this;
    }

    public function getProfitabiltyYears(): int
    {
        return $this->profitabiltyYears;
    }

    public function setProfitabiltyYears(int $profitabiltyYears): static
    {
        $this->profitabiltyYears = $profitabiltyYears;

        return $this;
    }

    public function getEffectiveness(): float
    {
        return $this->effectiveness;
    }

    public function setEffectiveness(float $effectiveness): static
    {
        $this->effectiveness = $effectiveness;

        return $this;
    }

    public function getYearlyConsumption(): int
    {
        return $this->yearlyConsumption;
    }

    public function setYearlyConsumption(int $yearlyConsumption): static
    {
        $this->yearlyConsumption = $yearlyConsumption;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getProfitabiltyMonthly(): array
    {
        return $this->profitabiltyMonthly;
    }

    public function setProfitabiltyMonthly(array $profitabiltyMonthly): static
    {
        $this->profitabiltyMonthly = $profitabiltyMonthly;

        return $this;
    }

    public function getPaybackPeroid(): float
    {
        return $this->paybackPeroid;
    }

    public function setPaybackPeroid(float $paybackPeroid): static
    {
        $this->paybackPeroid = $paybackPeroid;

        return $this;
    }

    public function getInstallationPrice(): int
    {
        return $this->installationPrice;
    }

    public function setInstallationPrice(int $installationPrice): static
    {
        $this->installationPrice = $installationPrice;

        return $this;
    }

    public function getEquipmentPrice(): int
    {
        return $this->equipmentPrice;
    }

    public function setEquipmentPrice(int $equipmentPrice): static
    {
        $this->equipmentPrice = $equipmentPrice;

        return $this;
    }

    public function getPotentialPower(): int
    {
        return $this->potentialPower;
    }

    public function setPotentialPower(int $potentialPower): static
    {
        $this->potentialPower = $potentialPower;

        return $this;
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): static
    {
        // set the owning side of the relation if necessary
        if ($project->getCalculation() !== $this) {
            $project->setCalculation($this);
        }

        $this->project = $project;

        return $this;
    }
}
