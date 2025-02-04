<?php

namespace App\Repositories;

use App\Models\Ticket;

class TicketRepository extends BaseRepository
{
    /**
     * Repo Constructor
     * Override to clarify typehinted model.
     *
     * @param  Ticket  $model  Repo DB ORM Model
     */
    public function __construct(Ticket $model)
    {
        parent::__construct($model);
    }
}
