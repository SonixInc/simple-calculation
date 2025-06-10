<?php declare(strict_types=1);

namespace App\Service;


use App\Entity\Order;
use App\Enum\OrderState;
use App\Payment\PaymentProcessorInterface;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;

/**
 * Class PurchaseService
 * @package App\Service
 */
readonly class PurchaseService
{
    /**
     * @param iterable|PaymentProcessorInterface[] $paymentAdapters
     * @param ProductRepository $productRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private iterable $paymentAdapters,
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * Makes product purchase for provided payment method
     *
     * @param string $productId
     * @param string $paymentMethod
     * @param float $price
     * @return Order
     */
    public function makePurchase(
        string $productId,
        string $paymentMethod,
        float $price
    ): Order
    {
        if ($price < 0) {
            throw new DomainException('Cannot create an order with negative price');
        }

        if (!$product = $this->productRepository->findById($productId)) {
            throw new DomainException(sprintf('Product with id %s is not found', $productId));
        }

        $order = new Order();
        $order->setState(OrderState::Open);
        $order->setProduct($product);
        $order->setPrice($price);
        $order->setPaymentMethod($paymentMethod);

        foreach ($this->paymentAdapters as $paymentAdapter) {
            if (!$paymentAdapter->supports($paymentMethod)) {
                continue;
            }

            $paymentAdapter->pay($order->getPrice());
            $this->entityManager->persist($order);
            $this->entityManager->flush();

            return $order;
        }

        throw new DomainException(sprintf('Payment method %s is not supported', $paymentMethod));
    }
}
