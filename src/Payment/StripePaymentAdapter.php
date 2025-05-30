<?php declare(strict_types=1);

namespace App\Payment;


use Psr\Log\LoggerInterface;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;

/**
 * Class StripePaymentAdapter
 * @package App\Payment
 */
class StripePaymentAdapter implements PaymentProcessorInterface
{
    public const string PAYMENT_METHOD = 'stripe';

    public function __construct(
        private readonly StripePaymentProcessor $paymentProcessor,
        private readonly LoggerInterface $logger
    )
    {
    }

    /**
     * @inheritDoc
     *
     * @param string $paymentMethod
     * @return bool
     */
    public function supports(string $paymentMethod): bool
    {
        return self::PAYMENT_METHOD === $paymentMethod;
    }

    /**
     * @inheritDoc
     *
     * @param float $price
     * @return void
     */
    public function pay(float $price): void
    {
        try {
            if (!$this->paymentProcessor->processPayment($price)) {
                throw new \DomainException('Cannot process stripe payment');
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new \DomainException('Cannot process stripe payment');
        }
    }
}
