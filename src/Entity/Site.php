<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SiteRepository::class)
 */
class Site
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="site", orphanRemoval=true)
     */
    private $Sortie;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="site", orphanRemoval=true)
     */
    private $Utilisateur;

    public function __construct()
    {
        $this->Sortie = new ArrayCollection();
        $this->Utilisateur = new ArrayCollection();
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
            $sortie->setSite($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): self
    {
        if ($this->Sortie->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getSite() === $this) {
                $sortie->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUtilisateur(): Collection
    {
        return $this->Utilisateur;
    }

    public function addUtilisateur(User $utilisateur): self
    {
        if (!$this->Utilisateur->contains($utilisateur)) {
            $this->Utilisateur[] = $utilisateur;
            $utilisateur->setSite($this);
        }

        return $this;
    }

    public function removeUtilisateur(User $utilisateur): self
    {
        if ($this->Utilisateur->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getSite() === $this) {
                $utilisateur->setSite(null);
            }
        }

        return $this;
    }
}
