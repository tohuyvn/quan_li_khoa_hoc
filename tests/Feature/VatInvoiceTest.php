<?php

namespace Tests\Feature;

use App\DB;
use App\Models\Order;
use Tests\IntegrationTestCase;

class VatInvoiceTest extends IntegrationTestCase
{
    private ?int $orderId = null;

    protected function tearDown(): void
    {
        if ($this->orderId !== null) {
            $db = DB::connection();
            $db->prepare('DELETE FROM order_items WHERE order_id = ?')->execute([$this->orderId]);
            $db->prepare('DELETE FROM orders WHERE id = ?')->execute([$this->orderId]);
        }
        parent::tearDown();
    }

    public function test_vat_invoice_is_issued_once_and_keeps_the_same_number(): void
    {
        $db = DB::connection();
        $product = $db->query("SELECT id, name, price FROM products WHERE status = 'active' LIMIT 1")->fetch();
        $this->assertNotFalse($product);

        $orders = new Order();
        $order = $orders->create([
            'customer_name' => 'VAT Test Customer',
            'phone' => '0987654321',
            'email' => 'customer@example.com',
            'total_amount' => (int) $product['price'],
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cash',
            'vat_company_name' => 'Công ty VAT Test',
            'vat_tax_code' => '0101234567',
            'vat_address' => 'Hà Nội',
            'vat_email' => 'invoice@example.com',
        ], [[
            'product_id' => (int) $product['id'],
            'name' => $product['name'],
            'quantity' => 1,
            'price' => (int) $product['price'],
        ]]);
        $this->orderId = (int) $order['id'];

        $first = $orders->issueVatInvoice($this->orderId);
        $second = $orders->issueVatInvoice($this->orderId);

        $this->assertSame(1, (int) $first['vat_invoice_issued']);
        $this->assertSame('HD-VAT-' . str_pad((string) $this->orderId, 6, '0', STR_PAD_LEFT), $first['vat_invoice_no']);
        $this->assertSame($first['vat_invoice_no'], $second['vat_invoice_no']);
        $this->assertSame($first['vat_invoice_issued_at'], $second['vat_invoice_issued_at']);
    }

    public function test_personal_invoice_can_be_issued_without_tax_code(): void
    {
        $db = DB::connection();
        $product = $db->query("SELECT id, name, price FROM products WHERE status = 'active' LIMIT 1")->fetch();
        $this->assertNotFalse($product);

        $orders = new Order();
        $order = $orders->create([
            'customer_name' => 'Nguyễn Văn A',
            'phone' => '0987654321',
            'email' => 'customer@example.com',
            'shipping_address' => 'Hà Nội',
            'total_amount' => (int) $product['price'],
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cash',
            'vat_company_name' => 'Nguyễn Văn A',
            'vat_tax_code' => null,
            'vat_address' => 'Hà Nội',
            'vat_email' => 'customer@example.com',
        ], [[
            'product_id' => (int) $product['id'],
            'name' => $product['name'],
            'quantity' => 1,
            'price' => (int) $product['price'],
        ]]);
        $this->orderId = (int) $order['id'];

        $invoice = $orders->issueVatInvoice($this->orderId);

        $this->assertSame(1, (int) $invoice['vat_invoice_issued']);
        $this->assertSame('Nguyễn Văn A', $invoice['vat_company_name']);
        $this->assertNull($invoice['vat_tax_code']);
        $this->assertSame('customer@example.com', $invoice['vat_email']);
    }
}
