<?php

namespace App\Enums\Ticket;

enum TicketStatusEnum: string
{
    case Open = 'open';
    case InProgress = 'in_progress';
    case Closed = 'closed';

    /**
     * Get all enum values as an array.
     *
     * @return string[]
     */
    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }
}
