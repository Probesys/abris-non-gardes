<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

trait EntityNameTrait
{
    /**
     * @Gedmo\Slug(fields={"name"})
     *
     * @ORM\Column(length=128, unique=true)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     *
     * @Gedmo\Versioned
     *
     * @Groups({"default","abris","dysfunction", "discussion" ,"user"})
     */
    private $name;

    /**
     * @return string
     */
    public function __toString()
    {
        if (0 == strlen($this->slug)) {
            return ' ';
        }

        return $this->name;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }
}
