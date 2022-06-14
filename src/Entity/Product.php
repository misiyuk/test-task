<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[UniqueEntity('sku')]
class Product
{
    public const GROUP_GET = 'product_get';
    public const GROUP_SET = 'product_set';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(self::GROUP_GET)]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([self::GROUP_GET, self::GROUP_SET])]
    #[Assert\NotNull]
    #[Assert\Length(max: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 10)]
    #[Groups([self::GROUP_GET, self::GROUP_SET])]
    #[Assert\NotNull]
    #[Assert\Regex(pattern: '/^\d+(\.\d+)?$/', message: 'Incorrect price')]
    #[Assert\Length(max: 10)]
    private $price;

    #[ORM\Column(type: 'string', length: 12, unique: true)]
    #[Groups([self::GROUP_GET, self::GROUP_SET])]
    #[Assert\NotNull]
    #[Assert\Regex(pattern: '/^\d{4}-\d{3}-\d{3}$/', message: 'Incorrect SKU number')]
    private $sku;

    #[ORM\Column(type: 'integer')]
    #[Groups([self::GROUP_GET, self::GROUP_SET])]
    #[Assert\NotNull]
    #[Assert\Range(min: 0, max: 2147483647)]
    private $quantity;

    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'products')]
    #[Groups([self::GROUP_GET, self::GROUP_SET])]
    private $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addProduct($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeProduct($this);
        }

        return $this;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): self
    {
        $this->sku = $sku;

        return $this;
    }
}
