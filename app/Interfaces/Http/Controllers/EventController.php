<?php

namespace App\Interfaces\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Application\UseCases\CreateEventUseCase;
use App\Application\UseCases\UpdateEventUseCase;
use App\Application\UseCases\ListEventsUseCase;
use App\Application\UseCases\DeleteEventUseCase;
use App\Application\DTOs\EventDTO;
use App\Interfaces\Http\Requests\CreateEventRequest;
use App\Interfaces\Http\Requests\UpdateEventRequest;
use App\Interfaces\Http\Requests\ListEventsRequest;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    private $createEventUseCase;
    private $updateEventUseCase;
    private $listEventsUseCase;
    private $deleteEventUseCase;

    public function __construct(
        CreateEventUseCase $createEventUseCase,
        UpdateEventUseCase $updateEventUseCase,
        ListEventsUseCase $listEventsUseCase,
        DeleteEventUseCase $deleteEventUseCase
    ) {
        $this->createEventUseCase = $createEventUseCase;
        $this->updateEventUseCase = $updateEventUseCase;
        $this->listEventsUseCase = $listEventsUseCase;
        $this->deleteEventUseCase = $deleteEventUseCase;
    }

    public function store(CreateEventRequest $request)
    {
        $recurringPattern = $request->input('recurring_pattern', []);
        $isRecurring = !empty($recurringPattern);

        $frequency = $recurringPattern['frequency'] ?? null;
        $repeatUntil = $recurringPattern['repeat_until'] ?? null;

        $eventDTO = new EventDTO(
            $request->title,
            $request->description,
            $request->start,
            $request->end,
            $isRecurring,
            $frequency,
            $repeatUntil
        );

        try {
            $createdEvent = $this->createEventUseCase->execute(
                $eventDTO->title,
                $eventDTO->description,
                $eventDTO->start,
                $eventDTO->end,
                $eventDTO->recurringPattern,
                $eventDTO->frequency,
                $eventDTO->repeat_until
            );

            $response = [
                'message' => 'Event created successfully',
                'event' => [
                    'id' => $createdEvent->id,
                    'title' => $createdEvent->title,
                    'description' => $createdEvent->description,
                    'start' => $createdEvent->getDateRange()->getStart()->format('Y-m-d\TH:i:s'),
                    'end' => $createdEvent->getDateRange()->getEnd()->format('Y-m-d\TH:i:s'),
                    'recurring_pattern' => $createdEvent->getRecurringPattern() ? [
                        'frequency' => $createdEvent->getRecurringPattern()->getFrequency(),
                        'repeat_until' => $createdEvent->getRecurringPattern()->getRepeatUntil()->format('Y-m-d\TH:i:s'),
                    ] : null,
                ]
            ];

            return response()->json($response, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 409);
        }
    }

    public function update(UpdateEventRequest $request, int $id): JsonResponse
    {
        $recurringPattern = $request->input('recurring_pattern', []);
        $isRecurring = !empty($recurringPattern);

        $frequency = $recurringPattern['frequency'] ?? null;
        $repeatUntil = $recurringPattern['repeat_until'] ?? null;

        $eventDTO = new EventDTO(
            $request->title,
            $request->description,
            $request->start,
            $request->end,
            $isRecurring,
            $frequency,
            $repeatUntil
        );

        try {
            $this->updateEventUseCase->execute(
                $id,
                $eventDTO->title,
                $eventDTO->description,
                $eventDTO->start,
                $eventDTO->end,
                $eventDTO->recurringPattern,
                $eventDTO->frequency,
                $eventDTO->repeat_until
            );

            return response()->json([
                'message' => 'Event updated successfully',
                'event' => $eventDTO
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 409);
        }
    }

    public function list(ListEventsRequest $request): JsonResponse
    {
        $start = $request->start;
        $end = $request->end;
        $perPage = $request->per_page ?? 15;
        $page = $request->page ?? 1;

        try {
            $events = $this->listEventsUseCase->execute($start, $end, $perPage, $page);
            return response()->json($events, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->deleteEventUseCase->execute($id);
            return response()->json(['message' => 'Event deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
