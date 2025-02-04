<?php

namespace App\Http\Requests\Ticket;

use App\Dto\BaseDto;
use Illuminate\Foundation\Http\FormRequest;

abstract class BaseTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    abstract public function rules(): array;

    /**
     * Get a DTO (Data Transfer Object) from the validated request data.
     */
    abstract public function getDto(): BaseDto;

    /**
     * Get the validated data as an array.
     */
    public function getDtoArray(): array
    {
        return $this->getDto()->toArray();
    }
}
