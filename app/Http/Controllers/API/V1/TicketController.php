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
use Illuminate\Support\Facades\Gate;

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
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="items",
     *                     type="array",
     *
     *                     @OA\Items(ref="#/components/schemas/TicketResource")
     *                 ),
     *
     *                 @OA\Property(
     *                     property="meta",
     *                     type="object",
     *                     @OA\Property(property="currentPage", type="integer"),
     *                     @OA\Property(property="lastPage", type="integer"),
     *                     @OA\Property(property="perPage", type="integer"),
     *                     @OA\Property(property="total", type="integer")
     *                 ),
     *                 @OA\Property(
     *                     property="links",
     *                     type="object",
     *                     @OA\Property(property="first", type="string"),
     *                     @OA\Property(property="last", type="string"),
     *                     @OA\Property(property="prev", type="string", nullable=true),
     *                     @OA\Property(property="next", type="string", nullable=true)
     *                 )
     *             )
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
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="status", type="integer"),
     *                     @OA\Property(property="message", type="string"),
     *                     @OA\Property(property="source", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="status", type="integer"),
     *                     @OA\Property(property="message", type="string"),
     *                     @OA\Property(property="source", type="string")
     *                 )
     *             )
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
     *         response=200,
     *         description="Ticket created successfully.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ticket created successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", example="open"),
     *                 @OA\Property(property="title", type="string", example="test title"),
     *                 @OA\Property(property="description", type="string", example="test description"),
     *                 @OA\Property(property="userId", type="string", example="67a35037e59d0043c407db77"),
     *                 @OA\Property(property="updatedAt", type="string", format="date-time", example="2025-02-06 08:19:45"),
     *                 @OA\Property(property="createdAt", type="string", format="date-time", example="2025-02-06 08:19:45"),
     *                 @OA\Property(property="id", type="string", example="67a470a1e1dd6b072d0bb2fe")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="status", type="integer", example=422),
     *                     @OA\Property(property="message", type="string", example="The title field is required."),
     *                     @OA\Property(property="source", type="string", example="title")
     *                 )
     *             )
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
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="status", type="integer", example=401),
     *                     @OA\Property(property="message", type="string", example="Unauthenticated."),
     *                     @OA\Property(property="source", type="string", example="")
     *                 )
     *             )
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
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="string", example="in_progress"),
     *                 @OA\Property(property="userId", type="string", example="67a35037e59d0043c407db77"),
     *                 @OA\Property(property="title", type="string", example="Id suscipit aut a."),
     *                 @OA\Property(property="description", type="string", example="Temporibus esse atque sed dolorem. Et tempora ut dolores tempore animi aliquam porro. Maxime et fugit numquam aliquam."),
     *                 @OA\Property(property="updatedAt", type="string", format="datetime", example="2025-02-05 11:49:12"),
     *                 @OA\Property(property="createdAt", type="string", format="datetime", example="2025-02-05 11:49:12"),
     *                 @OA\Property(property="id", type="string", example="67a35038e59d0043c407dba0")
     *             ),
     *             @OA\Property(property="message", type="string", example="Ticket retrieved successfully.")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="status", type="integer", example=401),
     *                     @OA\Property(property="message", type="string", example="Unauthenticated."),
     *                     @OA\Property(property="source", type="string", example="")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="status", type="integer", example=403),
     *                     @OA\Property(property="message", type="string", example="This action is unauthorized."),
     *                     @OA\Property(property="source", type="string", example="")
     *                 )
     *             )
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
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *
     *                     @OA\Property(property="status", type="integer", example=404),
     *                     @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Ticket] 67a1e91c72dcd8a4290695a6"),
     *                     @OA\Property(property="source", type="string", example="")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function show(Ticket $ticket): JsonResponse
    {
        Gate::authorize('delete', $ticket);
        $ticket = $this->ticketService->getById($ticket['id']);

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
     *         description="Ticket data to be updated",
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *
     *             @OA\Schema(
     *                 type="object",
     *                 required={"title", "description", "status"},
     *
     *                 @OA\Property(property="title", type="string", example="Issue with login"),
     *                 @OA\Property(property="description", type="string", example="User cannot log in with valid credentials."),
     *                 @OA\Property(property="status", type="string", enum={"open", "in_progress", "closed"}, example="in_progress")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Ticket updated successfully.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Ticket updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", example="closed"),
     *                 @OA\Property(property="title", type="string", example="test title"),
     *                 @OA\Property(property="description", type="string", example="test description"),
     *                 @OA\Property(property="userId", type="string", example="67a35037e59d0043c407db77"),
     *                 @OA\Property(property="updatedAt", type="string", format="date-time", example="2025-02-06 08:45:45"),
     *                 @OA\Property(property="createdAt", type="string", format="date-time", example="2025-02-06 08:22:18"),
     *                 @OA\Property(property="id", type="string", example="67a4713a61af4f3c6707050c")
     *             )
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
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="status", type="integer", example=404),
     *                     @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Ticket] 67a1e91c72dcd8a4290695a2"),
     *                     @OA\Property(property="source", type="string", example="")
     *                 )
     *             )
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
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="status", type="integer", example=401),
     *                     @OA\Property(property="message", type="string", example="Unauthenticated."),
     *                     @OA\Property(property="source", type="string", example="")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="status", type="integer", example=422),
     *                     @OA\Property(property="message", type="string", example="The title field is required."),
     *                     @OA\Property(property="source", type="string", example="title")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="status", type="integer", example=403),
     *                     @OA\Property(property="message", type="string", example="This action is unauthorized."),
     *                     @OA\Property(property="source", type="string", example="")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function update(TicketUpdateRequest $request, Ticket $ticket): JsonResponse
    {
        Gate::authorize('update', $ticket);
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
     *         response=401,
     *         description="Unauthorized",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="status", type="integer", example=401),
     *                     @OA\Property(property="message", type="string", example="Unauthenticated."),
     *                     @OA\Property(property="source", type="string", example="")
     *                 )
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *
     *         @OA\JsonContent(
     *             type="object",
     *
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="status", type="integer", example=403),
     *                     @OA\Property(property="message", type="string", example="This action is unauthorized."),
     *                     @OA\Property(property="source", type="string", example="")
     *                 )
     *             )
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
     *             @OA\Property(property="errors", type="array",
     *
     *                 @OA\Items(
     *                     type="object",
     *
     *                     @OA\Property(property="status", type="integer", example=404),
     *                     @OA\Property(property="message", type="string", example="No query results for model [App\\Models\\Ticket] 67a237edaa76b4fa8d028774"),
     *                     @OA\Property(property="source", type="string", example="")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function destroy(Ticket $ticket): JsonResponse
    {
        Gate::authorize('delete', $ticket);
        $this->ticketService->destroy($ticket['id']);

        return $this->sendResponse([], 'Ticket deleted successfully.', 200);
    }
}
