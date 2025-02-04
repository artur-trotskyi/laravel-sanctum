<?php

namespace App\Dto\Ticket;

use App\Dto\BaseDto;
use App\Traits\MakeableTrait;

final readonly class TicketUpdateDto extends BaseDto
{
    use MakeableTrait;

    public string $title;

    public string $description;

    public string $status;

    /**
     * TicketUpdateDto constructor.
     *
     * @param  array  $data  An associative array with data for update.
     */
    public function __construct(array $data)
    {
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->status = $data['status'];
    }
}
