<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Entity\Traits\EntityBlameableTrait;
use App\Entity\Traits\EntityTimestampableTrait;
use App\Entity\Traits\EntityCommonTrait;
use App\Entity\Traits\EntityNameTrait;
use App\Entity\City;

/**
 * @Gedmo\Tree(type="nested")
 * @ORM\Entity(repositoryClass="App\Repository\NestedTerritoryRepository")
 * @ORM\Table(name="territory", indexes={@ORM\Index(name="territory_slugs", columns={"slug"})})
 * @Gedmo\Loggable
 */
class Territory
{
    use EntityBlameableTrait;
    use EntityTimestampableTrait;
    use EntityCommonTrait;
    use EntityNameTrait;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", length=255, nullable=true)
     */
    private $root;

    /**
     * @var int
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer", length=255)
     */
    private $lft;

    /**
     * @var int
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer", length=255, nullable=true)
     */
    private $rgt;

    /**
     * @var int
     * @Gedmo\TreeLevel
     * @ORM\Column(name="level", type="integer", length=255, nullable=true)
     */
    private $level;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="App\Entity\Territory", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private ?\App\Entity\Territory $parent = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Territory", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\City", inversedBy="territories", cascade={"persist"})
     * @ORM\JoinTable(name="territories_cities")
     */
    protected $cities;


    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->cities = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return strlen($this->name) ? $this->name : ' ';
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param int $id
     *
     * @return Territory
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return Territory
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Territory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set root.
     *
     * @param int $root
     *
     * @return Territory
     */
    public function setRoot($root)
    {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root.
     *
     * @return int
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set lft.
     *
     * @param int $lft
     *
     * @return Territory
     */
    public function setLft($lft)
    {
        $this->lft = $lft;

        return $this;
    }

    /**
     * Get lft.
     *
     * @return int
     */
    public function getLft()
    {
        return $this->lft;
    }

    /**
     * Set rgt.
     *
     * @param int $rgt
     *
     * @return Territory
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;

        return $this;
    }

    /**
     * Get rgt.
     *
     * @return int
     */
    public function getRgt()
    {
        return $this->rgt;
    }

    /**
     * Set level.
     *
     * @param int $level
     *
     * @return Territory
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level.
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param Territory $parent
     */
    public function setParent(Territory $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return object
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add city.
     *
     * @param City $city
     *
     * @return Territory
     */
    public function addCity(City $city)
    {
        if ($this->cities->contains($city)) {
            return;
        }

        $this->cities[] = $city;
        $city->addTerritory($this);

        return $this;
    }

    /**
     * Remove city.
     *
     * @param City $city
     */
    public function removeCity(City $city)
    {
        if (!$this->cities->contains($city)) {
            return;
        }
        $this->cities->removeElement($city);
    }

    /**
     * Get cities.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCities()
    {
        return $this->cities;
    }

    /**
     * Add child.
     *
     * @param Territory $child
     *
     * @return Territory
     */
    public function addChild(Territory $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child.
     *
     * @param Territory $child
     *
     * @return bool tRUE if this collection contained the specified element, FALSE otherwise
     */
    public function removeChild(Territory $child)
    {
        return $this->children->removeElement($child);
    }

    /**
     * Get children.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }



    /**
     * Set created.
     *
     * @param \DateTime $created
     *
     * @return Territory
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Set updated.
     *
     * @param \DateTime $updated
     *
     * @return Territory
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }
}
