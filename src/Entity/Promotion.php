<?php declare(strict_types=1);

namespace App\Entity;


use App\Enum\PromotionType;
use App\Repository\PromotionRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Promotion
 * @package App\Entity
 */
#[ORM\Entity(repositoryClass: PromotionRepository::class)]
#[ORM\Table(name: 'promotions')]
#[Orm\HasLifecycleCallbacks]
class Promotion
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(type: Types::STRING)]
    private string $name;

    #[ORM\Column(type: Types::STRING, unique: true)]
    private string $code;

    #[ORM\Column(type: Types::STRING)]
    private string $type;

    #[ORM\Column(type: Types::FLOAT)]
    private float $discount;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getType(): PromotionType
    {
        return PromotionType::from($this->type);
    }

    public function setType(PromotionType $type): void
    {
        $this->type = $type->value;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function setDiscount(float $discount): void
    {
        $this->discount = $discount;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function onPrePersistCreatedAt(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PreUpdate]
    public function onPreUpdateUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
