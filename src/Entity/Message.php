<?php

namespace App\Entity;

use App\Entity\Discussion;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;

use App\Entity\Traits\EntityBlameableTrait;
use App\Entity\Traits\EntityTimestampableTrait;
use App\Entity\Traits\EntityCommonTrait;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message
{
    use EntityBlameableTrait;
    use EntityTimestampableTrait;
    use EntityCommonTrait;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"abris","dysfunction","discussion"})
     */
    private ?string $subject = null;

    /**
     * @ORM\Column(type="text")
     * @Groups({"abris","dysfunction","discussion"})
     */
    private ?string $message = null;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"abris","dysfunction","discussion"})
     */
    private $isPrivate=0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Discussion", inversedBy="messages")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private ?\App\Entity\Discussion $discussion = null;
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Message", inversedBy="childreens")
     * @ORM\JoinColumn(nullable=true)
     * @Groups({"abris","dysfunction"})
     */
    private ?\App\Entity\Message $parent = null;
    
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="parent")
     * @Groups({"abris","dysfunction"})
     */
    private $childreens;
    
    /**
     * @Gedmo\Slug(fields={"subject"})
     * @ORM\Column(type="string",length=255, unique=true)
     */
    private ?string $slug = null;

    public function __construct()
    {
        $this->childreens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getIsPrivate(): ?bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(bool $isPrivate): self
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    public function getDiscussion(): ?Discussion
    {
        return $this->discussion;
    }

    public function setDiscussion(?Discussion $discussion): self
    {
        $this->discussion = $discussion;

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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getChildreens(): Collection
    {
        return $this->childreens;
    }

    public function addChildreen(Message $childreen): self
    {
        if (!$this->childreens->contains($childreen)) {
            $this->childreens[] = $childreen;
            $childreen->setParent($this);
        }

        return $this;
    }

    public function removeChildreen(Message $childreen): self
    {
        if ($this->childreens->contains($childreen)) {
            $this->childreens->removeElement($childreen);
            // set the owning side to null (unless already changed)
            if ($childreen->getParent() === $this) {
                $childreen->setParent(null);
            }
        }

        return $this;
    }
}
