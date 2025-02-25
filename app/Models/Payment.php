<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Payment
 *
 * Represents a payment made for a subscription in the application.
 *
 * @property int $id
 * @property int $subscription_id The ID of the subscription being paid for.
 * @property int|null $user_id The ID of the user who made the payment (nullable).
 * @property \Carbon\Carbon $payment_date Timestamp when the payment was made.
 * @property float $amount The amount paid.
 * @property string $payment_method The method of payment (paypal, credit_card, bank_transfer).
 * @property string $payment_status The status of the payment (paid, unpaid, pending, failed).
 * @property string|null $transaction_id Transaction ID from the payment gateway.
 * @property string|null $receipt_number Receipt number for the payment.
 * @property string|null $invoice_link Link to the invoice.
 * @property array|null $gateway_response Raw response from the payment gateway.
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read Subscription $subscription The subscription this payment is for.
 * @property-read User|null $user The user who made this payment.
 */
class Payment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subscription_id',
        'user_id',
        'payment_date',
        'amount',
        'payment_method',
        'payment_status',
        'transaction_id',
        'receipt_number',
        'invoice_link',
        'gateway_response',
    ];

    /**
     * The attributes that should be cast to specific types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
        'payment_method' => 'string',
        'payment_status' => 'string',
        'gateway_response' => 'array',
    ];

    /**
     * Get the subscription this payment is for.
     *
     * @return BelongsTo
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Get the user who made this payment.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the payment is successful.
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if the payment is pending.
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    /**
     * Check if the payment has failed.
     *
     * @return bool
     */
    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }
}
