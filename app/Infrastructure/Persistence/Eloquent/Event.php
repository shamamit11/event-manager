<?php

namespace App\Infrastructure\Persistence\Eloquent;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $start
 * @property \Illuminate\Support\Carbon $end
 * @property string|null $frequency
 * @property \Illuminate\Support\Carbon|null $repeat_until
 * @property bool $recurring_pattern
 * @property int $parent_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */

class Event extends Model
{
    protected $table = 'events';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'title',
        'description',
        'start',
        'end',
        'recurring_pattern',
        'frequency',
        'repeat_until',
        'parent_id',
    ];

    protected $casts = [
        'start' => 'datetime',
        'end' => 'datetime',
        'recurring_pattern' => 'boolean',
        'repeat_until' => 'datetime'
    ];
}
