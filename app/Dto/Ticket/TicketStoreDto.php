<?php

namespace App\Dto\Ticket;

use App\Dto\BaseDto;
use App\Traits\MakeableTrait;

final readonly class TicketStoreDto extends BaseDto
{
    use MakeableTrait;

    public string $title;

    public string $description;

    public ?string $status;

    /**
     * TicketStoreDto constructor.
     *
     * @param  array  $data  An associative array with data for store.
     */
    public function __construct(array $data)
    {
        $this->title = $data['title'];
        $this->description = $data['description'];
        // Only set status if it's not null
        if (array_key_exists('status', $data) && $data['status'] !== null) {
            $this->status = $data['status'];
        }
    }
}
