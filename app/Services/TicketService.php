<?php

namespace App\Services;

use App\Dto\Ticket\TicketIndexDto;
use App\Repositories\TicketRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketService extends BaseService
{
    /**
     * Create a new TicketService instance.
     *
     * @param  TicketRepository  $repo  The repository for managing tickets.
     */
    public function __construct(TicketRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Get filtered and paginated tickets.
     */
    public function getFilteredTickets(TicketIndexDto $ticketIndexDto): LengthAwarePaginator
    {
        return $this->repo->getFilteredTickets($ticketIndexDto);
    }
}
