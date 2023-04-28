<?php

namespace App\Entity;

use App\Entity\Traits\EntityBlameableTrait;
use App\Entity\Traits\EntityTimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UploadedDocumentRepository")
 */
class UploadedDocument
{
    use EntityBlameableTrait;
    use EntityTimestampableTrait;    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"default", "abris", "dysfunction"})
     */
    private ?string $fileName = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $filesize = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $mimeType = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Abris", inversedBy="photos")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?\App\Entity\Abris $abris = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Dysfonctionnement", inversedBy="photos")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private ?\App\Entity\Dysfonctionnement $dysfonctionnement = null;
    
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="photo")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     * 
     */
    private ?\App\Entity\User $user = null;
    
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFilesize(): ?int
    {
        return $this->filesize;
    }

    public function setFilesize(int $filesize): self
    {
        $this->filesize = $filesize;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

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

    public function getDysfonctionnement(): ?Dysfonctionnement
    {
        return $this->dysfonctionnement;
    }

    public function setDysfonctionnement(?Dysfonctionnement $dysfonctionnement): self
    {
        $this->dysfonctionnement = $dysfonctionnement;

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
