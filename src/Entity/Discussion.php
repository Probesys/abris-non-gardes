<?php

namespace App\Entity;

use App\Entity\Message;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use App\Entity\Traits\EntityBlameableTrait;
use App\Entity\Traits\EntityTimestampableTrait;
use App\Entity\Traits\EntityCommonTrait;
use App\Entity\Traits\EntityNameTrait;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * @ORM\Entity(repositoryClass="App\Repository\DiscussionRepository")
 */
class Discussion
{
    use EntityBlameableTrait;
    use EntityTimestampableTrait;
    use EntityCommonTrait;
    use EntityNameTrait;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"abris","dysfunction","discussion"})
     */
    private $id;
    
    /**
     * 
     * @ORM\Column(type="string", length=255)
     * @Groups({"abris", "discussion"})
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Dysfonctionnement", inversedBy="discussion")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private ?\App\Entity\Dysfonctionnement $dysfonctionnement = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Abris", inversedBy="discussions")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups({"discussion"})
     */
    private ?\App\Entity\Abris $abris = null;
    
    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string",length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="discussion")
     * @Groups({"abris","dysfunction","discussion"})
     */
    private $messages;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isPrivate = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"discussion"})
     */
    private ?string $description = null;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDysfonctionnement(): ?Dysfonctionnement
    {
        return $this->dysfonctionnement;
    }

    public function setDysfonctionnement(Dysfonctionnement $dysfonctionnement): self
    {
        $this->dysfonctionnement = $dysfonctionnement;

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

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setDiscussion($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getDiscussion() === $this) {
                $message->setDiscussion(null);
            }
        }

        return $this;
    }

    public function getIsPrivate(): ?bool
    {
        return $this->isPrivate;
    }

    public function setIsPrivate(?bool $isPrivate): self
    {
        $this->isPrivate = $isPrivate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
