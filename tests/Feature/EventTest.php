<?php

use App\Infrastructure\Persistence\Eloquent\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create an recurring event', function () {

    $response = $this->postJson('/api/events', [
        'title' => 'Test Event',
        'description' => 'Event Description',
        'start' => '2024-08-20T10:00:00',
        'end' => '2024-08-30T10:00:00',
        'recurring_pattern' => [
            'frequency' => 'daily',
            'repeat_until' => '2025-08-30T10:00:00'
        ]
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'event' => [
                'id',
                'title',
                'description',
                'start',
                'end',
                'recurring_pattern' => [
                    'frequency',
                    'repeat_until',
                ],
                'parent_id'
            ],
        ])
        ->assertJson([
            'message' => 'Event created successfully',
            'event' => [
                'title' => 'Test Event',
                'description' => 'Event Description',
                'start' => '2024-08-20T10:00:00',
                'end' => '2024-08-30T10:00:00',
                'recurring_pattern' => [
                    'frequency' => 'daily',
                    'repeat_until' => '2025-08-30T10:00:00'
                ],
            ],
        ]);
});

it('can create an event', function () {

    $response = $this->postJson('/api/events', [
        'title' => 'Test Event',
        'description' => 'Event Description',
        'start' => '2024-08-20T10:00:00',
        'end' => '2024-08-30T10:00:00'
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'event' => [
                'id',
                'title',
                'description',
                'start',
                'end',
                'recurring_pattern'
            ],
        ])
        ->assertJson([
            'message' => 'Event created successfully',
            'event' => [
                'title' => 'Test Event',
                'description' => 'Event Description',
                'start' => '2024-08-20T10:00:00',
                'end' => '2024-08-30T10:00:00',
                'recurring_pattern' => null
            ],
        ]);
});

it('can update an recurring event', function () {
    $event = Event::create([
        'title' => 'Original Event Title',
        'description' => 'Original Description',
        'start' => '2024-08-20T10:00:00',
        'end' => '2024-08-30T10:00:00',
        'recurring_pattern' => true,
        'frequency' => 'weekly',
        'repeat_until' => '2025-08-30T10:00:00'
    ]);

    $response = $this->putJson('/api/events/' . $event->id, [
        'id' => $event->id,
        'title' => 'Updated Event Title',
        'description' => 'Updated Description',
        'start' => '2024-09-01T10:00:00',
        'end' => '2024-09-15T10:00:00'
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Event updated successfully',
            'event' => [
                'id' => $event->id,
                'title' => 'Updated Event Title',
                'description' => 'Updated Description',
                'start' => '2024-09-01T10:00:00',
                'end' => '2024-09-15T10:00:00',
                'parent_id' => $event->parent_id
            ]
        ]);
});

it('can update an event', function () {
    $event = Event::create([
        'title' => 'Original Event Title',
        'description' => 'Original Description',
        'start' => '2024-08-20T10:00:00',
        'end' => '2024-08-30T10:00:00'
    ]);

    $response = $this->putJson('/api/events/' . $event->id, [
        'id' => $event->id,
        'title' => 'Updated Event Title',
        'description' => 'Updated Description',
        'start' => '2024-09-01T10:00:00',
        'end' => '2024-09-15T10:00:00',
        'recurring_pattern' => [
            'frequency' => 'monthly',
            'repeat_until' => '2027-08-20T10:00:00'
        ]
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Event updated successfully',
            'event' => [
                'id' => $event->id,
                'title' => 'Updated Event Title',
                'description' => 'Updated Description',
                'start' => '2024-09-01T10:00:00',
                'end' => '2024-09-15T10:00:00',
                'recurring_pattern' => [
                    'frequency' => 'monthly',
                    'repeat_until' => '2027-08-20T10:00:00'
                ],
                'parent_id' => $event->parent_id
            ]
        ]);
});

it('can lists events', function () {
    Event::create([
        'title' => 'Event 1',
        'description' => 'Description 1',
        'start' => '2024-08-20T10:00:00',
        'end' => '2024-08-30T10:00:00',
        'recurring_pattern' => true,
        'frequency' => 'daily',
        'repeat_until' => '2025-08-20T10:00:00'
    ]);

    Event::create([
        'title' => 'Event 2',
        'description' => 'Description 2',
        'start' => '2024-09-01T10:00:00',
        'end' => '2024-09-15T10:00:00',
        'recurring_pattern' => true,
        'frequency' => 'weekly',
        'repeat_until' => '2025-09-01T10:00:00'
    ]);

    Event::create([
        'title' => 'Event 3',
        'description' => 'Description 3',
        'start' => '2024-09-10T10:00:00',
        'end' => '2024-09-20T10:00:00',
    ]);

    $startDate = '2024-08-01T00:00:00';
    $endDate = '2024-09-20T23:59:59';

    $response = $this->get('/api/events/list?start=' . urlencode($startDate) . '&end=' . urlencode($endDate));

    //$response->dump();

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'start',
                    'end',
                    'recurring_pattern',
                    'parent_id',
                ]
            ],
            'links' => [
                'first',
                'last',
                'prev',
                'next',
            ],
            'meta' => [
                'current_page',
                'last_page',
                'from',
                'to',
                'path',
                'per_page',
                'total',
            ],
        ])
        ->assertJsonFragment([
            'title' => 'Event 1',
            'description' => 'Description 1',
        ])
        ->assertJsonFragment([
            'title' => 'Event 2',
            'description' => 'Description 2',
        ])
        ->assertJsonFragment([
            'title' => 'Event 3',
            'description' => 'Description 3',
        ]);
});

it('can delete an event', function () {
    $event = Event::create([
        'title' => 'Original Event Title',
        'description' => 'Original Description',
        'start' => '2024-08-20T10:00:00',
        'end' => '2024-08-30T10:00:00',
        'recurring_pattern' => false,
        'frequency' => 'weekly',
        'repeat_until' => '2025-08-30T10:00:00'
    ]);

    $response = $this->delete('/api/events/' . $event->id);

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Event deleted successfully',
        ]);

    $this->assertDatabaseMissing('events', [
        'id' => $event->id,
        'title' => 'Original Event Title',
        'description' => 'Original Description',
        'start' => '2024-08-20T10:00:00',
        'end' => '2024-08-30T10:00:00'
    ]);
});
