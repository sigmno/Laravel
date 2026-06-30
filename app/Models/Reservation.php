<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    public const STATUS_NEW = 'new';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_EXTENDED = 'extended';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_CANCELLED = 'cancelled';

    public const ACTIVE_STATUSES = [
        self::STATUS_NEW,
        self::STATUS_ISSUED,
        self::STATUS_EXTENDED,
    ];

    protected $fillable = [
        'user_id',
        'book_id',
        'start_date',
        'due_date',
        'status',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'due_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_NEW => 'новое',
            self::STATUS_ISSUED => 'выдано',
            self::STATUS_EXTENDED => 'продлено',
            self::STATUS_RETURNED => 'возвращено',
            self::STATUS_CANCELLED => 'отменено',
        ];
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function isActiveReservation(): bool
    {
        return in_array($this->status, self::ACTIVE_STATUSES, true);
    }

    public function canBeExtended(): bool
    {
        return in_array($this->status, [self::STATUS_NEW, self::STATUS_ISSUED, self::STATUS_EXTENDED], true);
    }

    public function canBeCancelledByUser(): bool
    {
        return in_array($this->status, [self::STATUS_NEW, self::STATUS_EXTENDED], true);
    }
}
