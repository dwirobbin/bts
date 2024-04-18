<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'speedboat_id',
        'street_id',
        'hours_of_departure',
        'price',
        'stock',
    ];

    /**
     * Get the speedboat that owns the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function speedBoat(): BelongsTo
    {
        return $this->belongsTo(SpeedBoat::class, 'speedboat_id', 'id');
    }

    /**
     * Get the street that owns the Ticket
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function street(): BelongsTo
    {
        return $this->belongsTo(Street::class);
    }
}
