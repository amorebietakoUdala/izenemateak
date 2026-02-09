<?php

namespace App\Entity;

use App\Repository\ClasificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClasificationRepository::class)]
class Clasification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $descriptionEs = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $descriptionEu = null;

    #[ORM\OneToMany(targetEntity: Activity::class, mappedBy: 'clasification')]
    private Collection $activitys;

    public function __construct()
    {
        $this->activitys = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getDescriptionEs(): ?string
    {
        return $this->descriptionEs;
    }

    public function setDescriptionEs(string $descriptionEs): self
    {
        $this->descriptionEs = $descriptionEs;

        return $this;
    }

    public function getDescriptionEu(): ?string
    {
        return $this->descriptionEu;
    }

    public function setDescriptionEu(string $descriptionEu): self
    {
        $this->descriptionEu = $descriptionEu;

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivitys(): Collection
    {
        return $this->activitys;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activitys->contains($activity)) {
            $this->activitys->add($activity);
            $activity->setClasification($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activitys->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getClasification() === $this) {
                $activity->setClasification(null);
            }
        }

        return $this;
    }

    public function fill(Clasification $data): self
    {
        $this->id = $data->getId();
        $this->descriptionEs = $data->getDescriptionEs();
        $this->descriptionEu = $data->getDescriptionEu();
        return $this;
    }

}
