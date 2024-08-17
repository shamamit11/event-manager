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
            'repeat_until' => '2025-08-20T10:00:00'
        ]
    ]);

    $response->assertStatus(201)->assertJsonStructure([
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

    $response->assertStatus(201)->assertJsonStructure([
        'message',
        'event' => [
            'id',
            'title',
            'description',
            'start',
            'end',
            'recurring_pattern'
        ],
    ]);
});

it('can update an recurring event', function () {
    $event = Event::create([
        'title' => 'Original Event Title',
        'description' => 'Original Description',
        'start' => '2024-08-20T10:00:00',
        'end' => '2024-08-30T10:00:00',
        'recurring_pattern' => false,
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
    $endDate = '2024-09-01T23:59:59';

    $response = $this->get('/api/events/list?start=' . urlencode($startDate) . '&end=' . urlencode($endDate));

    $response->assertStatus(200)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'description',
                    'start',
                    'end',
                    'recurring_pattern'
                ]
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links' => [
                '*' => [
                    'url',
                    'label',
                    'active'
                ]
            ],
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total'
        ]);

    $response->assertJsonFragment([
        'title' => 'Event 1',
        'title' => 'Event 2'
    ]);

    $response->assertJsonMissing([
        'title' => 'Event 3'
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

    $this->assertDatabaseMissing('events', ['id' => $event->id]);
});
