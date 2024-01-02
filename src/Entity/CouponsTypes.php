<?php

namespace App\Entity;

use App\Repository\CouponsTypesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponsTypesRepository::class)]
class CouponsTypes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'coupons_types', targetEntity: Coupons::class, orphanRemoval: true)]
    private Collection $coupons;

    public function __construct()
    {
        $this->coupons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Coupons>
     */
    public function getCoupons(): Collection
    {
        return $this->coupons;
    }

    public function addCoupons(Coupons $coupons): static
    {
        if (!$this->coupons->contains($coupons)) {
            $this->coupons->add($coupons);
            $coupons->setCouponsTypes($this);
        }

        return $this;
    }

    public function removeCoupons(Coupons $coupons): static
    {
        if ($this->coupons->removeElement($coupons)) {
            // set the owning side to null (unless already changed)
            if ($coupons->getCouponsTypes() === $this) {
                $coupons->setCouponsTypes(null);
            }
        }

        return $this;
    }
}
