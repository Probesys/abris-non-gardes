<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AttributesWithQtyRepository")
 */
class AttributesWithQty
{
    /**
     * @ORM\Id()
     *
     * @ORM\GeneratedValue()
     *
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Abris", inversedBy="mobiliers")
     *
     * @ORM\JoinColumn(nullable=true)
     */
    private ?\App\Entity\Abris $abrisMobilier = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ListingValue")
     *
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=true)
     *
     * @Groups({"abris"})
     */
    private ?\App\Entity\ListingValue $listingValue = null;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"abris"})
     */
    private ?int $qty = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Abris", inversedBy="couchages")
     *
     * @ORM\JoinColumn(nullable=true)
     */
    private ?\App\Entity\Abris $abrisCouchage = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Abris", inversedBy="placeDeFeuInterieur")
     */
    private ?\App\Entity\Abris $abrisPlaceDeFeuInterieur = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Abris", inversedBy="mobilierPiqueniqueExterieur")
     */
    private ?\App\Entity\Abris $abrisMobilierPiqueniqueExterieur = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Abris", inversedBy="materielDivers")
     */
    private ?\App\Entity\Abris $abrisMaterielDivers = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbrisMobilier(): ?Abris
    {
        return $this->abrisMobilier;
    }

    public function setAbrisMobilier(?Abris $abrisMobilier): self
    {
        $this->abrisMobilier = $abrisMobilier;

        return $this;
    }

    public function getListingValue(): ?ListingValue
    {
        return $this->listingValue;
    }

    public function setListingValue(?ListingValue $listingValue): self
    {
        $this->listingValue = $listingValue;

        return $this;
    }

    public function getQty(): ?int
    {
        return $this->qty;
    }

    public function setQty(int $qty): self
    {
        $this->qty = $qty;

        return $this;
    }

    public function getAbrisCouchage(): ?Abris
    {
        return $this->abrisCouchage;
    }

    public function setAbrisCouchage(?Abris $abrisCouchage): self
    {
        $this->abrisCouchage = $abrisCouchage;

        return $this;
    }

    public function getAbrisPlaceDeFeuInterieur(): ?Abris
    {
        return $this->abrisPlaceDeFeuInterieur;
    }

    public function setAbrisPlaceDeFeuInterieur(?Abris $abrisPlaceDeFeuInterieur): self
    {
        $this->abrisPlaceDeFeuInterieur = $abrisPlaceDeFeuInterieur;

        return $this;
    }

    public function getAbrisMobilierPiqueniqueExterieur(): ?Abris
    {
        return $this->abrisMobilierPiqueniqueExterieur;
    }

    public function setAbrisMobilierPiqueniqueExterieur(?Abris $abrisMobilierPiqueniqueExterieur): self
    {
        $this->abrisMobilierPiqueniqueExterieur = $abrisMobilierPiqueniqueExterieur;

        return $this;
    }

    public function getAbrisMaterielDivers(): ?Abris
    {
        return $this->abrisMaterielDivers;
    }

    public function setAbrisMaterielDivers(?Abris $abrisMaterielDivers): self
    {
        $this->abrisMaterielDivers = $abrisMaterielDivers;

        return $this;
    }
}
