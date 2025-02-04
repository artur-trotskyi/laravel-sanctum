<?php

namespace App\Http\Requests\Ticket;

use App\Dto\Ticket\TicketIndexDto;
use App\Enums\Ticket\TicketStatusEnum;
use Illuminate\Validation\Rule;

class TicketIndexRequest extends BaseTicketRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'sort_by' => ['sometimes', 'string', Rule::in(['id', 'title', 'status', 'created_at', 'updated_at'])],
            'sort_order' => ['sometimes', 'string', Rule::in(['asc', 'desc'])],
            'status' => ['sometimes', 'nullable', Rule::in(TicketStatusEnum::cases())],
            'title' => ['sometimes', 'string', 'max:255'],
        ];
    }

    /**
     * Get DTO from request data.
     */
    public function getDto(): TicketIndexDto
    {
        return TicketIndexDto::make($this->validated());
    }
}
