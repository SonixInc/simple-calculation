<?php declare(strict_types=1);

namespace App\Validation;


use Symfony\Component\Validator\Constraint;

/**
 * Class ValidTaxNumber
 * @package App\Validation
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class ValidTaxNumber extends Constraint
{
    public string $message = 'Format {{ value }} is invalid';
}
