<?php

namespace App\Entity;

use App\Repository\ClasificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClasificationRepository::class)
 */
class Clasification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descriptionEs;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $descriptionEu;

    /**
     * @ORM\OneToMany(targetEntity=Course::class, mappedBy="clasification")
     */
    private $courses;

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
            $course->setClasification($this);
        }

        return $this;
    }

    public function removeCourse(Course $course): self
    {
        if ($this->courses->removeElement($course)) {
            // set the owning side to null (unless already changed)
            if ($course->getClasification() === $this) {
                $course->setClasification(null);
            }
        }

        return $this;
    }
}
