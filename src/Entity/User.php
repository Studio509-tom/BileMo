<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]

class User implements UserInterface, PasswordAuthenticatedUserInterface


{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["user_read"])]
    private ?int $id = null;
    
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "referent_id", referencedColumnName: "id", nullable: true)]
    private ?User $referent = null;

    #[ORM\Column(length: 180)]
    #[Groups(["user_read"])]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: "json")]
    #[Groups(["user_read"])]
    private array $roles = ["ROLE_USER"];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: "Le mots de passe est obligatoire")]
    private ?string $password = null;

    /**
     * @var Collection<int, Customer>
     */
    #[ORM\OneToMany(targetEntity: Customer::class, mappedBy: 'customer')]
    private Collection $customers;

    #[ORM\Column(length: 1024)]
    #[Groups(["user_read"])]
    #[Assert\Length(min: 3, max: 1024, minMessage: "Le token doit faire au moins {{ limit }} caractères", maxMessage: "Le titre ne peut pas faire plus de {{ limit }} caractères")]
    private ?string $token = null;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(){
        return $this->getUserIdentifier();
    }

     public function getReferent(): ?User
    {
        return $this->referent;
    }

    public function setReferent(?User $referent): static
    {
        $this->referent = $referent;
        return $this;
    }
    // public function getReferenceCustomer(): ?Customer
    // {
    //     return $this->referenceCustomer;
    // }

    // public function setReferenceCustomer(?Customer $referenceCustomer): static
    // {
    //     $this->referenceCustomer = $referenceCustomer;

    //     return $this;
    // }

    // public function getCustomer(): ?Customer
    // {
    //     return $this->customer;
    // }

    // public function setCustomer(?Customer $customer): static
    // {
    //     // unset the owning side of the relation if necessary
    //     if ($customer === null && $this->customer !== null) {
    //         $this->customer->setCustomer(null);
    //     }

    //     // set the owning side of the relation if necessary
    //     if ($customer !== null && $customer->getCustomer() !== $this) {
    //         $customer->setCustomer($this);
    //     }

    //     $this->customer = $customer;

    //     return $this;
    // }

    /**
     * @return Collection<int, Customer>
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customer $customer): static
    {
        if (!$this->customers->contains($customer)) {
            $this->customers->add($customer);
            $customer->setCustomer($this);
        }

        return $this;
    }

    public function removeCustomer(Customer $customer): static
    {
        if ($this->customers->removeElement($customer)) {
            // set the owning side to null (unless already changed)
            if ($customer->getCustomer() === $this) {
                $customer->setCustomer(null);
            }
        }

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }
}
