<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=CourseRepository::class)
 */
class Course
{

    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameEs;

        /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameEu;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $turnEs;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $turnEu;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $startDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endDate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $active;

    /**
     * @ORM\OneToMany(targetEntity=Registration::class, mappedBy="course")
     */
    private $registrations;

    /**
     * @ORM\ManyToOne(targetEntity=Activity::class, inversedBy="courses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activity;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $places;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $limitPlaces;

    /**
     * @ORM\ManyToOne(targetEntity=Status::class, inversedBy="courses")
     */
    private $status;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of nameEs
     */ 
    public function getNameEs()
    {
        return $this->nameEs;
    }

    /**
     * Set the value of nameEs
     *
     * @return  self
     */ 
    public function setNameEs($nameEs)
    {
        $this->nameEs = $nameEs;

        return $this;
    }

    /**
     * Get the value of nameEu
     */ 
    public function getNameEu()
    {
        return $this->nameEu;
    }

    /**
     * Set the value of nameEu
     *
     * @return  self
     */ 
    public function setNameEu($nameEu)
    {
        $this->nameEu = $nameEu;

        return $this;
    }

    public function getTurnEs(): ?string
    {
        return $this->turnEs;
    }

    public function setTurnEs(string $turnEs): self
    {
        $this->turnEs = $turnEs;

        return $this;
    }

    public function getTurnEu(): ?string
    {
        return $this->turnEu;
    }

    public function setTurnEu(string $turnEu): self
    {
        $this->turnEu = $turnEu;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return Collection|Registration[]
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function addRegistration(Registration $registration): self
    {
        if (!$this->registrations->contains($registration)) {
            $this->registrations[] = $registration;
            $registration->setCourse($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->removeElement($registration)) {
            // set the owning side to null (unless already changed)
            if ($registration->getCourse() === $this) {
                $registration->setCourse(null);
            }
        }

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

        /**
     * Set the value of registrations
     *
     * @return  self
     */ 
    public function setRegistrations(Collection $registrations)
    {
        $this->registrations = $registrations;

        return $this;
    }

    public function clone() {
        $course = new Course();
        $course->setNameEs($this->nameEs.'_copia');
        $course->setNameEu($this->nameEu.'_kopia');
        $course->setStartDate($this->startDate);
        $course->setEndDate($this->endDate);
        $course->setActive($this->active);
        $course->setRegistrations($this->registrations);
        $course->setActivity($this->activity);
        return $course;
    }

    public function canRegister(\DateTime $date) {
        if ($this->startDate > $date || $this->endDate < $date ) {
            return false;
        }
        return true;
    }

    public function getPlaces(): ?int
    {
        return $this->places;
    }

    public function setPlaces(?int $places): self
    {
        $this->places = $places;

        return $this;
    }

    public function getLimitPlaces(): ?bool
    {
        return $this->limitPlaces;
    }

    public function setLimitPlaces(?bool $limitPlaces): self
    {
        $this->limitPlaces = $limitPlaces;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }
}
