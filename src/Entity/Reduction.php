<?php

namespace App\Entity;

use App\Repository\ReductionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReductionRepository::class)]
class Reduction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("reduction:read")]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups("reduction:read")]
    #[Assert\NotBlank(message:"reduction name is required !")]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups("reduction:read")]
    #[Assert\NotBlank(message:"reduction val is required !")]
    private ?float $reduction_val = null;

    #[ORM\Column]
    #[Groups("reduction:read")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'reduction', targetEntity: Product::class)]
    private Collection $products;

    public function __construct()
    {
        $this->createdAt=new \DateTimeImmutable;
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getReductionVal(): ?float
    {
        return $this->reduction_val;
    }

    public function setReductionVal(float $reduction_val): static
    {
        $this->reduction_val = $reduction_val;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setReduction($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getReduction() === $this) {
                $product->setReduction(null);
            }
        }

        return $this;
    }
}
