<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\Traits\EntityBlameableTrait;
use App\Entity\Traits\EntityTimestampableTrait;
use App\Entity\Traits\EntityCommonTrait;
use App\Entity\Traits\EntityNameTrait;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PageRepository")
 * 
 */
class Page 
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
     * 
     * @ORM\Column(type="string", length=255)
     * @Groups({"default"})
     */
    private $name;

    /**
     * 
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"default"})
     */
    private ?string $body = null;
    
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $orderInList = null;
    
    /**
     * @ORM\Column(type="boolean")
     */
    private $dontListedInFrontPage = 0;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string",length=128, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"default"})
     */
    private ?string $linkText = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getOrderInList(): ?int
    {
        return $this->orderInList;
    }

    public function setOrderInList(?int $orderInList): self
    {
        $this->orderInList = $orderInList;

        return $this;
    }

    public function getDontListedInFrontPage(): ?bool
    {
        return $this->dontListedInFrontPage;
    }

    public function setDontListedInFrontPage(bool $dontListedInFrontPage): self
    {
        $this->dontListedInFrontPage = $dontListedInFrontPage;

        return $this;
    }

    public function getLinkText(): ?string
    {
        return $this->linkText;
    }

    public function setLinkText(?string $linkText): self
    {
        $this->linkText = $linkText;

        return $this;
    }

    
    
    
}
