<?php

namespace App\Repositories;

use App\Dto\Ticket\TicketIndexDto;
use App\Models\Ticket;
use Illuminate\Pagination\LengthAwarePaginator;

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

    /**
     * Get filtered and paginated tickets.
     */
    public function getFilteredTickets(TicketIndexDto $dto): LengthAwarePaginator
    {
        $query = $this->model;

        if (isset($dto->status)) {
            $query->where('status', $dto->status);
        }
        if (isset($dto->title)) {
            $query->where('title', 'like', "%{$dto->title}%");
        }

        $query->orderBy($dto->sort_by, $dto->sort_order);

        return $query->paginate($dto->per_page, ['*'], 'page', $dto->page);
    }
}
