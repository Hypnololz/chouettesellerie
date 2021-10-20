<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message=" veuillez remplir tous les champ !")
     * @Assert\Length(
     *     min=2,
     *     max=255,
     *     minMessage="le nom doit faire au minimum {{ limit }} caractere",
     *     maxMessage="le nom doit faire au maximum {{ limit }} caractere")
     *
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message=" veuillez remplir tous les champ !")
     * @Assert\Length(
     *     min=2,
     *     max=5000,
     *     minMessage="la description doit faire au minimum {{ limit }} caractere",
     *     maxMessage="la description doit faire au maximum {{ limit }} caractere")
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message=" veuillez remplir tous les champ !")
     * @Assert\GreaterThanOrEqual(0)
     */
    private $price;

    /**
     * @Assert\NotBlank(message=" veuillez remplir tous les champ !")
     * @ORM\Column(type="integer")
     * @Assert\GreaterThanOrEqual(0)
     */
    private $stock;

    /**
     * @Assert\NotBlank(message=" veuillez remplir tous les champ !")
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(
     *     min=2,
     *     max=50,
     *     minMessage="le nom de la marque doit faire au minimum {{ limit }} caractere",
     *     maxMessage="le nom de la marque doit faire au maximum {{ limit }} caractere")
     */
    private $brand;


    /**
     * @Assert\NotBlank(message=" veuillez remplir tous les champ !")
     * @ORM\Column(type="string", length=255)
     */
    private $reference;

    /**
     *
     * @ORM\Column(type="string", length=255, unique=true)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable="true")
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Gammes::class, inversedBy="product")
     * @ORM\JoinColumn(nullable=false)
     */
    private $gammes;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $Photo;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }


    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist()
     *
     */
    public function setCreatedAt(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function setUpdatedAt(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getGammes(): ?Gammes
    {
        return $this->gammes;
    }

    public function setGammes(?Gammes $gammes): self
    {
        $this->gammes = $gammes;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->Photo;
    }

    public function setPhoto(string $Photo): self
    {
        $this->Photo = $Photo;

        return $this;
    }
}
