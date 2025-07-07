<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanceTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'description',
        'amount',
        'category',
        'transaction_date',
        'payment_method',
        'is_recurring',
        'notes',
        'attachments',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'is_recurring' => 'boolean',
        'attachments' => 'array',
    ];

    /**
     * Get the user who created this transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include income transactions.
     */
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    /**
     * Scope a query to only include expense transactions.
     */
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    /**
     * Get the formatted amount with currency.
     */
    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get the formatted category name.
     */
    public function getFormattedCategoryAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->category));
    }

    /**
     * Get the formatted payment method.
     */
    public function getFormattedPaymentMethodAttribute()
    {
        return ucfirst($this->payment_method);
    }

    /**
     * Get the color class for the transaction type.
     */
    public function getTypeColorClassAttribute()
    {
        return $this->type === 'income' ? 'positive' : 'negative';
    }

    /**
     * Get the icon class for the transaction type.
     */
    public function getTypeIconClassAttribute()
    {
        return $this->type === 'income' ? 'fas fa-plus-circle' : 'fas fa-minus-circle';
    }
}