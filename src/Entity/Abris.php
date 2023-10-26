<?php

namespace App\Entity;

use App\Annotations\ListingAnnotation;
use App\Entity\Traits\EntityBlameableTrait;
use App\Entity\Traits\EntityCommonTrait;
use App\Entity\Traits\EntityNameTrait;
use App\Entity\Traits\EntityTimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AbrisRepository")
 *
 * @ORM\Table(name="abris")
 *
 * @ORM\HasLifecycleCallbacks
 *
 * @Gedmo\Loggable
 */
class Abris
{
    use EntityBlameableTrait;
    use EntityTimestampableTrait;
    use EntityCommonTrait;
    use EntityNameTrait;

    /**
     * @ORM\Id
     *
     * @ORM\Column(type="uuid", unique=true)
     *
     * @Groups({"abris","dysfunction", "discussion" ,"user"})
     *
     * @var UuidInterface
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ListingValue")
     *
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     *
     * @ListingAnnotation(idListingUuid="b0cc7b35-5bee-4823-a8e9-70508c91cd51")
     *
     * @Groups({"abris","dysfunction"})
     */
    private ?ListingValue $type = null;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"abris","dysfunction"})
     *
     * @Assert\Regex(
     *     pattern="/^((\-?|\+?)?\d+(\.\d+)?),\s*((\-?|\+?)?\d+(\.\d+)?)$/",
     *     match=true,
     *     message="Le format du champ doit être de la forme 45.179225,5.724737"
     * )
     */
    private ?string $coordinate = null;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="abris")
     *
     * @ORM\JoinColumn(nullable=false)
     *
     * @Groups({"default", "abris","dysfunction","user"})
     */
    private ?City $city = null;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="proprietaireAbris")
     *
     * @ORM\JoinTable(name="abris_proprietaires")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @Groups({"abris","dysfunction"})
     */
    private $proprietaires;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="gestionnaireAbris")
     *
     * @ORM\JoinTable(name="abris_gestionnaires")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $gestionnaires;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"abris"})
     */
    private ?int $capaciteAccueil = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Groups({"abris"})
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="ListingValue")
     *
     * @ORM\JoinTable(name="abris_types_toit")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @ListingAnnotation(idListingUuid="d4543c60-7e7c-4df3-bc12-f8baae7ec0ae")
     *
     * @Groups({"abris"})
     */
    private $toit;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"abris"})
     */
    private ?bool $chemineeEnPierreSurLeToit = null;

    /**
     * @ORM\ManyToOne(targetEntity="ListingValue")
     *
     * @ListingAnnotation(idListingUuid="1fe5ef1a-564e-45a1-8e18-77ba40521326")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @Groups({"abris"})
     */
    private ?ListingValue $sortieFumees = null;

    /**
     * @ORM\ManyToOne(targetEntity="ListingValue")
     *
     * @ListingAnnotation(idListingUuid="3c6e74fe-f55d-4dfd-a8ed-612f41734c51")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @Groups({"abris"})
     */
    private ?ListingValue $materiauSortieFumees = null;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"abris"})
     */
    private ?int $nbPortes = null;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"abris"})
     */
    private ?int $nbFenetres = null;

    /**
     * @ORM\ManyToOne(targetEntity="ListingValue")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @ListingAnnotation(idListingUuid="5456d32c-543f-4511-a6f6-1f2ce533cc68")
     *
     * @Groups({"abris"})
     */
    private ?ListingValue $typeMur = null;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"abris"})
     */
    private ?bool $etage = null;

    /**
     * @ORM\ManyToMany(targetEntity="ListingValue")
     *
     * @ORM\JoinTable(name="abris_accesEtages")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @ListingAnnotation(idListingUuid="65640c10-9c90-40e8-af5d-82f67f18e9be")
     *
     * @Groups({"abris"})
     */
    private $accesEtage;

    /**
     * @ORM\ManyToMany(targetEntity="ListingValue")
     *
     * @ORM\JoinTable(name="abris_typeAccesEtages")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @ListingAnnotation(idListingUuid="ebea69ef-79fd-4ac5-bbd5-c9f5107918b2")
     *
     * @Groups({"abris"})
     */
    private $typeAccesEtage;

    /**
     * @ORM\ManyToMany(targetEntity="ListingValue")
     *
     * @ORM\JoinTable(name="abris_typeSols")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @ListingAnnotation(idListingUuid="eb393982-56eb-457b-88db-0ed2599af93e")
     *
     * @Groups({"abris"})
     */
    private $typeSol;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"abris"})
     */
    private ?bool $citerneExterieure = null;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"abris"})
     */
    private ?bool $appentisExterieur = null;

    /**
     * @ORM\OneToMany(targetEntity="AttributesWithQty",mappedBy="abrisMobilier", orphanRemoval=true, cascade={"persist"})
     *
     * @ListingAnnotation(idListingUuid="2412c70c-d215-4181-941b-82b8fe30f23c")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @Groups({"abris"})
     */
    private $mobiliers;

    /**
     * @ORM\OneToMany(targetEntity="AttributesWithQty",mappedBy="abrisCouchage", orphanRemoval=true, cascade={"persist"})
     *
     * @ListingAnnotation(idListingUuid="0cada1ad-1ee1-4d43-9808-e6e520401aa3")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @Groups({"abris"})
     */
    private $couchages;

    /**
     * @ORM\OneToMany(targetEntity="AttributesWithQty",mappedBy="abrisPlaceDeFeuInterieur", orphanRemoval=true, cascade={"persist"})
     *
     * @ListingAnnotation(idListingUuid="d2c557e2-9c22-4491-a5de-55b33ea4d03c")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @Groups({"abris"})
     */
    private $placeDeFeuInterieur;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"abris"})
     */
    private ?bool $placeDeFeuExterieure = null;

    /**
     * @ORM\OneToMany(targetEntity="AttributesWithQty",mappedBy="abrisMobilierPiqueniqueExterieur", orphanRemoval=true, cascade={"persist"})
     *
     * @ListingAnnotation(idListingUuid="451c7145-5e9d-49e3-a586-6f1bb312a37c")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @Groups({"abris"})
     */
    private $mobilierPiqueniqueExterieur;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"abris"})
     */
    private ?bool $emplacementInterieurReserveBois = null;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"abris"})
     */
    private ?bool $toilettesSeches = null;

    /**
     * @ORM\OneToMany(targetEntity="AttributesWithQty",mappedBy="abrisMaterielDivers", orphanRemoval=true, cascade={"persist"})
     *
     * @ListingAnnotation(idListingUuid="2d007014-64fa-4e70-ae3e-f0119c2063d1")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @Groups({"abris"})
     */
    private $materielDivers;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"abris"})
     */
    private ?bool $source = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Groups({"abris"})
     */
    private ?string $nomSource = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @Groups({"abris"})
     */
    private ?string $coordinateSource = null;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"abris"})
     */
    private ?bool $eauCourante = null;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"abris"})
     */
    private ?bool $cahierSuiviEtCrayon = null;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"abris"})
     */
    private ?bool $plaqueAbris = null;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"abris"})
     */
    private ?bool $panneauInfosBonnesPratiques = null;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"abris"})
     */
    private ?bool $signaletiqueSourceProche = null;

    /**
     * @ORM\Column(type="integer")
     *
     * @Groups({"abris"})
     */
    private ?int $capaciteCouchage = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Groups({"abris"})
     */
    private ?int $nbAncrageSol = null;

    /**
     * @ORM\ManyToOne(targetEntity="ListingValue")
     *
     * @ListingAnnotation(idListingUuid="716dfe77-bc73-40ec-aba8-85daed10e9af")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @Groups({"abris"})
     */
    private ?ListingValue $typeAncrageSol = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @Groups({"abris"})
     */
    private ?string $remarqueStructureBat = null;

    /**
     * @ORM\OneToMany(targetEntity="UploadedDocument", mappedBy="abris")
     *
     * @Groups({"abris"})
     */
    private $photos;

    /**
     * @ORM\OneToMany(targetEntity="Dysfonctionnement", mappedBy="abris", orphanRemoval=true)
     *
     * @Groups({"abris"})
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @ORM\OrderBy({"updated" = "DESC"})
     */
    private $dysfonctionnements;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="abrisFavoris", cascade={"persist"})
     *
     * @ORM\JoinTable(name="abris_followers")
     */
    private $followers;

    /**
     * @ORM\OneToMany(targetEntity="Discussion", mappedBy="abris")
     *
     * @ORM\JoinColumn(onDelete="SET NULL")
     *
     * @ORM\OrderBy({"updated" = "DESC"})
     */
    private $discussions;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"abris"})
     */
    private ?string $altitude = null;

    public function __construct()
    {
        $this->proprietaires = new ArrayCollection();
        $this->gestionnaires = new ArrayCollection();
        $this->toit = new ArrayCollection();
        $this->typeAccesEtage = new ArrayCollection();
        $this->typeSol = new ArrayCollection();
        $this->mobiliers = new ArrayCollection();
        $this->couchages = new ArrayCollection();
        $this->placeDeFeuInterieur = new ArrayCollection();
        $this->mobilierPiqueniqueExterieur = new ArrayCollection();
        $this->materielDivers = new ArrayCollection();
        $this->accesEtage = new ArrayCollection();
        $this->photos = new ArrayCollection();
        $this->dysfonctionnements = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->discussions = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     *
     * @throws Exception;
     */
    public function onPrePersist(): void
    {
        $this->id = Uuid::uuid4();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate(): void
    {
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCoordinate(): ?string
    {
        return $this->coordinate;
    }

    public function setCoordinate(string $coordinate): self
    {
        $this->coordinate = $coordinate;

        return $this;
    }

    public function getCapaciteAccueil(): ?int
    {
        return $this->capaciteAccueil;
    }

    public function setCapaciteAccueil(int $capaciteAccueil): self
    {
        $this->capaciteAccueil = $capaciteAccueil;

        return $this;
    }

    public function getDescription(): ?string
    {
        if ($this->description) {
            return $this->description;
        } else {
            return ' ';
        }
    }

    public function setDescription($description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getChemineeEnPierreSurLeToit(): ?bool
    {
        return $this->chemineeEnPierreSurLeToit;
    }

    public function setChemineeEnPierreSurLeToit(bool $chemineeEnPierreSurLeToit): self
    {
        $this->chemineeEnPierreSurLeToit = $chemineeEnPierreSurLeToit;

        return $this;
    }

    public function getNbPortes(): ?int
    {
        return $this->nbPortes;
    }

    public function setNbPortes(int $nbPortes): self
    {
        $this->nbPortes = $nbPortes;

        return $this;
    }

    public function getNbFenetres(): ?int
    {
        return $this->nbFenetres;
    }

    public function setNbFenetres(int $nbFenetres): self
    {
        $this->nbFenetres = $nbFenetres;

        return $this;
    }

    public function getEtage(): ?bool
    {
        return $this->etage;
    }

    public function setEtage(bool $etage): self
    {
        $this->etage = $etage;

        return $this;
    }

    public function getCiterneExterieure(): ?bool
    {
        return $this->citerneExterieure;
    }

    public function setCiterneExterieure(bool $citerneExterieure): self
    {
        $this->citerneExterieure = $citerneExterieure;

        return $this;
    }

    public function getAppentisExterieur(): ?bool
    {
        return $this->appentisExterieur;
    }

    public function setAppentisExterieur(bool $appentisExterieur): self
    {
        $this->appentisExterieur = $appentisExterieur;

        return $this;
    }

    public function getPlaceDeFeuExterieure(): ?bool
    {
        return $this->placeDeFeuExterieure;
    }

    public function setPlaceDeFeuExterieure(bool $placeDeFeuExterieure): self
    {
        $this->placeDeFeuExterieure = $placeDeFeuExterieure;

        return $this;
    }

    public function getEmplacementInterieurReserveBois(): ?bool
    {
        return $this->emplacementInterieurReserveBois;
    }

    public function setEmplacementInterieurReserveBois(bool $emplacementInterieurReserveBois): self
    {
        $this->emplacementInterieurReserveBois = $emplacementInterieurReserveBois;

        return $this;
    }

    public function getToilettesSeches(): ?bool
    {
        return $this->toilettesSeches;
    }

    public function setToilettesSeches(bool $toilettesSeches): self
    {
        $this->toilettesSeches = $toilettesSeches;

        return $this;
    }

    public function getSource(): ?bool
    {
        return $this->source;
    }

    public function setSource(bool $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getNomSource(): ?string
    {
        return $this->nomSource;
    }

    public function setNomSource(?string $nomSource): self
    {
        $this->nomSource = $nomSource;

        return $this;
    }

    public function getCoordinateSource(): ?string
    {
        return $this->coordinateSource;
    }

    public function setCoordinateSource(?string $coordinateSource): self
    {
        $this->coordinateSource = $coordinateSource;

        return $this;
    }

    public function getEauCourante(): ?bool
    {
        return $this->eauCourante;
    }

    public function setEauCourante(bool $eauCourante): self
    {
        $this->eauCourante = $eauCourante;

        return $this;
    }

    public function getCahierSuiviEtCrayon(): ?bool
    {
        return $this->cahierSuiviEtCrayon;
    }

    public function setCahierSuiviEtCrayon(bool $cahierSuiviEtCrayon): self
    {
        $this->cahierSuiviEtCrayon = $cahierSuiviEtCrayon;

        return $this;
    }

    public function getPlaqueAbris(): ?bool
    {
        return $this->plaqueAbris;
    }

    public function setPlaqueAbris(bool $plaqueAbris): self
    {
        $this->plaqueAbris = $plaqueAbris;

        return $this;
    }

    public function getPanneauInfosBonnesPratiques(): ?bool
    {
        return $this->panneauInfosBonnesPratiques;
    }

    public function setPanneauInfosBonnesPratiques(bool $panneauInfosBonnesPratiques): self
    {
        $this->panneauInfosBonnesPratiques = $panneauInfosBonnesPratiques;

        return $this;
    }

    public function getSignaletiqueSourceProche(): ?bool
    {
        return $this->signaletiqueSourceProche;
    }

    public function setSignaletiqueSourceProche(bool $signaletiqueSourceProche): self
    {
        $this->signaletiqueSourceProche = $signaletiqueSourceProche;

        return $this;
    }

    public function getType(): ?ListingValue
    {
        return $this->type;
    }

    public function setType(?ListingValue $type): self
    {
        $this->type = $type;

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

    /**
     * @return Collection|User[]
     */
    public function getProprietaires(): Collection
    {
        return $this->proprietaires;
    }

    public function addProprietaire(User $proprietaire): self
    {
        if (!$this->proprietaires->contains($proprietaire)) {
            $this->proprietaires[] = $proprietaire;
        }

        return $this;
    }

    public function removeProprietaire(User $proprietaire): self
    {
        if ($this->proprietaires->contains($proprietaire)) {
            $this->proprietaires->removeElement($proprietaire);
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getGestionnaires(): Collection
    {
        return $this->gestionnaires;
    }

    public function addGestionnaire(User $gestionnaire): self
    {
        if (!$this->gestionnaires->contains($gestionnaire)) {
            $this->gestionnaires[] = $gestionnaire;
        }

        return $this;
    }

    public function removeGestionnaire(User $gestionnaire): self
    {
        if ($this->gestionnaires->contains($gestionnaire)) {
            $this->gestionnaires->removeElement($gestionnaire);
        }

        return $this;
    }

    public function getSortieFumees(): ?ListingValue
    {
        return $this->sortieFumees;
    }

    public function setSortieFumees(?ListingValue $sortieFumees): self
    {
        $this->sortieFumees = $sortieFumees;

        return $this;
    }

    public function getMateriauSortieFumees(): ?ListingValue
    {
        return $this->materiauSortieFumees;
    }

    public function setMateriauSortieFumees(?ListingValue $materiauSortieFumees): self
    {
        $this->materiauSortieFumees = $materiauSortieFumees;

        return $this;
    }

    public function getTypeMur(): ?ListingValue
    {
        return $this->typeMur;
    }

    public function setTypeMur(?ListingValue $typeMur): self
    {
        $this->typeMur = $typeMur;

        return $this;
    }

    /**
     * @return Collection|ListingValue[]
     */
    public function getAccesEtage(): Collection
    {
        return $this->accesEtage;
    }

    public function addAccesEtage(ListingValue $accesEtage): self
    {
        if (!$this->accesEtage->contains($accesEtage)) {
            $this->accesEtage[] = $accesEtage;
        }

        return $this;
    }

    public function removeAccesEtage(ListingValue $accesEtage): self
    {
        if ($this->accesEtage->contains($accesEtage)) {
            $this->accesEtage->removeElement($accesEtage);
        }

        return $this;
    }

    /**
     * @return Collection|ListingValue[]
     */
    public function getTypeAccesEtage(): Collection
    {
        return $this->typeAccesEtage;
    }

    public function addTypeAccesEtage(ListingValue $typeAccesEtage): self
    {
        if (!$this->typeAccesEtage->contains($typeAccesEtage)) {
            $this->typeAccesEtage[] = $typeAccesEtage;
        }

        return $this;
    }

    public function removeTypeAccesEtage(ListingValue $typeAccesEtage): self
    {
        if ($this->typeAccesEtage->contains($typeAccesEtage)) {
            $this->typeAccesEtage->removeElement($typeAccesEtage);
        }

        return $this;
    }

    /**
     * @return Collection|ListingValue[]
     */
    public function getTypeSol(): Collection
    {
        return $this->typeSol;
    }

    public function addTypeSol(ListingValue $typeSol): self
    {
        if (!$this->typeSol->contains($typeSol)) {
            $this->typeSol[] = $typeSol;
        }

        return $this;
    }

    public function removeTypeSol(ListingValue $typeSol): self
    {
        if ($this->typeSol->contains($typeSol)) {
            $this->typeSol->removeElement($typeSol);
        }

        return $this;
    }

    /**
     * @return Collection|AttributesWithQty[]
     */
    public function getMobiliers(): Collection
    {
        return $this->mobiliers;
    }

    public function addMobilier(AttributesWithQty $mobilier): self
    {
        if (!$this->mobiliers->contains($mobilier)) {
            $this->mobiliers[] = $mobilier;
            $mobilier->setAbrisMobilier($this);
        }

        return $this;
    }

    public function removeMobilier(AttributesWithQty $mobilier): self
    {
        if ($this->mobiliers->contains($mobilier)) {
            $this->mobiliers->removeElement($mobilier);
            // set the owning side to null (unless already changed)
            if ($mobilier->getAbrisMobilier() === $this) {
                $mobilier->setAbrisMobilier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AttributesWithQty[]
     */
    public function getCouchages(): Collection
    {
        return $this->couchages;
    }

    public function addCouchage(AttributesWithQty $couchage): self
    {
        if (!$this->couchages->contains($couchage)) {
            $this->couchages[] = $couchage;
            $couchage->setAbrisCouchage($this);
        }

        return $this;
    }

    public function removeCouchage(AttributesWithQty $couchage): self
    {
        if ($this->couchages->contains($couchage)) {
            $this->couchages->removeElement($couchage);
            // set the owning side to null (unless already changed)
            if ($couchage->getAbrisCouchage() === $this) {
                $couchage->setAbrisCouchage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AttributesWithQty[]
     */
    public function getPlaceDeFeuInterieur(): Collection
    {
        return $this->placeDeFeuInterieur;
    }

    public function addPlaceDeFeuInterieur(AttributesWithQty $placeDeFeuInterieur): self
    {
        if (!$this->placeDeFeuInterieur->contains($placeDeFeuInterieur)) {
            $this->placeDeFeuInterieur[] = $placeDeFeuInterieur;
            $placeDeFeuInterieur->setAbrisPlaceDeFeuInterieur($this);
        }

        return $this;
    }

    public function removePlaceDeFeuInterieur(AttributesWithQty $placeDeFeuInterieur): self
    {
        if ($this->placeDeFeuInterieur->contains($placeDeFeuInterieur)) {
            $this->placeDeFeuInterieur->removeElement($placeDeFeuInterieur);
            // set the owning side to null (unless already changed)
            if ($placeDeFeuInterieur->getAbrisPlaceDeFeuInterieur() === $this) {
                $placeDeFeuInterieur->setAbrisPlaceDeFeuInterieur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AttributesWithQty[]
     */
    public function getMobilierPiqueniqueExterieur(): Collection
    {
        return $this->mobilierPiqueniqueExterieur;
    }

    public function addMobilierPiqueniqueExterieur(AttributesWithQty $mobilierPiqueniqueExterieur): self
    {
        if (!$this->mobilierPiqueniqueExterieur->contains($mobilierPiqueniqueExterieur)) {
            $this->mobilierPiqueniqueExterieur[] = $mobilierPiqueniqueExterieur;
            $mobilierPiqueniqueExterieur->setAbrisMobilierPiqueniqueExterieur($this);
        }

        return $this;
    }

    public function removeMobilierPiqueniqueExterieur(AttributesWithQty $mobilierPiqueniqueExterieur): self
    {
        if ($this->mobilierPiqueniqueExterieur->contains($mobilierPiqueniqueExterieur)) {
            $this->mobilierPiqueniqueExterieur->removeElement($mobilierPiqueniqueExterieur);
            // set the owning side to null (unless already changed)
            if ($mobilierPiqueniqueExterieur->getAbrisMobilierPiqueniqueExterieur() === $this) {
                $mobilierPiqueniqueExterieur->setAbrisMobilierPiqueniqueExterieur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AttributesWithQty[]
     */
    public function getMaterielDivers(): Collection
    {
        return $this->materielDivers;
    }

    public function addMaterielDiver(AttributesWithQty $materielDiver): self
    {
        if (!$this->materielDivers->contains($materielDiver)) {
            $this->materielDivers[] = $materielDiver;
            $materielDiver->setAbrisMaterielDivers($this);
        }

        return $this;
    }

    public function removeMaterielDiver(AttributesWithQty $materielDiver): self
    {
        if ($this->materielDivers->contains($materielDiver)) {
            $this->materielDivers->removeElement($materielDiver);
            // set the owning side to null (unless already changed)
            if ($materielDiver->getAbrisMaterielDivers() === $this) {
                $materielDiver->setAbrisMaterielDivers(null);
            }
        }

        return $this;
    }

    public function getCapaciteCouchage(): ?int
    {
        return $this->capaciteCouchage;
    }

    public function setCapaciteCouchage(int $capaciteCouchage): self
    {
        $this->capaciteCouchage = $capaciteCouchage;

        return $this;
    }

    public function getNbAncrageSol(): ?int
    {
        return $this->nbAncrageSol;
    }

    public function setNbAncrageSol(?int $nbAncrageSol): self
    {
        $this->nbAncrageSol = $nbAncrageSol;

        return $this;
    }

    public function getTypeAncrageSol(): ?ListingValue
    {
        return $this->typeAncrageSol;
    }

    public function setTypeAncrageSol(?ListingValue $typeAncrageSol): self
    {
        $this->typeAncrageSol = $typeAncrageSol;

        return $this;
    }

    public function getRemarqueStructureBat(): ?string
    {
        return $this->remarqueStructureBat;
    }

    public function setRemarqueStructureBat(?string $remarqueStructureBat): self
    {
        $this->remarqueStructureBat = $remarqueStructureBat;

        return $this;
    }

    /**
     * @return Collection|ListingValue[]
     */
    public function getToit(): Collection
    {
        return $this->toit;
    }

    public function addToit(ListingValue $toit): self
    {
        if (!$this->toit->contains($toit)) {
            $this->toit[] = $toit;
        }

        return $this;
    }

    public function removeToit(ListingValue $toit): self
    {
        if ($this->toit->contains($toit)) {
            $this->toit->removeElement($toit);
        }

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
            $photo->setAbris($this);
        }

        return $this;
    }

    public function removePhoto(UploadedDocument $photo): self
    {
        if ($this->photos->contains($photo)) {
            $this->photos->removeElement($photo);
            // set the owning side to null (unless already changed)
            if ($photo->getAbris() === $this) {
                $photo->setAbris(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Dysfonctionnement[]
     */
    public function getDysfonctionnements(): Collection
    {
        return $this->dysfonctionnements;
    }

    public function addDysfonctionnement(Dysfonctionnement $dysfonctionnement): self
    {
        if (!$this->dysfonctionnements->contains($dysfonctionnement)) {
            $this->dysfonctionnements[] = $dysfonctionnement;
            $dysfonctionnement->setAbris($this);
        }

        return $this;
    }

    public function removeDysfonctionnement(Dysfonctionnement $dysfonctionnement): self
    {
        if ($this->dysfonctionnements->contains($dysfonctionnement)) {
            $this->dysfonctionnements->removeElement($dysfonctionnement);
            // set the owning side to null (unless already changed)
            if ($dysfonctionnement->getAbris() === $this) {
                $dysfonctionnement->setAbris(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(User $follower): self
    {
        if (!$this->followers->contains($follower)) {
            $this->followers[] = $follower;
        }

        return $this;
    }

    public function removeFollower(User $follower): self
    {
        if ($this->followers->contains($follower)) {
            $this->followers->removeElement($follower);
        }

        return $this;
    }

    /**
     * @return Collection|Discussion[]
     */
    public function getDiscussions(): Collection
    {
        return $this->discussions;
    }

    public function addDiscussion(Discussion $discussion): self
    {
        if (!$this->discussions->contains($discussion)) {
            $this->discussions[] = $discussion;
            $discussion->setAbris($this);
        }

        return $this;
    }

    public function removeDiscussion(Discussion $discussion): self
    {
        if ($this->discussions->contains($discussion)) {
            $this->discussions->removeElement($discussion);
            // set the owning side to null (unless already changed)
            if ($discussion->getAbris() === $this) {
                $discussion->setAbris(null);
            }
        }

        return $this;
    }

    public function getAltitude(): ?string
    {
        return $this->altitude;
    }

    public function setAltitude(string $altitude): self
    {
        $this->altitude = $altitude;

        return $this;
    }
}
