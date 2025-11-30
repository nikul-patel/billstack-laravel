<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * RecurringProfile defines monthly or periodic billable items for a customer.
 */
class RecurringProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'customer_id',
        'name',
        'frequency',
        'next_run_date',
        'day_of_month',
        'amount',
        'notes',
    ];

    protected $casts = [
        'next_run_date' => 'date',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(RecurringProfileItem::class);
    }

    public function generateInvoice(?int $creatorId = null, ?iterable $overrideItems = null): Invoice
    {
        $this->loadMissing('business', 'items');

        $business = $this->business;

        if (! $business) {
            throw new RuntimeException('Recurring profile must be linked to a business.');
        }

        $sequence = $business->invoice_start_no ?? 1;
        $invoicePrefix = $business->invoice_prefix ?? 'INV-';
        $invoiceNumber = $invoicePrefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);

        $lines = $overrideItems !== null
            ? collect($overrideItems)
            : $this->items;

        if ($lines->isEmpty()) {
            throw new RuntimeException('Add at least one item before generating an invoice.');
        }

        $invoice = Invoice::create([
            'business_id' => $business->id,
            'customer_id' => $this->customer_id,
            'invoice_number' => $invoiceNumber,
            'invoice_date' => now(),
            'due_date' => now()->addDays(7),
            'status' => 'draft',
            'currency' => $business->currency ?? 'INR',
            'subtotal' => 0,
            'discount_type' => 'none',
            'discount_value' => 0,
            'tax_total' => 0,
            'grand_total' => 0,
            'amount_paid' => 0,
            'amount_due' => 0,
            'public_hash' => Str::random(40),
            'created_by' => $creatorId,
        ]);

        $lineTotal = 0;

        foreach ($lines as $index => $item) {
            if ($item instanceof RecurringProfileItem) {
                $lineRate = $item->rate;
                $lineQuantity = $item->quantity;
                $lineName = $item->name;
                $lineDescription = $item->description;
                $lineTax = $item->tax_rate ?? 0;
                $lineSort = $item->sort_order;
                $lineItemId = $item->item_id ?? null;
            } else {
                $lineRate = (float) ($item['rate'] ?? 0);
                $lineQuantity = (float) ($item['quantity'] ?? 0);
                $lineName = $item['name'] ?? 'Line Item';
                $lineDescription = $item['description'] ?? null;
                $lineTax = (float) ($item['tax_percent'] ?? 0);
                $lineSort = $item['sort_order'] ?? $index;
                $lineItemId = $item['item_id'] ?? null;
            }

            $lineAmount = $lineRate * $lineQuantity;

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'item_id' => $lineItemId,
                'name' => $lineName,
                'description' => $lineDescription,
                'rate' => $lineRate,
                'quantity' => $lineQuantity,
                'tax_percent' => $lineTax,
                'tax_amount' => 0,
                'line_total' => $lineAmount,
                'sort_order' => $lineSort,
            ]);

            $lineTotal += $lineAmount;
        }

        $invoice->fill([
            'subtotal' => $lineTotal,
            'grand_total' => $lineTotal,
            'amount_due' => $lineTotal,
        ])->save();

        $business->invoice_start_no = $sequence + 1;
        $business->save();

        return $invoice;
    }

    public function calculateNextRunDate(): Carbon
    {
        $current = $this->next_run_date ?? now();
        $frequency = strtolower($this->frequency ?? 'monthly');

        return match ($frequency) {
            'daily' => $current->copy()->addDay(),
            'weekly' => $current->copy()->addWeek(),
            'biweekly' => $current->copy()->addWeeks(2),
            'quarterly' => $this->applyDayOfMonth($current->copy()->addMonths(3)),
            'yearly' => $this->applyDayOfMonth($current->copy()->addYear()),
            default => $this->applyDayOfMonth($current->copy()->addMonth()),
        };
    }

    public function advanceToNextRun(): void
    {
        $next = $this->calculateNextRunDate();

        $this->forceFill(['next_run_date' => $next])->save();
    }

    private function applyDayOfMonth(Carbon $date): Carbon
    {
        if (! $this->day_of_month) {
            return $date;
        }

        $day = min($this->day_of_month, $date->daysInMonth);

        $date->setDate($date->year, $date->month, $day);

        return $date;
    }
}
