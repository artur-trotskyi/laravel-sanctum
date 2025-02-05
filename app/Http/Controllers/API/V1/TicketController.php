<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Ticket\TicketIndexRequest;
use App\Http\Requests\Ticket\TicketStoreRequest;
use App\Http\Requests\Ticket\TicketUpdateRequest;
use App\Http\Resources\Ticket\TicketCollection;
use App\Http\Resources\Ticket\TicketResource;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *     title="API Documentation",
 *     description="Detailed documentation of the Rocket9 API",
 *     version="1.0.0",
 * )
 */
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
    /**
     * @OA\Get(
     *     path="/api/v1/tickets",
     *     summary="Get list of tickets with optional filters",
     *     tags={"Tickets"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page (max 100)",
     *         required=false,
     *
     *         @OA\Schema(type="integer", minimum=1, maximum=100)
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"id", "title", "status", "created_at", "updated_at"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by ticket status",
     *         required=false,
     *
     *         @OA\Schema(type="string", enum={"open", "in_progress", "closed"})
     *     ),
     *
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Filter by ticket title",
     *         required=false,
     *
     *         @OA\Schema(type="string", maxLength=255)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Tickets retrieved successfully.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *
     *                 @OA\Items(ref="#/components/schemas/TicketResource")
     *             ),
     *
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="currentPage", type="integer"),
     *                 @OA\Property(property="lastPage", type="integer"),
     *                 @OA\Property(property="perPage", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string"),
     *                 @OA\Property(property="last", type="string"),
     *                 @OA\Property(property="prev", type="string"),
     *                 @OA\Property(property="next", type="string")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid parameters",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
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
    /**
     * @OA\Post(
     *     path="/api/v1/tickets",
     *     summary="Create a new ticket",
     *     tags={"Tickets"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *         description="Ticket data to be created",
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title", "description"},
     *
     *                 @OA\Property(property="title", type="string", example="Issue with login"),
     *                 @OA\Property(property="description", type="string", example="Unable to log in with valid credentials"),
     *                 @OA\Property(property="status", type="string", enum={"open", "in_progress", "closed"}, example="open")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="Ticket created successfully.",
     *
     *         @OA\JsonContent(
     *             ref="#/components/schemas/TicketResource"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input, validation errors",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Something went wrong.")
     *         )
     *     )
     * )
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
    /**
     * @OA\Get(
     *     path="/api/v1/tickets/{id}",
     *     summary="Get a ticket by ID",
     *     tags={"Tickets"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the ticket to retrieve",
     *
     *         @OA\Schema(type="string", example="67a1e91c72dcd8a4290695a6")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Ticket retrieved successfully.",
     *
     *         @OA\JsonContent(
     *             ref="#/components/schemas/TicketResource"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Ticket not found.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
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
    /**
     * @OA\Put(
     *     path="/api/v1/tickets/{id}",
     *     summary="Update a ticket by ID",
     *     tags={"Tickets"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the ticket to update",
     *
     *         @OA\Schema(type="string", example="67a1e91c72dcd8a4290695a6")
     *     ),
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *             required={"title", "description", "status"},
     *
     *             @OA\Property(property="title", type="string", example="Issue with login"),
     *             @OA\Property(property="description", type="string", example="User cannot log in with valid credentials."),
     *             @OA\Property(property="status", type="string", enum={"open", "in_progress", "closed"}, example="in_progress")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Ticket updated successfully.",
     *
     *         @OA\JsonContent(
     *             ref="#/components/schemas/TicketResource"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Ticket not found.")
     *         )
     *     )
     * )
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
    /**
     * @OA\Delete(
     *     path="/api/v1/tickets/{id}",
     *     summary="Delete a ticket by ID",
     *     tags={"Tickets"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the ticket to delete",
     *
     *         @OA\Schema(type="string", example="67a1e91c72dcd8a4290695a6")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Ticket deleted successfully.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object", example={}),
     *             @OA\Property(property="message", type="string", example="Ticket deleted successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Ticket not found",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Ticket not found.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function destroy(Ticket $ticket): JsonResponse
    {
        $this->ticketService->destroy($ticket['id']);

        return $this->sendResponse([], 'Ticket deleted successfully.', 200);
    }
}
