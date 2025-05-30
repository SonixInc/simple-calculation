<?php declare(strict_types=1);

namespace App\Tests\Unit;


use App\Entity\Product;
use App\Entity\Promotion;
use App\Entity\Tax;
use App\Enum\PromotionType;
use App\Repository\ProductRepository;
use App\Repository\PromotionRepository;
use App\Repository\TaxRepository;
use App\Service\PriceService;
use DomainException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * Class PriceServiceTest
 * @package App\Tests\Unit
 */
class PriceServiceTest extends TestCase
{
    private MockObject $productRepository;
    private MockObject $taxRepository;
    private MockObject $promotionRepository;
    private PriceService $priceService;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->taxRepository = $this->createMock(TaxRepository::class);
        $this->promotionRepository = $this->createMock(PromotionRepository::class);

        $this->priceService = new PriceService(
            $this->productRepository,
            $this->taxRepository,
            $this->promotionRepository
        );
    }

    public function testCalculatePriceSuccess(): void
    {
        $productMock = $this->createMock(Product::class);
        $productMock->method('getPrice')->willReturn(100.0);

        $taxMock = $this->createMock(Tax::class);
        $taxMock->method('getRate')->willReturn(19.0);

        $this->productRepository->method('findById')->willReturn($productMock);
        $this->taxRepository->method('findByCountryCode')->willReturn($taxMock);
        $this->promotionRepository->method('findByCode')->willReturn(null);

        $price = $this->priceService->calculatePrice(Uuid::uuid4()->toString(), 'DE123456789');
        $this->assertEquals(119.0, $price);
    }

    public function testCalculatePriceWithFixedDiscount()
    {
        $productMock = $this->createMock(Product::class);
        $productMock->method('getPrice')->willReturn(100.0);

        $taxMock = $this->createMock(Tax::class);
        $taxMock->method('getRate')->willReturn(19.0);

        $promotionMock = $this->createMock(Promotion::class);
        $promotionMock->method('getDiscount')->willReturn(10.0);
        $promotionMock->method('getType')->willReturn(PromotionType::Fixed);

        $this->productRepository->method('findById')->willReturn($productMock);
        $this->taxRepository->method('findByCountryCode')->willReturn($taxMock);
        $this->promotionRepository->method('findByCode')->willReturn($promotionMock);

        $price = $this->priceService->calculatePrice(Uuid::uuid4()->toString(), 'DE123456789', 'F10');
        $this->assertEquals(109.0, $price);
    }

    public function testCalculatePriceWithPercentageDiscount()
    {
        $productMock = $this->createMock(Product::class);
        $productMock->method('getPrice')->willReturn(100.0);

        $taxMock = $this->createMock(Tax::class);
        $taxMock->method('getRate')->willReturn(19.0);

        $promotionMock = $this->createMock(Promotion::class);
        $promotionMock->method('getDiscount')->willReturn(50.0);
        $promotionMock->method('getType')->willReturn(PromotionType::Percentage);

        $this->productRepository->method('findById')->willReturn($productMock);
        $this->taxRepository->method('findByCountryCode')->willReturn($taxMock);
        $this->promotionRepository->method('findByCode')->willReturn($promotionMock);

        $price = $this->priceService->calculatePrice(Uuid::uuid4()->toString(), 'DE123456789', 'F10');
        $this->assertEquals(69.0, $price);
    }

    public function testCalculatePriceProductNotFound()
    {
        $this->productRepository->method('findById')
            ->willReturn(null);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Product with id 123 is not found');

        $this->priceService->calculatePrice('123', 'DE123456789');
    }

    public function testCalculatePriceTaxNotFound()
    {
        $productMock = $this->createMock(Product::class);
        $productMock->method('getPrice')->willReturn(100.0);

        $this->productRepository->method('findById')
            ->willReturn($productMock);

        $this->taxRepository->method('findByCountryCode')
            ->willReturn(null);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Tax with country code DE is not found');

        $this->priceService->calculatePrice('123', 'DE123456789');
    }
}
