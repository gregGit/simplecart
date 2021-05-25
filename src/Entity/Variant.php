<?php

namespace App\Entity;

use App\Repository\VariantRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VariantRepository::class)
 */
class Variant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\Column(type="json")
     */
    private $tailles = [];

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="variants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $produit;

    /**
     * @ORM\ManyToOne(targetEntity=Couleur::class, inversedBy="variants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $couleur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getTailles(): ?array
    {
        return $this->tailles;
    }

    public function setTailles(array $tailles): self
    {
        $this->tailles = $tailles;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getImageRaw64()
    {
        if($this->image==null || get_resource_type($this->image) !='stream'){
            return null;
        }
        rewind($this->image);
        return base64_encode(stream_get_contents($this->image));
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getCouleur(): ?Couleur
    {
        return $this->couleur;
    }

    public function setCouleur(?Couleur $couleur): self
    {
        $this->couleur = $couleur;

        return $this;
    }

    /**
     * Retourne le stock du variant dans la taille $scTaille
     * Si la taille n'existe pas la fonction retourne false (donc à différencier de 0)
     *
     * @param $scTaille
     * @return false|mixed
     */
    public function getStock($scTaille){
        foreach($this->getTailles() as $taille=>$stock){
            if($taille==$scTaille){
                return $stock;
            }
        }
        return false;
    }
}
