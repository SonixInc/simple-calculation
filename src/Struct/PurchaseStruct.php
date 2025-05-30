<?php declare(strict_types=1);

namespace App\Struct;

use App\Validation\ValidTaxNumber;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class PurchaseStruct
 * @package App\Struct
 */
class PurchaseStruct
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $productId;

    #[Assert\NotBlank]
    #[ValidTaxNumber]
    #[Assert\Length(max: 60)]
    public string $taxNumber;

    #[Assert\Length(max: 255)]
    public string $couponCode;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['paypal', 'stripe'])]
    public string $paymentMethod;
}
