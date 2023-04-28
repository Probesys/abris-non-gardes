<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

use App\Entity\Traits\EntityBlameableTrait;
use App\Entity\Traits\EntityTimestampableTrait;
use App\Entity\Traits\EntityCommonTrait;
use App\Entity\Traits\EntityNameTrait;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ListingTypeRepository")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\Loggable
 */
class ListingType
{
    use EntityBlameableTrait;
    use EntityTimestampableTrait;
    use EntityCommonTrait;
    use EntityNameTrait;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"default"})
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ListingValue", mappedBy="listingType")
     */
    private $listingValues;
    
    /**
     *
     * @ORM\Column(type="uuid", unique=true)
     * @Groups({"default"})
     * @var UuidInterface
     */
    private $uuid;
    
    /**
     * @ORM\PrePersist
     *
     * @throws Exception;
     */
    public function onPrePersist(): void
    {
        $this->uuid = Uuid::uuid4();
    }

    public function __construct()
    {
        $this->listingValues = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|ListingValue[]
     */
    public function getListingValues(): Collection
    {
        return $this->listingValues;
    }

    public function addListingValue(ListingValue $listingValue): self
    {
        if (!$this->listingValues->contains($listingValue)) {
            $this->listingValues[] = $listingValue;
            $listingValue->setListingType($this);
        }

        return $this;
    }

    public function removeListingValue(ListingValue $listingValue): self
    {
        if ($this->listingValues->contains($listingValue)) {
            $this->listingValues->removeElement($listingValue);
            // set the owning side to null (unless already changed)
            if ($listingValue->getListingType() === $this) {
                $listingValue->setListingType(null);
            }
        }

        return $this;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid($uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }
}
