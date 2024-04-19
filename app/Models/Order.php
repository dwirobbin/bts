<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\{HasOne, HasMany, BelongsTo};
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    public function goDate(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Carbon::parse($value)->format('d/m/Y'),
        );
    }

    public function backDate(): Attribute
    {
        return new Attribute(
            get: fn ($value) => Carbon::parse($value)->format('d/m/Y'),
        );
    }

    /**
     * Get the user that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ticket that owns the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get all of the passengers for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function passengers(): HasMany
    {
        return $this->hasMany(Passenger::class);
    }

    /**
     * Get the transaction associated with the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    /**
     * Get all of the complaints for the Order
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }
}
