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
     *     @OA\Property(property="id", type="integer", example=24),
     *     @OA\Property(property="title", type="string", example="Issue with login"),
     *     @OA\Property(property="status", type="string", enum={"open", "in_progress", "closed"}, example="in_progress"),
     *     @OA\Property(property="userId", type="integer", example=1),
     *     @OA\Property(property="description", type="string", example="Temporibus esse atque sed dolorem. Et tempora ut dolores tempore animi aliquam porro. Maxime et fugit numquam aliquam."),
     *     @OA\Property(property="createdAt", type="string", format="date-time", example="2025-02-05 11:49:12"),
     *     @OA\Property(property="updatedAt", type="string", format="date-time", example="2025-02-05 11:49:12")
     * )
     */
    public function toArray(Request $request): array
    {
        return $this->resource->toArray();
    }
}
