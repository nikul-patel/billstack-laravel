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
        [$user] = $this->seedInvoiceContext('invoice-user@example.com');

        $response = $this->actingAs($user)->get(route('invoices.create'));

        $response->assertOk();
        $response->assertSee('Create Invoice');
    }

    public function test_user_can_create_invoice_with_line_item_and_totals_are_calculated(): void
    {
        [$user, $business, $customer, $item] = $this->seedInvoiceContext('invoice-user-2@example.com');

        $response = $this->actingAs($user)->post(route('invoices.store'), $this->invoicePayload($customer->id, $item->id));

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

    public function test_invoice_preview_renders_successfully(): void
    {
        [$user, , $customer, $item] = $this->seedInvoiceContext('invoice-user-3@example.com');

        $this->actingAs($user)->post(route('invoices.store'), $this->invoicePayload($customer->id, $item->id));

        $invoice = Invoice::firstOrFail();

        $response = $this->actingAs($user)->get(route('invoices.preview', $invoice));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_invoice_pdf_download_renders_successfully(): void
    {
        [$user, , $customer, $item] = $this->seedInvoiceContext('invoice-user-4@example.com');

        $this->actingAs($user)->post(route('invoices.store'), $this->invoicePayload($customer->id, $item->id));

        $invoice = Invoice::firstOrFail();

        $response = $this->actingAs($user)->get(route('invoices.pdf', $invoice));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $response->assertHeader('content-disposition');
    }

    private function seedInvoiceContext(string $email): array
    {
        $user = User::create([
            'name' => 'Invoice User',
            'email' => $email,
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

        return [$user, $business, $customer, $item];
    }

    private function invoicePayload(int $customerId, int $itemId): array
    {
        return [
            'customer_id' => $customerId,
            'invoice_date' => '2026-02-21',
            'due_date' => '2026-02-28',
            'notes' => 'Thanks for your business.',
            'terms' => 'Payment due in 7 days.',
            'items' => [
                [
                    'item_id' => $itemId,
                    'name' => 'Website Development',
                    'description' => 'Landing page build',
                    'rate' => 100,
                    'quantity' => 2,
                    'tax_percent' => 18,
                    'hsn_code' => '9983',
                ],
            ],
        ];
    }
}
