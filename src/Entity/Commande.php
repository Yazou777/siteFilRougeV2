<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $com_date = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $com_commentaire = null;

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $com_uti = null;

    #[ORM\OneToMany(mappedBy: 'pan_com', targetEntity: Panier::class)]
    private Collection $paniers;

    #[ORM\OneToMany(mappedBy: 'bon_com', targetEntity: BonLivraison::class)]
    private Collection $bonLivraisons;

    public function __construct()
    {
        $this->com_date = new \DateTimeImmutable();
        $this->paniers = new ArrayCollection();
        $this->bonLivraisons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComDate(): ?\DateTimeImmutable
    {
        return $this->com_date;
    }

    // public function setComDate(\DateTimeImmutable $com_date): static
    // {
    //     $this->com_date = $com_date;

    //     return $this;
    // }
    
    #[ORM\PrePersist]
    public function setComDate(): static
    {
        $this->com_date =  new \DateTimeImmutable();

        return $this;
    }

    public function getComCommentaire(): ?string
    {
        return $this->com_commentaire;
    }

    public function setComCommentaire(?string $com_commentaire): static
    {
        $this->com_commentaire = $com_commentaire;

        return $this;
    }

    public function getComUti(): ?Utilisateur
    {
        return $this->com_uti;
    }

    public function setComUti(?Utilisateur $com_uti): static
    {
        $this->com_uti = $com_uti;

        return $this;
    }

    /**
     * @return Collection<int, Panier>
     */
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function addPanier(Panier $panier): static
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers->add($panier);
            $panier->setPanCom($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): static
    {
        if ($this->paniers->removeElement($panier)) {
            // set the owning side to null (unless already changed)
            if ($panier->getPanCom() === $this) {
                $panier->setPanCom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, BonLivraison>
     */
    public function getBonLivraisons(): Collection
    {
        return $this->bonLivraisons;
    }

    public function addBonLivraison(BonLivraison $bonLivraison): static
    {
        if (!$this->bonLivraisons->contains($bonLivraison)) {
            $this->bonLivraisons->add($bonLivraison);
            $bonLivraison->setBonCom($this);
        }

        return $this;
    }

    public function removeBonLivraison(BonLivraison $bonLivraison): static
    {
        if ($this->bonLivraisons->removeElement($bonLivraison)) {
            // set the owning side to null (unless already changed)
            if ($bonLivraison->getBonCom() === $this) {
                $bonLivraison->setBonCom(null);
            }
        }

        return $this;
    }

}
