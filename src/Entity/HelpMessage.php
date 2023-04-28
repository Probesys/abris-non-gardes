<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

use App\Entity\Traits\EntityBlameableTrait;
use App\Entity\Traits\EntityTimestampableTrait;
use App\Entity\Traits\EntityCommonTrait;

use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HelpMessageRepository")
 * 
 */
class HelpMessage 
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
     * 
     * @ORM\Column(type="string", length=255)
     * @Groups({"default"})
     */
    private ?string $name = null;

    /**
     * 
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $message = null;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string",length=128, unique=true)
     */
    private ?string $slug = null;

    

    public function __construct()
    {
    }

    public function __toString()
    {
        return $this->name;
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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

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

    
}
