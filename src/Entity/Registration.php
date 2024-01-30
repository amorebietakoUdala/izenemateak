<?php

namespace App\Entity;

use App\Repository\RegistrationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: RegistrationRepository::class)]
class Registration
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'boolean')]
    private ?bool $forMe = true;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $dni = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'string', length: 30)]
    private ?string $surname1 = null;

    #[ORM\Column(type: 'string', length: 30, nullable: true)]
    private ?string $surname2 = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $telephone1 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $telephone2 = null;

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateOfBirth = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $subscriber = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $representativeDni = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $representativeName = null;

    #[ORM\Column(type: 'string', length: 30, nullable: true)]
    private ?string $representativeSurname1 = null;

    #[ORM\Column(type: 'string', length: 30, nullable: true)]
    private ?string $representativeSurname2 = null;

    #[ORM\ManyToOne(targetEntity: Activity::class, inversedBy: 'registrations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Activity $activity = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $fortunate = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $confirmed = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $token = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $confirmationDate = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\OneToMany(targetEntity: RegistrationExtraField::class, mappedBy: 'registration', cascade: ['persist'])]
    private Collection|array $registrationExtraFields;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $paymentWho = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $paymentDni = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $paymentName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $paymentSurname1 = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $paymentSurname2 = null;

    #[ORM\Column(type: 'string', length: 29, nullable: true)]
    private ?string $paymentIBANAccount = null;

    #[ORM\Column(type: 'string', length: 1024, nullable: true)]
    private ?string $paymentURL = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $waitingListOrder = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $calledOnWaitingList = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $school = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $copied = null;

    public function __construct()
    {
        $this->registrationExtraFields = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function setDni(?string $dni): self
    {
        $this->dni = mb_strtoupper($dni);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = mb_strtoupper($name);

        return $this;
    }

    public function getSurname1(): ?string
    {
        return $this->surname1;
    }

    public function setSurname1(string $surname1): self
    {
        $this->surname1 = mb_strtoupper($surname1);

        return $this;
    }

    public function getSurname2(): ?string
    {
        return $this->surname2;
    }

    public function setSurname2(?string $surname2): self
    {
        $this->surname2 = mb_strtoupper($surname2);

        return $this;
    }

    public function getTelephone1(): ?string
    {
        return $this->telephone1;
    }

    public function setTelephone1(?string $telephone1): self
    {
        $this->telephone1 = $telephone1;

        return $this;
    }

    public function getTelephone2(): ?string
    {
        return $this->telephone2;
    }

    public function setTelephone2(?string $telephone2): self
    {
        $this->telephone2 = $telephone2;

        return $this;
    }

    public function getSubscriber(): ?bool
    {
        return $this->subscriber;
    }

    public function setSubscriber(bool $subscriber): self
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

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

    public function getForMe(): ?bool
    {
        return $this->forMe;
    }

    public function setForMe(bool $forMe): self
    {
        $this->forMe = $forMe;

        return $this;
    }

    /**
     * Get the value of representativeDni
     */ 
    public function getRepresentativeDni(): ?string
    {
        return $this->representativeDni;
    }

    /**
     * Set the value of representativeDni
     *
     * @return  self
     */ 
    public function setRepresentativeDni(?string $representativeDni): self
    {
        $this->representativeDni = mb_strtoupper($representativeDni);

        return $this;
    }

    public function getRepresentativeName(): ?string
    {
        return $this->representativeName;
    }

    public function setRepresentativeName(?string $representativeName): self
    {
        $this->representativeName = mb_strtoupper($representativeName);

        return $this;
    }

    public function getRepresentativeSurname1(): ?string
    {
        return $this->representativeSurname1;
    }

    public function setRepresentativeSurname1(?string $representativeSurname1): self
    {
        $this->representativeSurname1 = mb_strtoupper($representativeSurname1);

        return $this;
    }

    public function getRepresentativeSurname2(): ?string
    {
        return $this->representativeSurname2;
    }

    public function setRepresentativeSurname2(?string $representativeSurname2): self
    {
        $this->representativeSurname2 = mb_strtoupper($representativeSurname2);

        return $this;
    }

    public function getRepresentativeDateOfBirth(): ?\DateTimeInterface
    {
        return $this->representativeDateOfBirth;
    }

    public function setRepresentativeDateOfBirth(?\DateTimeInterface $representativeDateOfBirth): self
    {
        $this->representativeDateOfBirth = $representativeDateOfBirth;

        return $this;
    }

    public function fillWithGiltzaUser(array $giltzaUser): self {
        $this->dni = mb_strtoupper((string) $giltzaUser['dni']);
        $this->name = mb_strtoupper((string) $giltzaUser['given_name']);
        $this->surname1 = mb_strtoupper((string) $giltzaUser['surname1']);
        $this->surname2 = mb_strtoupper((string) $giltzaUser['surname2']);

        return $this;
    }

    public function getPaymentDni(): ?string
    {
        return $this->paymentDni;
    }

    public function setPaymentDni(?string $paymentDni): self
    {
        $this->paymentDni = mb_strtoupper($paymentDni);

        return $this;
    }

    public function getPaymentIBANAccount(): ?string
    {
        return $this->paymentIBANAccount;
    }

    public function setPaymentIBANAccount(?string $paymentIBANAccount): self
    {
        $iban = str_replace(' ', '', $paymentIBANAccount);
        $iban = str_replace('-', '', $iban);
        $this->paymentIBANAccount = mb_strtoupper($iban);

        return $this;
    }

    public function getPaymentSurname1(): ?string
    {
        return $this->paymentSurname1;
    }

    public function setPaymentSurname1(?string $paymentSurname1): self
    {
        $this->paymentSurname1 = mb_strtoupper($paymentSurname1);

        return $this;
    }
    
    public function getPaymentSurname2(): ?string
    {
        return $this->paymentSurname2;
    }

    public function setPaymentSurname2(?string $paymentSurname2): self
    {
        $this->paymentSurname2 = mb_strtoupper($paymentSurname2);

        return $this;
    }

    public function getPaymentWho(): ?int
    {
        return $this->paymentWho;
    }

    public function setPaymentWho(int $paymentWho): self
    {
        $this->paymentWho = $paymentWho;

        return $this;
    }

    public function getPaymentIBANAccountMasked(): ?string
    {
        return mb_strcut((string) $this->paymentIBANAccount,0,4).'****************'.mb_strcut((string) $this->paymentIBANAccount,-4);
    }

    public function getFullName(): string {
        return $this->name.' '.$this->surname1.' '.$this->surname2;
    }

    public function getRepresentativeFullName(): string {
        return $this->representativeName.' '.$this->representativeSurname1.' '.$this->representativeSurname2;
    }

    public function getFortunate(): ?bool
    {
        return $this->fortunate;
    }

    public function setFortunate(?bool $fortunate): self
    {
        $this->fortunate = $fortunate;

        return $this;
    }

    public function getConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(?bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getConfirmationDate(): ?\DateTimeInterface
    {
        return $this->confirmationDate;
    }

    public function setConfirmationDate(?\DateTimeInterface $confirmationDate): self
    {
        $this->confirmationDate = $confirmationDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
            $registrationExtraField->setRegistration($this);
        }

        return $this;
    }

    public function removeRegistrationExtraField(RegistrationExtraField $registrationExtraField): self
    {
        if ($this->registrationExtraFields->removeElement($registrationExtraField)) {
            // set the owning side to null (unless already changed)
            if ($registrationExtraField->getRegistration() === $this) {
                $registrationExtraField->setRegistration(null);
            }
        }

        return $this;
    }

    public function getPaymentName(): ?string
    {
        return $this->paymentName;
    }

    public function setPaymentName(?string $paymentName): self
    {
        $this->paymentName = mb_strtoupper($paymentName);

        return $this;
    }

    public function getPaymentURL(): ?string
    {
        return $this->paymentURL;
    }

    public function setPaymentURL(?string $paymentURL): self
    {
        $this->paymentURL = $paymentURL;

        return $this;
    }

    public function getWaitingListOrder(): ?int
    {
        return $this->waitingListOrder;
    }

    public function setWaitingListOrder(?int $waitingListOrder): self
    {
        $this->waitingListOrder = $waitingListOrder;

        return $this;
    }

    public function isCalledOnWaitingList(): ?bool
    {
        return $this->calledOnWaitingList;
    }

    public function setCalledOnWaitingList(?bool $calledOnWaitingList): self
    {
        $this->calledOnWaitingList = $calledOnWaitingList;

        return $this;
    }

    public function getSchool(): ?string
    {
        return $this->school;
    }

    public function setSchool(?string $school): self
    {
        $this->school = $school;

        return $this;
    }

    public function copyBaseData(Registration $registration): self {
        $this->forMe = $registration->getForMe();
        $this->dni = $registration->getDni();
        $this->email = $registration->getEmail();
        $this->name = $registration->getName();
        $this->surname1 = $registration->getSurname1();
        $this->surname2 = $registration->getSurname2();
        $this->telephone1 = $registration->getTelephone1();
        $this->telephone2 = $registration->getTelephone2();
        $this->dateOfBirth = $registration->getDateOfBirth();
        $this->subscriber = $registration->getSubscriber();
        $this->representativeDni = $registration->getRepresentativeDni();
        $this->representativeName = $registration->getRepresentativeName();
        $this->representativeSurname1 = $registration->getRepresentativeSurname1();
        $this->representativeSurname2 = $registration->getRepresentativeSurname2();
        $this->paymentWho = $registration->getPaymentWho();
        $this->paymentName = $registration->getPaymentName();
        $this->paymentSurname1 = $registration->getPaymentSurname1();
        $this->paymentSurname2 = $registration->getPaymentSurname2();
        $this->paymentIBANAccount = $registration->getPaymentIBANAccount();
        $this->copied = true;
        return $this;
    }

    public function isCopied(): ?bool
    {
        return $this->copied;
    }

    public function setCopied(?bool $copied): self
    {
        $this->copied = $copied;

        return $this;
    }
}
