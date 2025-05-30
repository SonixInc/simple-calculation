<?php declare(strict_types=1);

namespace App\Enum;


/**
 * Class OrderState
 * @package App\Enum
 */
enum OrderState: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Paid = 'paid';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
