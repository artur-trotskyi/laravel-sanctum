<?php

namespace App\Services;

use App\Dto\Ticket\TicketIndexDto;
use App\Repositories\TicketRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

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
        // Fields that affect the cache (all other fields will cause the cache to be ignored)
        $cacheableFields = ['page', 'per_page', 'sort_by', 'sort_order'];

        // Get all the properties of the DTO
        $dtoData = get_object_vars($ticketIndexDto);

        // If the DTO contains any extra fields that are not in the cacheable list, don't use the cache
        $hasExtraFields = array_diff_key($dtoData, array_flip($cacheableFields));
        if (! empty($hasExtraFields)) {
            return $this->repo->getFilteredTickets($ticketIndexDto);
        }

        // Create a cache key based on the cacheable fields
        $cacheKey = sprintf(
            'filtered_tickets_%s',
            md5(implode('_', [
                $ticketIndexDto->page,
                $ticketIndexDto->per_page,
                $ticketIndexDto->sort_by,
                $ticketIndexDto->sort_order,
            ]))
        );

        // Dynamic TTL (Time To Live), for example, depending on the complexity of the query
        $ttl = now()->addMinutes(10); // You can adjust this based on other factors

        // Retrieve the filtered tickets from the cache or database, and cache the result for the given TTL
        return Cache::tags(['tickets'])->remember($cacheKey, $ttl, function () use ($ticketIndexDto) {
            return $this->repo->getFilteredTickets($ticketIndexDto);
        });
    }
}
