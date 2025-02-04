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
