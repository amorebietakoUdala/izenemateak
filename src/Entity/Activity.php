<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=ActivityRepository::class)
 */
class Activity
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
     * @ORM\OneToMany(targetEntity=Registration::class, mappedBy="activity")
     */
    private $registrations;

    /**
     * @ORM\ManyToOne(targetEntity=ActivityType::class, inversedBy="activitys")
     * @ORM\JoinColumn(nullable=false)
     */
    private $activityType;

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
    private $costForSubscribers;

    /**
     * @ORM\ManyToOne(targetEntity=Clasification::class, inversedBy="activitys")
     * @ORM\JoinColumn(nullable=false)
     */
    private $clasification;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $accountingConcept;

    /**
     * @ORM\ManyToMany(targetEntity=ExtraField::class, inversedBy="activities", cascade={"persist"})
     */
    private $extraFields;

    /**
     * @ORM\Column(type="boolean")
     */
    private $domiciled;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $url;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->extraFields = new ArrayCollection();
        $this->limitPlaces = false;
        $this->domiciled = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameEs()
    {
        return $this->nameEs;
    }

    public function setNameEs($nameEs)
    {
        $this->nameEs = $nameEs;

        return $this;
    }

    public function getNameEu()
    {
        return $this->nameEu;
    }

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
            $registration->setActivity($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->removeElement($registration)) {
            // set the owning side to null (unless already changed)
            if ($registration->getActivity() === $this) {
                $registration->setActivity(null);
            }
        }

        return $this;
    }

    public function getActivityType(): ?ActivityType
    {
        return $this->activityType;
    }

    public function setActivityType(?ActivityType $activityType): self
    {
        $this->activityType = $activityType;

        return $this;
    }

    public function setRegistrations(Collection $registrations)
    {
        $this->registrations = $registrations;

        return $this;
    }

    public function clone() {
        $activity = new Activity();
        $activity->setNameEs($this->nameEs.'_copia');
        $activity->setNameEu($this->nameEu.'_kopia');
        $activity->setTurnEs($this->turnEs.'_copia');
        $activity->setTurnEu($this->turnEu.'_kopia');
        $activity->setPlaces($this->places);
        $activity->setStartDate($this->startDate);
        $activity->setEndDate($this->endDate);
        $activity->setActive($this->active);
        $activity->setActivityType($this->activityType);
        $activity->setLimitPlaces($this->limitPlaces);
        $activity->setCost($this->cost);
        $activity->setCostForSubscribers($this->costForSubscribers);
        $activity->setAccountingConcept($this->accountingConcept);
        $activity->setDomiciled($this->domiciled);
        $activity->setClasification($this->clasification);
        foreach($this->extraFields as $extraField) {
            $activity->addExtraField($extraField);
        }
        
        return $activity;
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

    public function getCostForSubscribers(): ?float
    {
        return $this->costForSubscribers;
    }

    public function setCostForSubscribers(?float $costForSubscribers): self
    {
        $this->costForSubscribers = $costForSubscribers;

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
        if ( $confirmed === null ) {
            return 0;
        }
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
        if ( $confirmed === null ) {
            return 0;
        }
        return $confirmed;
    }

    public function isFull() {
        return $this->countConfirmed() >= $this->places ? true : false; 
    }

    public function isFree() {
        if ($this->cost === null && $this->costForSubscribers === null) {
            return true;
        }
        if ($this->cost === 0 && $this->costForSubscribers === 0) {
            return true;
        }

        return false;
    }

    public function getAccountingConcept(): ?string
    {
        return $this->accountingConcept;
    }

    public function setAccountingConcept(string $accountingConcept): self
    {
        $this->accountingConcept = $accountingConcept;

        return $this;
    }

    /**
     * @return Collection<int, ExtraField>
     */
    public function getExtraFields(): Collection
    {
        return $this->extraFields;
    }

    public function addExtraField(ExtraField $extraField): self
    {
        if (!$this->extraFields->contains($extraField)) {
            $this->extraFields[] = $extraField;
        }

        return $this;
    }

    public function removeExtraField(ExtraField $extraField): self
    {
        $this->extraFields->removeElement($extraField);

        return $this;
    }

    public function canConfirm() {
        if ($this->isFull()) {
            return false;
        }
        if ($this->status === Activity::STATUS_WAITING_CONFIRMATIONS || $this->status === Activity::STATUS_WAITING_LIST) {
            return true;
        }
        return false;
    }

    public function isDomiciled(): ?bool
    {
        return $this->domiciled;
    }

    public function setDomiciled(bool $domiciled): self
    {
        $this->domiciled = $domiciled;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
