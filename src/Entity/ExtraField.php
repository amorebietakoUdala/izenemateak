<?php

namespace App\Entity;

use App\Repository\ExtraFieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ExtraFieldRepository::class)
 */
class ExtraField
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"api"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"api"})
     */
    private $nameEu;

    /**
     * @ORM\OneToMany(targetEntity=RegistrationExtraField::class, mappedBy="extraField")
     */
    private $registrationExtraFields;

    /**
     * @ORM\ManyToMany(targetEntity=Activity::class, mappedBy="extraFields")
     */
    private $activities;

    public function __construct()
    {
        $this->registrationExtraFields = new ArrayCollection();
        $this->activities = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNameEu(): ?string
    {
        return $this->nameEu;
    }

    public function setNameEu(string $nameEu): self
    {
        $this->nameEu = $nameEu;

        return $this;
    }

    /**
     * @return Collection<int, RegistrationExtraField>
     */
    public function getRegistrationExtraFields(): Collection
    {
        return $this->registrationExtraFields;
    }

    public function addRegistrationExtraField(RegistrationExtraField $registrationExtraField): self
    {
        if (!$this->registrationExtraFields->contains($registrationExtraField)) {
            $this->registrationExtraFields[] = $registrationExtraField;
            $registrationExtraField->setExtraField($this);
        }

        return $this;
    }

    public function removeRegistrationExtraField(RegistrationExtraField $registrationExtraField): self
    {
        if ($this->registrationExtraFields->removeElement($registrationExtraField)) {
            // set the owning side to null (unless already changed)
            if ($registrationExtraField->getExtraField() === $this) {
                $registrationExtraField->setExtraField(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities[] = $activity;
            $activity->addExtraField($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            $activity->removeExtraField($this);
        }

        return $this;
    }
}
