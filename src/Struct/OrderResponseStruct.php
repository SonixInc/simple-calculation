<?php declare(strict_types=1);

namespace App\Struct;


use App\Entity\Order;
use DateTimeImmutable;

/**
 * Class OrderResponseStruct
 * @package App\Struct
 */
class OrderResponseStruct
{
    private string $id;
    private string $state;
    private float $price;
    private DateTimeImmutable $createdAt;

    public function __construct(Order $order)
    {
        $this->id = $order->getId()->toString();
        $this->state = $order->getState()->value;
        $this->price = $order->getPrice();
        $this->createdAt = $order->getCreatedAt();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
