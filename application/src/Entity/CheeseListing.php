<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CheeseListingRepository;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ApiResource(
 *      collectionOperations={"get", "post"},
 *      itemOperations={"get","put"},
 *      shortName="cheeses",
 *      normalizationContext={"groups"={"cheese_listing:read"}},
 *      denormalizationContext={"groups"={"cheese_listing:write"}}
 * )
 *
 * @ORM\Entity(repositoryClass=CheeseListingRepository::class)
 */
class CheeseListing
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups({"cheese_listing:read", "cheese_listing:write"})
     *
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @Groups({"cheese_listing:read"})
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * The price of this delicious cheese, in cents.
     *
     * @Groups({"cheese_listing:read", "cheese_listing:write"})
     *
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished;

    public function __construct(string $title)
    {
        $this->title = $title;
        $this->createdAt = new \DateTimeImmutable();
        $this->isPublished = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @Groups({"cheese_listing:write"})
     * @SerializedName("description")
     */
    public function setTextDescription(?string $textDescription): self
    {
        $this->description = nl2br($textDescription);

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * How long ago in text that this cheese listing was added.
     *
     * @Groups({"cheese_listing:read"})
     */
    public function getCreatedAtAgo(): ?string
    {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }
}