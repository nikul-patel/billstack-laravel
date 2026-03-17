<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_create_page_renders_successfully(): void
    {
        $user = User::create([
            'name' => 'Invoice User',
            'email' => 'invoice-user@example.com',
            'password' => bcrypt('password'),
        ]);
        $business = Business::create([
            'name' => 'Acme Pvt Ltd',
            'owner_id' => $user->id,
            'invoice_prefix' => 'INV-',
            'invoice_start_no' => 1,
        ]);

        $user->update(['business_id' => $business->id]);

        $response = $this->actingAs($user)->get(route('invoices.create'));

        $response->assertOk();
        $response->assertSee('Create Invoice');
    }

    public function test_user_can_create_invoice_with_line_item_and_totals_are_calculated(): void
    {
        $user = User::create([
            'name' => 'Invoice User 2',
            'email' => 'invoice-user-2@example.com',
            'password' => bcrypt('password'),
        ]);
        $business = Business::create([
            'name' => 'Acme Pvt Ltd',
            'owner_id' => $user->id,
            'invoice_prefix' => 'INV-',
            'invoice_start_no' => 1,
        ]);
        $user->update(['business_id' => $business->id]);

        $customer = Customer::create([
            'business_id' => $business->id,
            'name' => 'John Customer',
        ]);

        $item = Item::create([
            'business_id' => $business->id,
            'name' => 'Website Development',
            'unit' => 'service',
            'price' => 100,
            'tax_rate' => 18,
        ]);

        $payload = [
            'customer_id' => $customer->id,
            'invoice_date' => '2026-02-21',
            'due_date' => '2026-02-28',
            'notes' => 'Thanks for your business.',
            'terms' => 'Payment due in 7 days.',
            'items' => [
                [
                    'item_id' => $item->id,
                    'name' => 'Website Development',
                    'description' => 'Landing page build',
                    'rate' => 100,
                    'quantity' => 2,
                    'tax_percent' => 18,
                    'hsn_code' => '9983',
                ],
            ],
        ];

        $response = $this->actingAs($user)->post(route('invoices.store'), $payload);

        $invoice = Invoice::first();

        $response->assertRedirect(route('invoices.show', $invoice));

        $this->assertNotNull($invoice);
        $this->assertSame('INV-0001', $invoice->invoice_number);
        $this->assertEquals(200.0, (float) $invoice->subtotal);
        $this->assertEquals(36.0, (float) $invoice->tax_total);
        $this->assertEquals(236.0, (float) $invoice->grand_total);
        $this->assertEquals(236.0, (float) $invoice->amount_due);

        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'name' => 'Website Development',
            'line_total' => 200.00,
            'tax_amount' => 36.00,
            'hsn_code' => '9983',
        ]);

        $this->assertDatabaseHas('businesses', [
            'id' => $business->id,
            'invoice_start_no' => 2,
        ]);
    }

    public function test_invoice_totals_remain_unchanged_after_product_price_update(): void
    {
        $user = User::create([
            'name' => 'Invoice User 3',
            'email' => 'invoice-user-3@example.com',
            'password' => bcrypt('password'),
        ]);
        $business = Business::create([
            'name' => 'Acme Pvt Ltd',
            'owner_id' => $user->id,
            'invoice_prefix' => 'INV-',
            'invoice_start_no' => 1,
        ]);
        $user->update(['business_id' => $business->id]);

        $customer = Customer::create([
            'business_id' => $business->id,
            'name' => 'Jane Customer',
        ]);

        // Create an item with initial price of 100
        $item = Item::create([
            'business_id' => $business->id,
            'name' => 'Consulting Service',
            'unit' => 'hour',
            'price' => 100,
            'tax_rate' => 10,
        ]);

        // Create invoice with line item using rate of 100
        $payload = [
            'customer_id' => $customer->id,
            'invoice_date' => '2026-03-15',
            'due_date' => '2026-03-22',
            'items' => [
                [
                    'item_id' => $item->id,
                    'name' => 'Consulting Service',
                    'rate' => 100,
                    'quantity' => 5,
                    'tax_percent' => 10,
                ],
            ],
        ];

        $this->actingAs($user)->post(route('invoices.store'), $payload);

        $invoice = Invoice::first();

        // Verify initial totals: subtotal=500, tax=50, total=550
        $this->assertEquals(500.0, (float) $invoice->subtotal);
        $this->assertEquals(50.0, (float) $invoice->tax_total);
        $this->assertEquals(550.0, (float) $invoice->grand_total);

        // Update the product price to 200 (doubling it)
        $item->update(['price' => 200]);
        $this->assertEquals(200, $item->fresh()->price);

        // Refresh invoice and verify totals remain unchanged
        $invoice->refresh();
        $this->assertEquals(500.0, (float) $invoice->subtotal);
        $this->assertEquals(50.0, (float) $invoice->tax_total);
        $this->assertEquals(550.0, (float) $invoice->grand_total);

        // Verify calculation methods use stored line item values, not product prices
        $this->assertEquals(500.0, $invoice->calculateSubtotal());
        $this->assertEquals(50.0, $invoice->calculateTaxTotal());
        $this->assertEquals(550.0, $invoice->calculateGrandTotal());

        // Verify recalculateTotals uses stored line item values
        $invoice->recalculateTotals();
        $this->assertEquals(500.0, (float) $invoice->subtotal);
        $this->assertEquals(50.0, (float) $invoice->tax_total);
        $this->assertEquals(550.0, (float) $invoice->grand_total);
    }

    public function test_invoice_line_item_stores_snapshot_rate_independent_of_product(): void
    {
        $user = User::create([
            'name' => 'Invoice User 4',
            'email' => 'invoice-user-4@example.com',
            'password' => bcrypt('password'),
        ]);
        $business = Business::create([
            'name' => 'Test Business',
            'owner_id' => $user->id,
            'invoice_prefix' => 'TEST-',
            'invoice_start_no' => 1,
        ]);
        $user->update(['business_id' => $business->id]);

        $customer = Customer::create([
            'business_id' => $business->id,
            'name' => 'Test Customer',
        ]);

        $item = Item::create([
            'business_id' => $business->id,
            'name' => 'Product A',
            'unit' => 'unit',
            'price' => 50,
            'tax_rate' => 0,
        ]);

        // Create invoice with a custom rate different from product price
        $payload = [
            'customer_id' => $customer->id,
            'invoice_date' => '2026-03-15',
            'items' => [
                [
                    'item_id' => $item->id,
                    'name' => 'Product A',
                    'rate' => 75, // Custom rate, different from product price of 50
                    'quantity' => 4,
                    'tax_percent' => 0,
                ],
            ],
        ];

        $this->actingAs($user)->post(route('invoices.store'), $payload);

        $invoice = Invoice::first();

        // Verify the stored rate is the custom rate (75), not product price (50)
        $invoiceItem = $invoice->items->first();
        $this->assertEquals(75.0, (float) $invoiceItem->rate);
        $this->assertEquals(300.0, (float) $invoiceItem->line_total); // 75 * 4 = 300

        // Verify invoice totals use the snapshot values
        $this->assertEquals(300.0, (float) $invoice->subtotal);
        $this->assertEquals(300.0, $invoice->calculateSubtotal());
    }
}
