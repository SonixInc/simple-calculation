<?php declare(strict_types=1);

namespace App\Validation;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ValidTaxNumberValidator
 * @package App\Validation
 */
class ValidTaxNumberValidator extends ConstraintValidator
{
    public function __construct(private readonly array $patterns)
    {
    }

    /**
     * @param mixed $value
     * @param ValidTaxNumber $constraint
     * @return void
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $countyCode = strtoupper(substr($value, 0, 2));
        $rule = null;
        foreach ($this->patterns as $code => $pattern) {
            if ($code === $countyCode) {
                $rule = $pattern;
                break;
            }
        }

        if (!$rule) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
            return;
        }

        $regEx = $this->ruleToRegEx($rule);
        if (!preg_match($regEx, substr($value, 2))) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }

    private function ruleToRegEx(string $rule): string
    {
        $regex = '';

        foreach (str_split($rule) as $char) {
            match ($char) {
                'X' => $regex .= '[0-9]',
                'Y' => $regex .= '[A-Z]',
            };
        }

        return '/^' . $regex . '$/';
    }
}
