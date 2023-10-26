<?php

namespace App\Entity;

use App\Entity\Traits\EntityCommonTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CoordinateRepository")
 */
class Coordinate
{
    use EntityCommonTrait;
    /**
     * @ORM\Id()
     *
     * @ORM\GeneratedValue()
     *
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $addressLine1 = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $addressLine2 = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $addressLine3 = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $cedex = null;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private ?string $phone = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $mobilePhone = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\City")
     *
     * @ORM\JoinColumn(nullable=true)
     */
    private ?\App\Entity\City $city = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="coordinate", cascade={"persist", "remove"})
     */
    private ?\App\Entity\User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFormatedAddress()
    {
        $return = $this->getAddressLine1();
        if ($this->getAddressLine2()) {
            $return .= '<br/>'.$this->getAddressLine2();
        }
        if ($this->getCity()) {
            $return .= '<br/>'.$this->getCity()->getZipCode().' '.$this->getCity()->getName();
            if ($this->cedex) {
                $return .= ' '.$this->cedex;
            }
        }
        $return = nl2br(str_replace('<br />', '', $return));

        return $return;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(?string $addressLine1): self
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(?string $addressLine2): self
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function getCedex(): ?string
    {
        return $this->cedex;
    }

    public function setCedex(?string $cedex): self
    {
        $this->cedex = $cedex;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    public function setMobilePhone(?string $mobilePhone): self
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        // set (or unset) the owning side of the relation if necessary
        $newCoordinate = null === $user ? null : $this;
        if ($user->getCoordinate() !== $newCoordinate) {
            $user->setCoordinate($newCoordinate);
        }

        return $this;
    }

    public function getAddressLine3(): ?string
    {
        return $this->addressLine3;
    }

    public function setAddressLine3(?string $addressLine3): self
    {
        $this->addressLine3 = $addressLine3;

        return $this;
    }
}
