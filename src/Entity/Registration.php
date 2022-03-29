<?php

namespace App\Entity;

use App\Repository\RegistrationRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=RegistrationRepository::class)
 */
class Registration
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
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dni;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $surname1;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $surname2;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telephone1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telephone2;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateOfBirth;

    /**
     * @ORM\Column(type="boolean")
     */
    private $subscriber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $representativeDni;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $representativeName;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $representativeSurname1;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $representativeSurname2;

    /**
     * @ORM\ManyToOne(targetEntity=Course::class, inversedBy="registrations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $course;

    /**
     * @ORM\Column(type="boolean")
     */
    private $forMe;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    private $paymentDni;

    /**
     * @ORM\Column(type="string", length=29, nullable=false)
     */
    private $paymentIBANAccount;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $fortunate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $confirmed;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $confirmationDate;

    public function __construct()
    {
        $this->forMe = true;
        $this->subscriber = false;
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

    public function setDateOfBirth(\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;

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
        $this->dni = mb_strtoupper($giltzaUser['dni']);
        $this->name = mb_strtoupper($giltzaUser['given_name']);
        $this->surname1 = mb_strtoupper($giltzaUser['surname1']);
        $this->surname2 = mb_strtoupper($giltzaUser['surname2']);

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

    public function getPaymentIBANAccountMasked(): ?string
    {
        return mb_strcut($this->paymentIBANAccount,0,4).'****************'.mb_strcut($this->paymentIBANAccount,-4);
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
}
