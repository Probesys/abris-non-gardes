<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\EntityCommonTrait;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CityRepository")
 * @Gedmo\Loggable
 */
class City
{
    use EntityCommonTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"default","abris", "user"})
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"default","abris", "user"})
     */
    private ?string $zipCode = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"default","abris", "user"})
     */
    private ?string $insee = null;

    /**
     *
     * @ORM\Column(name="department", type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     * @Groups({"default","abris"})
     */
    private ?string $department = null;

    /**
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=true)
     * @Gedmo\Versioned
     */
    private ?string $country = null;

    /**
     * @ORM\Column(name="coordinate", type="string", length=255, nullable=true)
     */
    private ?string $coordinate = null;

    /**
     * @Gedmo\Slug(fields={"name", "zipCode"})
     * @ORM\Column(length=128, unique=true)
     */
    private ?string $slug = null;

    /**
     * @Gedmo\Slug(fields={"department"})
     * @ORM\Column(length=128, unique=false)
     */
    private ?string $slugDepartment = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Abris", mappedBy="city")
     */
    private $abris;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Territory", mappedBy="cities", cascade={"persist"})
     */
    public $territories;

    public function __construct()
    {
        $this->abris = new ArrayCollection();
        $this->territories = new ArrayCollection();
    }

    public function __toString()
    {
        if (0 == strlen($this->name)) {
            return '  ';
        }

        return ' '.$this->name;
    }
    
    /**
     * @return string
     */
    public function getListTerritories()
    {
        $return = '';
        $separator = '';
        foreach ($this->getTerritories() as $territory) {
            $return .= $separator.$territory->getName();
            $separator = ', ';
        }

        return $return;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getInsee(): ?string
    {
        return $this->insee;
    }

    public function setInsee(?string $insee): self
    {
        $this->insee = $insee;

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

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCoordinate(): ?string
    {
        return $this->coordinate;
    }

    public function setCoordinate(?string $coordinate): self
    {
        $this->coordinate = $coordinate;

        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(?string $department): self
    {
        $this->department = $department;

        return $this;
    }

    public function getSlugDepartment(): ?string
    {
        return $this->slugDepartment;
    }

    public function setSlugDepartment(string $slugDepartment): self
    {
        $this->slugDepartment = $slugDepartment;

        return $this;
    }

    /**
     * @return Collection|Abris[]
     */
    public function getAbris(): Collection
    {
        return $this->abris;
    }

    public function addAbri(Abris $abri): self
    {
        if (!$this->abris->contains($abri)) {
            $this->abris[] = $abri;
            $abri->setCity($this);
        }

        return $this;
    }

    public function removeAbri(Abris $abri): self
    {
        if ($this->abris->contains($abri)) {
            $this->abris->removeElement($abri);
            // set the owning side to null (unless already changed)
            if ($abri->getCity() === $this) {
                $abri->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Territory[]
     */
    public function getTerritories(): Collection
    {
        return $this->territories;
    }

    public function addTerritory(Territory $territory): self
    {
        if (!$this->territories->contains($territory)) {
            $this->territories[] = $territory;
            $territory->addCity($this);
        }

        return $this;
    }

    public function removeTerritory(Territory $territory): self
    {
        if ($this->territories->contains($territory)) {
            $this->territories->removeElement($territory);
            $territory->removeCity($this);
        }

        return $this;
    }
}
