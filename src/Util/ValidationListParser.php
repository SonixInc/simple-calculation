<?php declare(strict_types=1);

namespace App\Util;


use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class ValidationListParser
 * @package App\Util
 */
class ValidationListParser
{
    /**
     * Converts violation list to array
     *
     * @param ConstraintViolationListInterface $violationList
     * @return array
     */
    public static function toArray(ConstraintViolationListInterface $violationList): array
    {
        $errors = [];
        foreach ($violationList as $violation) {
            $errors[$violation->getPropertyPath()] = $violation->getMessage();
        }

        return $errors;
    }
}
