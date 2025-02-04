<?php

namespace App\Dto\Ticket;

use App\Dto\BaseDto;
use App\Enums\Ticket\TicketDefaultsEnum;
use App\Traits\MakeableTrait;

final readonly class TicketIndexDto extends BaseDto
{
    use MakeableTrait;

    public int $page;

    public int $per_page;

    public string $sort_by;

    public string $sort_order;

    public string $status;

    public string $title;

    /**
     * TicketIndexDto constructor.
     *
     * @param  array  $data  Request data.
     */
    public function __construct(array $data)
    {
        $this->page = $data['page'] ?? (int) TicketDefaultsEnum::PAGE->value;
        $this->per_page = $data['per_page'] ?? (int) TicketDefaultsEnum::PER_PAGE->value;
        $this->sort_by = $data['sort_by'] ?? TicketDefaultsEnum::SORT_BY->value;
        $this->sort_order = $data['sort_order'] ?? TicketDefaultsEnum::SORT_ORDER->value;

        if (isset($data['status'])) {
            $this->status = $data['status'];
        }
        if (isset($data['title'])) {
            $this->title = $data['title'];
        }
    }
}
