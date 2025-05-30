<?php

namespace App\Payment;

interface PaymentProcessorInterface
{
    /**
     * Checks if payment method is supported
     *
     * @param string $paymentMethod
     * @return bool
     */
    public function supports(string $paymentMethod): bool;

    /**
     * Makes payment for provided price
     *
     * @param float $price
     * @return void
     */
    public function pay(float $price): void;
}
