<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Annotations\ListingAnnotation;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"email"}, message="Il existe déjà un compte ayant cette adresse email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @Groups({"user"})
     * @var UuidInterface
     */
    private $id;

    /**
     * @ORM\Column(name="login", type="string", unique=true)
     *
     * @Assert\NotBlank()
     */
    private ?string $login = null;

    /**
     * @Assert\Length(max=4096)
     */
    private ?string $plainPassword = null;

    /**
     * @ORM\Column(name="password", type="string")
     */
    private ?string $password = null;
    
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email()
     * @Groups({"user"})
     */
    private ?string $email = null;

    /**
     * @ORM\Column(name="roles", type="simple_array")
     * @Groups({"abris","dysfunction", "discussion" ,"user"})
     * @var string[]
     */
    private $roles = [];

    /**
     * @ORM\Column(name="created", type="datetime")
     * @var DateTime
     */
    private $created;

    /**
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     * @var DateTime
     */
    private $updated;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"abris","dysfunction", "discussion" ,"user"})
     */
    private ?string $lastName = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"abris","dysfunction", "discussion" ,"user"})
     */
    private ?string $firstName = null;

    /**
     * @Gedmo\Slug(fields={"structureName","lastName", "firstName"})
     * @ORM\Column(type="string",length=500, unique=true)
     */
    private ?string $slug = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Abris", mappedBy="proprietaires")
     */
    private $proprietaireAbris;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Abris", mappedBy="gestionnaires")
     */
    private $gestionnaireAbris;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ListingValue")
     * @ORM\JoinColumn(nullable=true)
     * @ListingAnnotation(idListingUuid="77091221-21dd-48d8-8cad-075b47af69aa")
     * @Groups({"user"})
     */
    private ?\App\Entity\ListingValue $userType = null;
    
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Coordinate", inversedBy="user", cascade={"persist", "remove"})
     */
    private ?\App\Entity\Coordinate $coordinate = null;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Abris", mappedBy="followers", cascade={"persist"})
     * @Groups({"user"})
     */
    public $abrisFavoris;
    
    
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\UploadedDocument", mappedBy="user", cascade={"persist", "remove"})
     */
    private ?\App\Entity\UploadedDocument $photo = null;

    /**
     * @ORM\OneToMany(targetEntity=Dysfonctionnement::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"user"})
     */
    private $dysfonctionnements;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"abris","dysfunction", "discussion" ,"user"})
     */
    private ?string $structureName = null;


    public function __construct()
    {
        $this->proprietaireAbris = new ArrayCollection();
        $this->gestionnaireAbris = new ArrayCollection();
        $this->abrisFavoris = new ArrayCollection();
        $this->dysfonctionnements = new ArrayCollection();
    }
    
    public function __toString()
    {
        $return = '';
        if ($this->structureName) {
            $return .= $this->structureName.' / ';
        }
        $return .= $this->lastName.' '.$this->firstName;
        
        return $return;
    }

    /**
     * @ORM\PrePersist
     *
     * @throws Exception
     */
    public function onPrePersist(): void
    {
        $this->id = Uuid::uuid4();
        $this->created = new DateTime('NOW');
    }
    
    public function onPostPersist(): void
    {
        $this->login = $this->email;
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate(): void
    {
        $this->updated = new DateTime('NOW');
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function getUsername(): string
    {
        return $this->login;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $password): void
    {
        $this->plainPassword = $password;

        // forces the object to look "dirty" to Doctrine. Avoids
        // Doctrine *not* saving this entity, if only plainPassword changes
        $this->password = null;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return null
     */
    public function getSalt()
    {
        // The bcrypt algorithm doesn't require a separate salt.
        return null;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
    
    public function addRole($role)
    {
        $roles = $this->roles;
        if (!in_array($role, $roles)) {
            array_push($roles, $role);
        }
        $this->roles = $roles;
    }
    
    public function removeRole($role)
    {
        $roles = $this->roles;
        $key = array_search($role, $roles);
        if (false !== $key) {
            unset($roles[$key]);
        }
        $this->roles = $roles;
    }
    
    public function resetRoles()
    {
        $this->roles = [];
    }
    
    public function hasRole($role)
    {
        $roles = $this->roles;
        return in_array($role, $roles);
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    /**
     * @return Collection|Abris[]
     */
    public function getProprietaireAbris(): Collection
    {
        return $this->proprietaireAbris;
    }

    public function addProprietaireAbri(Abris $proprietaireAbri): self
    {
        if (!$this->proprietaireAbris->contains($proprietaireAbri)) {
            $this->proprietaireAbris[] = $proprietaireAbri;
            $proprietaireAbri->addProprietaire($this);
        }

        return $this;
    }

    public function removeProprietaireAbri(Abris $proprietaireAbri): self
    {
        if ($this->proprietaireAbris->contains($proprietaireAbri)) {
            $this->proprietaireAbris->removeElement($proprietaireAbri);
            $proprietaireAbri->removeProprietaire($this);
        }

        return $this;
    }

    /**
     * @return Collection|Abris[]
     */
    public function getGestionnaireAbris(): Collection
    {
        return $this->gestionnaireAbris;
    }

    public function addGestionnaireAbri(Abris $gestionnaireAbri): self
    {
        if (!$this->gestionnaireAbris->contains($gestionnaireAbri)) {
            $this->gestionnaireAbris[] = $gestionnaireAbri;
            $gestionnaireAbri->addGestionnaire($this);
        }

        return $this;
    }

    public function removeGestionnaireAbri(Abris $gestionnaireAbri): self
    {
        if ($this->gestionnaireAbris->contains($gestionnaireAbri)) {
            $this->gestionnaireAbris->removeElement($gestionnaireAbri);
            $gestionnaireAbri->removeGestionnaire($this);
        }

        return $this;
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

    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function setUpdated(?\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getUserType(): ?ListingValue
    {
        return $this->userType;
    }

    public function setUserType(?ListingValue $userType): self
    {
        $this->userType = $userType;

        return $this;
    }

    public function getCoordinate(): ?Coordinate
    {
        return $this->coordinate;
    }

    public function setCoordinate(?Coordinate $coordinate): self
    {
        $this->coordinate = $coordinate;

        return $this;
    }

    /**
     * @return Collection|Abris[]
     */
    public function getAbrisFavoris(): Collection
    {
        return $this->abrisFavoris;
    }

    public function addAbrisFavori(Abris $abrisFavori): self
    {
        if (!$this->abrisFavoris->contains($abrisFavori)) {
            $this->abrisFavoris[] = $abrisFavori;
            $abrisFavori->addFollower($this);
        }

        return $this;
    }

    public function removeAbrisFavori(Abris $abrisFavori): self
    {
        if ($this->abrisFavoris->contains($abrisFavori)) {
            $this->abrisFavoris->removeElement($abrisFavori);
            $abrisFavori->removeFollower($this);
        }

        return $this;
    }

    public function getPhoto(): ?UploadedDocument
    {
        return $this->photo;
    }

    public function setPhoto(?UploadedDocument $photo): self
    {
        $this->photo = $photo;

        // set (or unset) the owning side of the relation if necessary
        $newUser = null === $photo ? null : $this;
        if ($photo->getUser() !== $newUser) {
            $photo->setUser($newUser);
        }

        return $this;
    }

    /**
     * @return Collection|Dysfonctionnement[]
     */
    public function getDysfonctionnements(): Collection
    {
        return $this->dysfonctionnements;
    }

    public function addDysfonctionnement(Dysfonctionnement $dysfonctionnement): self
    {
        if (!$this->dysfonctionnements->contains($dysfonctionnement)) {
            $this->dysfonctionnements[] = $dysfonctionnement;
            $dysfonctionnement->setUser($this);
        }

        return $this;
    }

    public function removeDysfonctionnement(Dysfonctionnement $dysfonctionnement): self
    {
        if ($this->dysfonctionnements->contains($dysfonctionnement)) {
            $this->dysfonctionnements->removeElement($dysfonctionnement);
            // set the owning side to null (unless already changed)
            if ($dysfonctionnement->getUser() === $this) {
                $dysfonctionnement->setUser(null);
            }
        }

        return $this;
    }

    public function getStructureName(): ?string
    {
        return $this->structureName;
    }

    public function setStructureName(?string $structureName): self
    {
        $this->structureName = $structureName;

        return $this;
    }
}
