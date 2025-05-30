<?php declare(strict_types=1);

namespace App\Payment;


use Psr\Log\LoggerInterface;
use Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor;

/**
 * Class PaypalPaymentAdapter
 * @package App\Payment
 */
class PaypalPaymentAdapter implements PaymentProcessorInterface
{
    public const string PAYMENT_METHOD = 'paypal';

    public function __construct(
        private readonly PaypalPaymentProcessor $paymentProcessor,
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
            $this->paymentProcessor->pay((int) $price);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new \DomainException('Cannot process paypal payment');
        }
    }
}
