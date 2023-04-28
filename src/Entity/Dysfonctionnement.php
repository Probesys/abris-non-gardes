<?php

namespace App\Entity;

use App\Entity\Traits\EntityBlameableTrait;
use App\Entity\Traits\EntityTimestampableTrait;
use App\Repository\DysfonctionnementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Annotations\ListingAnnotation;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DysfonctionnementRepository")
 */
class Dysfonctionnement
{
    use EntityBlameableTrait;
    use EntityTimestampableTrait;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"abris","dysfunction","user"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ListingValue")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @ListingAnnotation(idListingUuid="da73e657-d6c3-420a-a6cf-a6f03dd8148c")
     * @Groups({"abris","dysfunction","user"})
     */
    private ?\App\Entity\ListingValue $natureDys = null;

    /**
     * @ORM\ManyToOne(targetEntity="ListingValue")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @ListingAnnotation(idListingUuid="da73e657-d6c3-420a-a6cf-a6f03dd8148c")
     * @Groups({"abris","dysfunction","user"})
     */
    private ?\App\Entity\ListingValue $elementDys = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"abris","dysfunction","user"})
     */
    private ?string $description = null;

    /**
     * @ORM\OneToMany(targetEntity="UploadedDocument", mappedBy="dysfonctionnement")
     * @Groups({"abris","dysfunction","user"})
     */
    private $photos;

    /**
     * @ORM\ManyToOne(targetEntity="ListingValue")
     * @ListingAnnotation(idListingUuid="36abf808-9f70-4a80-b388-78f89413a0ac")
     * @ORM\JoinColumn(onDelete="SET NULL")
     * @Groups({"abris","dysfunction","user"})
     */
    private ?\App\Entity\ListingValue $statusDys = null;

    /**
     * @ORM\ManyToOne(targetEntity="Abris", inversedBy="dysfonctionnements")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Groups({"dysfunction","user"})
     */
    private ?\App\Entity\Abris $abris = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Discussion", mappedBy="dysfonctionnement", cascade={"persist", "remove"})
     * @Groups({"abris","dysfunction","user"})
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private ?\App\Entity\Discussion $discussion = null;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="dysfonctionnements")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * @Gedmo\Blameable(on="create")
     */
    private ?\App\Entity\User $user = null;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }
    
    public function __toString()
    {
        return $this->getAbris()." ".$this->getNatureDys().$this->getCreated()->format('d/m/Y');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNatureDys(): ?ListingValue
    {
        return $this->natureDys;
    }

    public function setNatureDys(?ListingValue $natureDys): self
    {
        $this->natureDys = $natureDys;

        return $this;
    }

    public function getElementDys(): ?ListingValue
    {
        return $this->elementDys;
    }

    public function setElementDys(?ListingValue $elementDys): self
    {
        $this->elementDys = $elementDys;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|UploadedDocument[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(UploadedDocument $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setDysfonctionnement($this);
        }

        return $this;
    }

    public function removePhoto(UploadedDocument $photo): self
    {
        if ($this->photos->contains($photo)) {
            $this->photos->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getDysfonctionnement() === $this) {
                $photo->setDysfonctionnement(null);
            }
        }

        return $this;
    }

    public function getStatusDys(): ?ListingValue
    {
        return $this->statusDys;
    }

    public function setStatusDys(?ListingValue $statusDys): self
    {
        $this->statusDys = $statusDys;

        return $this;
    }

    public function getAbris(): ?Abris
    {
        return $this->abris;
    }

    public function setAbris(?Abris $abris): self
    {
        $this->abris = $abris;

        return $this;
    }

    public function getDiscussion(): ?Discussion
    {
        return $this->discussion;
    }

    public function setDiscussion(Discussion $discussion): self
    {
        $this->discussion = $discussion;

        // set the owning side of the relation if necessary
        if ($discussion->getName() !== $this) {
            $discussion->setName($this);
        }

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
}
