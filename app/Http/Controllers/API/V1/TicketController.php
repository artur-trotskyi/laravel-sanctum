<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Ticket\TicketIndexRequest;
use App\Http\Requests\Ticket\TicketStoreRequest;
use App\Http\Requests\Ticket\TicketUpdateRequest;
use App\Http\Resources\TicketCollection;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;

class TicketController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        private readonly TicketService $ticketService,
    ) {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index(TicketIndexRequest $request): JsonResponse
    {
        $ticketIndexDto = $request->getDto();
        $tickets = $this->ticketService->getFilteredTickets($ticketIndexDto);

        return $this->sendResponse(new TicketCollection($tickets), 'Tickets retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TicketStoreRequest $request): JsonResponse
    {
        $ticketStoreData = $request->getDtoArray();
        $ticket = $this->ticketService->create($ticketStoreData);

        return $this->sendResponse(new TicketResource($ticket), 'Ticket created successfully.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $ticket = $this->ticketService->getById($id);

        if (is_null($ticket)) {
            return $this->sendError('Ticket not found.');
        }

        return $this->sendResponse(new TicketResource($ticket), 'Ticket retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TicketUpdateRequest $request, Ticket $ticket): JsonResponse
    {
        $ticketUpdateData = $request->getDtoArray();
        $this->ticketService->update($ticket['id'], $ticketUpdateData);

        return $this->sendResponse(new TicketResource($ticket->fresh()), 'Ticket updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket): JsonResponse
    {
        $this->ticketService->destroy($ticket['id']);

        return $this->sendResponse([], 'Ticket deleted successfully.', 200);
    }
}
