<?php

namespace App\Entity;

use App\Repository\MatiereRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MatiereRepository::class)]
class Matiere
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $nom;

    #[ORM\Column(type: 'float')]
    private $coef;

    #[ORM\OneToMany(mappedBy: 'matiere', targetEntity: Note::class, orphanRemoval: true)]
    private $notes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
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

    public function getCoef(): ?float
    {
        return $this->coef;
    }

    public function setCoef(float $coef): self
    {
        $this->coef = $coef;

        return $this;
    }  /**
    * @return Collection|Note[]
    */
   public function getNotes(): Collection
   {
       return $this->notes;
   }

   public function addNote(Note $note): self
   {
       if (!$this->notes->contains($note)) {
           $this->notes[] = $note;
           $note->setMatiere($this);
       }

       return $this;
   }

   public function removeNote(Note $note): self
   {
       if ($this->notes->removeElement($note)) {
           // set the owning side to null (unless already changed)
           if ($note->getMatiere() === $this) {
               $note->setMatiere(null);
           }
       }

       return $this;
   }

    public function __toString()
    {
        return $this->nom . " - coeff : " . $this->coef;
    }
}
