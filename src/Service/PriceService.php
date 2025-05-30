<?php declare(strict_types=1);

namespace App\Service;


use App\Entity\Promotion;
use App\Enum\PromotionType;
use App\Repository\ProductRepository;
use App\Repository\PromotionRepository;
use App\Repository\TaxRepository;

/**
 * Class PriceService
 * @package App\Service
 */
readonly class PriceService
{
    public function __construct(
        private ProductRepository $productRepository,
        private TaxRepository $taxRepository,
        private PromotionRepository $promotionRepository
    )
    {
    }

    /**
     * Calculates price for provided data
     *
     * @param string $productId
     * @param string $taxNumber
     * @param string|null $couponCode
     * @return float
     */
    public function calculatePrice(
        string $productId,
        string $taxNumber,
        ?string $couponCode = null
    ): float
    {
        if (!$product = $this->productRepository->findById($productId)) {
            throw new \DomainException(sprintf('Product with id %s is not found', $productId));
        }

        $countryCode = strtoupper(substr($taxNumber, 0, 2));
        if (!$tax = $this->taxRepository->findByCountryCode($countryCode)) {
            throw new \DomainException(sprintf('Tax with country code %s is not found',$countryCode));
        }

        $productPrice = $product->getPrice();
        $taxPrice = $this->calculateTaxPrice($productPrice, $tax->getRate());
        if (!$couponCode || !$promotion = $this->promotionRepository->findByCode($couponCode)) {
            return $productPrice + $taxPrice;
        }

        $discount = $this->calculatePromotionDiscount($productPrice, $promotion);

        return $productPrice + $taxPrice - $discount;
    }

    /**
     * Calculates tax rate
     *
     * @param float $productPrice
     * @param float $taxRate
     * @return float
     */
    private function calculateTaxPrice(float $productPrice, float $taxRate): float
    {
        return $taxRate * $productPrice / 100;
    }

    /**
     * Calculates promotion discount
     *
     * @param float $productPrice
     * @param Promotion $promotion
     * @return float
     */
    private function calculatePromotionDiscount(float $productPrice, Promotion $promotion): float
    {
        $discount = 0;
        match ($promotion->getType()) {
            PromotionType::Fixed => $discount = $promotion->getDiscount(),
            PromotionType::Percentage => $discount = $promotion->getDiscount() * $productPrice / 100
        };

        return $discount;
    }
}
