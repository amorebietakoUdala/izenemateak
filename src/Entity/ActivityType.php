<?php

namespace App\Entity;

use App\Repository\ActivityTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivityTypeRepository::class)]
class ActivityType implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Activity::class, mappedBy: 'activityType')]
    private Collection|array $activitys;

    public function __construct()
    {
        $this->activitys = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $this->activitys[] = $activity;
            $activity->setActivityType($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activitys->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getActivityType() === $this) {
                $activity->setActivityType(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->name;
    }

    public function fill(ActivityType $data): self
    {
        $this->id = $data->getId();
        $this->name = $data->getName();
        return $this;
    }

}
