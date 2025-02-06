<?php

namespace App\Http\Resources\Ticket;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MongoDB\BSON\UTCDateTime;

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
     *     @OA\Property(property="status", type="string", enum={"open", "in_progress", "closed"}, example="in_progress"),
     *     @OA\Property(property="userId", type="string", example="67a35037e59d0043c407db77"),
     *     @OA\Property(property="description", type="string", example="Temporibus esse atque sed dolorem. Et tempora ut dolores tempore animi aliquam porro. Maxime et fugit numquam aliquam."),
     *     @OA\Property(property="createdAt", type="string", format="date-time", example="2025-02-05 11:49:12"),
     *     @OA\Property(property="updatedAt", type="string", format="date-time", example="2025-02-05 11:49:12")
     * )
     */
    public function toArray(Request $request): array
    {
        $data = $this->resource->toArray();

        // Array of fields that need date formatting
        $dateFields = ['created_at', 'updated_at', 'deleted_at'];
        // Loop through the fields and format each date if it exists
        foreach ($dateFields as $field) {
            if (isset($data[$field]) && $data[$field] instanceof UTCDateTime) {
                $data[$field] = $this->formatMongoDate($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Format MongoDB UTCDateTime into a desired format.
     */
    protected function formatMongoDate($date): string
    {
        return Carbon::instance($date->toDateTime())->format('Y-m-d H:i:s');
    }
}
