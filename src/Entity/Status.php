<?php

namespace App\Entity;

use App\Repository\StatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StatusRepository::class)
 */
class Status
{

    const PREINSCRIPTION = 0;
    const RAFFLED = 1;
    const CLOSED = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $descriptionEs;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $descriptionEu;

    /**
     * @ORM\OneToMany(targetEntity=Course::class, mappedBy="status")
     */
    private $courses;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $statusNumber;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): self
    {
        if (!$this->courses->contains($course)) {
            $this->courses[] = $course;
            $course->setStatus($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): self
    {
        if ($this->courses->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getStatus() === $this) {
                $course->setStatus(null);
            }
        }

        return $this;
    }

    public function getStatusNumber(): ?int
    {
        return $this->statusNumber;
    }

    public function setStatusNumber(?int $statusNumber): self
    {
        $this->statusNumber = $statusNumber;

        return $this;
    }

}
