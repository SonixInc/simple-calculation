<?php declare(strict_types=1);

namespace App\Struct;


use App\Validation\ValidTaxNumber;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class CalcPriceStruct
 * @package App\Struct
 */
class CalcPriceStruct
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
}
