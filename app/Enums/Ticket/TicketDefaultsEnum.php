<?php

namespace App\Enums\Ticket;

enum TicketDefaultsEnum: string
{
    case PAGE = '1';
    case PER_PAGE = '10';
    case SORT_BY = 'created_at';
    case SORT_ORDER = 'desc';
}
