<?php

namespace App\Http\Resources\Ticket;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    /**
     * @OA\Schema(
     *     schema="TicketResource",
     *     type="object",
     *
     *     @OA\Property(property="id", type="string", example="67a1e91c72dcd8a4290695a6"),
     *     @OA\Property(property="title", type="string", example="Issue with login"),
     *     @OA\Property(property="status", type="string", enum={"open", "in_progress", "closed"}, example="open"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-04 12:00"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-04 12:00")
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
