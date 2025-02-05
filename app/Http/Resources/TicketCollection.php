<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class TicketCollection extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    /**
     * @OA\Schema(
     *     schema="TicketCollection",
     *     type="object",
     *
     *     @OA\Property(
     *         property="items",
     *         type="array",
     *
     *         @OA\Items(ref="#/components/schemas/TicketResource")
     *     ),
     *
     *     @OA\Property(
     *         property="meta",
     *         type="object",
     *         nullable=true,
     *         @OA\Property(property="currentPage", type="integer", example=1),
     *         @OA\Property(property="lastPage", type="integer", example=5),
     *         @OA\Property(property="perPage", type="integer", example=10),
     *         @OA\Property(property="total", type="integer", example=50)
     *     ),
     *     @OA\Property(
     *         property="links",
     *         type="object",
     *         nullable=true,
     *         @OA\Property(property="first", type="string", format="uri", example="https://api.example.com/api/v1/tickets?page=1"),
     *         @OA\Property(property="last", type="string", format="uri", example="https://api.example.com/api/v1/tickets?page=5"),
     *         @OA\Property(property="prev", type="string", format="uri", nullable=true, example="https://api.example.com/api/v1/tickets?page=1"),
     *         @OA\Property(property="next", type="string", format="uri", nullable=true, example="https://api.example.com/api/v1/tickets?page=3")
     *     )
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'items' => $this->collection,
            'meta' => $this->when($this->resource instanceof LengthAwarePaginator, function () {
                return [
                    'currentPage' => $this->resource->currentPage(),
                    'lastPage' => $this->resource->lastPage(),
                    'perPage' => $this->resource->perPage(),
                    'total' => $this->resource->total(),
                ];
            }),
            'links' => $this->when($this->resource instanceof LengthAwarePaginator, function () {
                return [
                    'first' => $this->resource->url(1),
                    'last' => $this->resource->url($this->resource->lastPage()),
                    'prev' => $this->resource->previousPageUrl(),
                    'next' => $this->resource->nextPageUrl(),
                ];
            }),
        ];
    }
}
