<?php declare(strict_types=1);

namespace App\Enum;


/**
 * Class PromotionType
 * @package App\Enum
 */
enum PromotionType: string
{
    case Fixed = 'fixed';
    case Percentage = 'percentage';
}
