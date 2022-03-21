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

    const STATUS_PREINSCRIPTION = 0;
    const STATUS_RAFFLED = 1;
    const STATUS_WAITING_CONFIRMATIONS = 2;
    const STATUS_WAITING_LIST = 3;
    const STATUS_CLOSED = 4;

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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $cost;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $deposit;

    /**
     * @ORM\ManyToOne(targetEntity=Clasification::class, inversedBy="courses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $clasification;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->limitPlaces = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

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

    public function setRegistrations(Collection $registrations)
    {
        $this->registrations = $registrations;

        return $this;
    }

    public function clone() {
        $course = new Course();
        $course->setNameEs($this->nameEs.'_copia');
        $course->setNameEu($this->nameEu.'_kopia');
        $course->setTurnEs($this->turnEs.'_copia');
        $course->setTurnEu($this->turnEu.'_kopia');
        $course->setPlaces($this->places);
        $course->setStartDate($this->startDate);
        $course->setEndDate($this->endDate);
        $course->setActive($this->active);
        $course->setRegistrations($this->registrations);
        $course->setActivity($this->activity);
        $course->setDeposit($this->deposit);
        $course->setCost($this->cost);
        $course->setClasification($this->clasification);
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

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(?float $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getDeposit(): ?float
    {
        return $this->deposit;
    }

    public function setDeposit(?float $deposit): self
    {
        $this->deposit = $deposit;

        return $this;
    }

    public function getClasification(): ?Clasification
    {
        return $this->clasification;
    }

    public function setClasification(?Clasification $clasification): self
    {
        $this->clasification = $clasification;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    public function countConfirmed(): int {
        $confirmed = array_reduce($this->getRegistrations()->toArray(),function($carry, Registration $item){
            if ( $item->getConfirmed()) {
                $carry += 1;
            } else {
                $carry += 0;
            }
            return $carry;
        });
        return $confirmed;
    }

    public function countRejected(): int {
        $confirmed = array_reduce($this->getRegistrations()->toArray(),function($carry, Registration $item){
            if ($item->getConfirmed() !== null && !$item->getConfirmed()) {
                $carry += 1;
            } else {
                $carry += 0;
            }
            return $carry;
        });
        return $confirmed;
    }

    public function isFull() {
        return $this->countConfirmed() >= $this->places ? true : false; 
    }

}
