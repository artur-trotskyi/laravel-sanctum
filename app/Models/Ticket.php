<?php

namespace App\Models;

use App\Casts\DateTimeCast;
use App\Enums\Ticket\TicketStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    public bool $timestamp = true;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tickets';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title', 'description', 'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * The attributes that should be set to default values when a new model is created.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => TicketStatusEnum::Open,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => DateTimeCast::class,
            'updated_at' => DateTimeCast::class,
            'deleted_at' => DateTimeCast::class,
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Ticket $ticket) {
            $ticket->user_id = $ticket->user_id ?: auth()->id();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
