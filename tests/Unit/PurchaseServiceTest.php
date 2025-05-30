<?php declare(strict_types=1);

namespace App\Tests\Unit;


use App\Entity\Order;
use App\Entity\Product;
use App\Enum\OrderState;
use App\Payment\PaymentProcessorInterface;
use App\Repository\ProductRepository;
use App\Service\PurchaseService;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * Class PurchaseServiceTest
 * @package App\Tests\Unit
 */
class PurchaseServiceTest extends TestCase
{
    private MockObject $productRepository;
    private MockObject $entityManager;
    private PurchaseService $purchaseService;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $paymentAdapterSupported = $this->createMock(PaymentProcessorInterface::class);
        $paymentAdapterSupported->method('supports')
            ->willReturnCallback(fn($method) => $method === 'supported_method');

        $paymentAdapterUnsupported = $this->createMock(PaymentProcessorInterface::class);
        $paymentAdapterUnsupported->method('supports')
            ->willReturn(false);

        $paymentAdapters = [
            $paymentAdapterUnsupported,
            $paymentAdapterSupported,
        ];

        $this->purchaseService = new PurchaseService(
            $paymentAdapters,
            $this->productRepository,
            $this->entityManager
        );
    }

    public function testMakePurchaseSuccessful(): void
    {
        $productId = Uuid::uuid4()->toString();

        $productMock = $this->createMock(Product::class);
        $this->productRepository->method('findById')->with($productId)->willReturn($productMock);

        $price = 119.0;
        $paymentMethod = 'supported_method';

        $this->entityManager->expects($this->once())
            ->method('persist');

        $this->entityManager->expects($this->once())
            ->method('flush');

        $order = $this->purchaseService->makePurchase($productId, $paymentMethod, $price);

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(OrderState::Open, $order->getState());
        $this->assertEquals($price, $order->getPrice());
        $this->assertEquals($paymentMethod, $order->getPaymentMethod());
    }

    public function testMakePurchaseProductNotFound(): void
    {
        $this->productRepository->method('findById')->with('missing_product')->willReturn(null);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Product with id missing_product is not found');

        $this->purchaseService->makePurchase('missing_product', 'supported_method', 100);
    }

    public function testMakePurchasePaymentMethodNotSupported(): void
    {
        $productMock = $this->createMock(Product::class);
        $this->productRepository->method('findById')->with('product123')->willReturn($productMock);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Payment method unsupported_method is not supported');

        $this->purchaseService->makePurchase('product123', 'unsupported_method', 100);
    }
}
