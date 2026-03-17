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

    public function test_changing_item_price_does_not_affect_existing_invoice_totals(): void
    {
        $user = User::create([
            'name' => 'Price Test User',
            'email' => 'price-test@example.com',
            'password' => bcrypt('password'),
        ]);
        $business = Business::create([
            'name' => 'Price Test Business',
            'owner_id' => $user->id,
            'invoice_prefix' => 'INV-',
            'invoice_start_no' => 1,
        ]);
        $user->update(['business_id' => $business->id]);

        $customer = Customer::create([
            'business_id' => $business->id,
            'name' => 'Price Test Customer',
        ]);

        // Create an item with initial price
        $item = Item::create([
            'business_id' => $business->id,
            'name' => 'Consulting Service',
            'unit' => 'hour',
            'price' => 100,
            'tax_rate' => 10,
        ]);

        // Create invoice with the item at $100/hour
        $payload = [
            'customer_id' => $customer->id,
            'invoice_date' => '2026-03-01',
            'due_date' => '2026-03-15',
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

        // Verify initial totals (5 * 100 = 500 subtotal, 50 tax, 550 grand total)
        $this->assertEquals(500.0, (float) $invoice->subtotal);
        $this->assertEquals(50.0, (float) $invoice->tax_total);
        $this->assertEquals(550.0, (float) $invoice->grand_total);

        // Now change the item price to $200
        $item->update(['price' => 200]);
        $item->refresh();

        // Reload the invoice
        $invoice->refresh();

        // Invoice totals should NOT have changed
        $this->assertEquals(500.0, (float) $invoice->subtotal, 'Subtotal should not change when item price changes');
        $this->assertEquals(50.0, (float) $invoice->tax_total, 'Tax total should not change when item price changes');
        $this->assertEquals(550.0, (float) $invoice->grand_total, 'Grand total should not change when item price changes');

        // Verify calculated totals from stored line items also match
        $this->assertEquals(500.0, $invoice->getCalculatedSubtotal());
        $this->assertEquals(50.0, $invoice->getCalculatedTaxTotal());
        $this->assertEquals(550.0, $invoice->getCalculatedGrandTotal());
    }

    public function test_recalculate_totals_from_items_uses_stored_line_item_values(): void
    {
        $user = User::create([
            'name' => 'Recalc Test User',
            'email' => 'recalc-test@example.com',
            'password' => bcrypt('password'),
        ]);
        $business = Business::create([
            'name' => 'Recalc Test Business',
            'owner_id' => $user->id,
            'invoice_prefix' => 'INV-',
            'invoice_start_no' => 1,
        ]);
        $user->update(['business_id' => $business->id]);

        $customer = Customer::create([
            'business_id' => $business->id,
            'name' => 'Recalc Test Customer',
        ]);

        // Create invoice with multiple items
        $payload = [
            'customer_id' => $customer->id,
            'invoice_date' => '2026-03-01',
            'due_date' => '2026-03-15',
            'items' => [
                [
                    'name' => 'Item A',
                    'rate' => 100,
                    'quantity' => 2,
                    'tax_percent' => 10,
                ],
                [
                    'name' => 'Item B',
                    'rate' => 50,
                    'quantity' => 4,
                    'tax_percent' => 5,
                ],
            ],
        ];

        $this->actingAs($user)->post(route('invoices.store'), $payload);

        $invoice = Invoice::first();

        // Expected: Item A = 200 + 20 tax, Item B = 200 + 10 tax
        // Total: 400 subtotal, 30 tax, 430 grand total
        $this->assertEquals(400.0, (float) $invoice->subtotal);
        $this->assertEquals(30.0, (float) $invoice->tax_total);
        $this->assertEquals(430.0, (float) $invoice->grand_total);

        // Simulate someone manually changing the stored invoice totals (bad data)
        $invoice->forceFill(['subtotal' => 999, 'tax_total' => 999, 'grand_total' => 9999])->save();
        $invoice->refresh();

        // Verify the bad data is there
        $this->assertEquals(999.0, (float) $invoice->subtotal);

        // Recalculate from items should restore correct values
        $invoice->recalculateTotalsFromItems()->save();
        $invoice->refresh();

        $this->assertEquals(400.0, (float) $invoice->subtotal, 'Recalculated subtotal should match stored line items');
        $this->assertEquals(30.0, (float) $invoice->tax_total, 'Recalculated tax total should match stored line items');
        $this->assertEquals(430.0, (float) $invoice->grand_total, 'Recalculated grand total should match stored line items');
    }

    public function test_invoice_line_items_store_rate_snapshot_not_current_catalog_price(): void
    {
        $user = User::create([
            'name' => 'Snapshot Test User',
            'email' => 'snapshot-test@example.com',
            'password' => bcrypt('password'),
        ]);
        $business = Business::create([
            'name' => 'Snapshot Test Business',
            'owner_id' => $user->id,
            'invoice_prefix' => 'INV-',
            'invoice_start_no' => 1,
        ]);
        $user->update(['business_id' => $business->id]);

        $customer = Customer::create([
            'business_id' => $business->id,
            'name' => 'Snapshot Test Customer',
        ]);

        $item = Item::create([
            'business_id' => $business->id,
            'name' => 'Product X',
            'price' => 150,
            'tax_rate' => 0,
        ]);

        // Create invoice with a DIFFERENT rate than the catalog price
        $payload = [
            'customer_id' => $customer->id,
            'invoice_date' => '2026-03-01',
            'items' => [
                [
                    'item_id' => $item->id,
                    'name' => 'Product X',
                    'rate' => 75, // Half the catalog price (negotiated discount)
                    'quantity' => 2,
                    'tax_percent' => 0,
                ],
            ],
        ];

        $this->actingAs($user)->post(route('invoices.store'), $payload);

        $invoice = Invoice::with('items')->first();

        // The invoice should use the provided rate (75), not the catalog price (150)
        $this->assertEquals(75.0, (float) $invoice->items->first()->rate);
        $this->assertEquals(150.0, (float) $invoice->items->first()->line_total);
        $this->assertEquals(150.0, (float) $invoice->subtotal);

        // Change catalog price
        $item->update(['price' => 300]);

        // Reload invoice - totals should still be based on stored rate
        $invoice->refresh();
        $this->assertEquals(150.0, (float) $invoice->subtotal);
        $this->assertEquals(150.0, $invoice->getCalculatedSubtotal());
    }
}
