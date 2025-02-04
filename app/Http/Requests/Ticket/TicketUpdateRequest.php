<?php

namespace App\Http\Requests\Ticket;

use App\Dto\Ticket\TicketUpdateDto;
use App\Enums\Ticket\TicketStatusEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class TicketUpdateRequest extends BaseTicketRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'status' => ['required', 'string', Rule::in(TicketStatusEnum::cases())],
        ];
    }

    /**
     * Get a DTO (Data Transfer Object) from the validated request data.
     */
    public function getDto(): TicketUpdateDto
    {
        return TicketUpdateDto::make($this->validated());
    }
}
