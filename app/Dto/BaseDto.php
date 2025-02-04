<?php

namespace App\Dto;

abstract readonly class BaseDto
{
    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        $data = [];
        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }

        return $data;
    }
}
