<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\Traits\EntityBlameableTrait;
use App\Entity\Traits\EntityTimestampableTrait;
use App\Entity\Traits\EntityCommonTrait;
use App\Entity\Traits\EntityNameTrait;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ListingValueRepository")
 * @Gedmo\Loggable
 *
 */
class ListingValue
{
    use EntityBlameableTrait;
    use EntityTimestampableTrait;
    use EntityCommonTrait;
    use EntityNameTrait;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"default","abris","dysfunction","user"})
     */
    private $id;

    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ListingType", inversedBy="listingValues")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"default"})
     */
    private ?\App\Entity\ListingType $listingType = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\ListingValue")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id",nullable=true, onDelete="SET NULL")
     */
    private ?\App\Entity\ListingValue $parent = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\HelpMessage", cascade={"persist", "remove"})
     * @Groups({"default"})
     */
    private ?\App\Entity\HelpMessage $helpMessage = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $orderInList = null;
    

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

    public function getOrderInList(): ?int
    {
        return $this->orderInList;
    }

    public function setOrderInList(?int $order): self
    {
        $this->orderInList = $order;

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

    public function getListingType(): ?ListingType
    {
        return $this->listingType;
    }

    public function setListingType(?ListingType $listingType): self
    {
        $this->listingType = $listingType;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getHelpMessage(): ?HelpMessage
    {
        return $this->helpMessage;
    }

    public function setHelpMessage(?HelpMessage $helpMessage): self
    {
        $this->helpMessage = $helpMessage;

        return $this;
    }

    /**
     * Retourne la liste des parents d'un item
     * @param ListingValue $item
     * @return type
     */
    public function getPath()
    {
        $return = [];
        $item = $this->getParent();
        while ($item) {
            $return[] = $item;
            $item = $item->getParent();
        }
//        $return[] = $item;
        return array_reverse($return);
    }
}
