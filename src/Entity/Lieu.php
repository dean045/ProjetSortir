<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=LieuRepository::class)
 */
class Lieu
{
    /**
     * @Groups({"liste"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"liste"})
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @Groups({"liste"})
     * @ORM\Column(type="string", length=100)
     */
    private $rue;

    /**
     * @Groups({"liste"})
     * @ORM\Column(type="float")
     */
    private $latitute;

    /**
     * @Groups({"liste"})
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @Groups({"liste"})
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="Lieu")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ville;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="lieu")
     */
    private $Sortie;

    public function __construct()
    {
        $this->Sortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitute(): ?float
    {
        return $this->latitute;
    }

    public function setLatitute(float $latitute): self
    {
        $this->latitute = $latitute;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSortie(): Collection
    {
        return $this->Sortie;
    }

    public function addSortie(Sortie $sortie): self
    {
        if (!$this->Sortie->contains($sortie)) {
            $this->Sortie[] = $sortie;
            $sortie->setLieu($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): self
    {
        if ($this->Sortie->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getLieu() === $this) {
                $sortie->setLieu(null);
            }
        }

        return $this;
    }

    /**
     * Transform to string
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->getId();
    }

}
