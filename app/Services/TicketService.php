<?php

namespace App\Services;

use App\Repositories\TicketRepository;

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
}
