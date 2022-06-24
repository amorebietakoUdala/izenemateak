<?php

namespace App\Entity;

use App\Repository\RegistrationExtraFieldRepository;
use App\Entity\ExtraField;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RegistrationExtraFieldRepository::class)
 */
class RegistrationExtraField
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Registration::class, inversedBy="registrationExtraFields")
     */
    private $registration;

    /**
     * @ORM\ManyToOne(targetEntity=ExtraField::class, inversedBy="registrationExtraFields")
     */
    private $extraField;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRegistration(): ?Registration
    {
        return $this->registration;
    }

    public function setRegistration(?Registration $registration): self
    {
        $this->registration = $registration;

        return $this;
    }

    public function getExtraField(): ?ExtraField
    {
        return $this->extraField;
    }

    public function setExtraField(?ExtraField $extraField): self
    {
        $this->extraField = $extraField;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }
}
