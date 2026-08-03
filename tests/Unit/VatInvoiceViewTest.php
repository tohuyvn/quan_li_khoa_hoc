<?php

namespace Tests\Unit;

use Tests\TestCase;

class VatInvoiceViewTest extends TestCase
{
    public function test_printable_invoice_contains_auto_print_script_and_customer_data(): void
    {
        $order = [
            'id' => 123,
            'order_code' => 'HD260801-TEST1',
            'customer_name' => 'Nguyễn Văn A',
            'phone' => '0987654321',
            'shipping_address' => 'Hà Nội',
            'vat_company_name' => 'Nguyễn Văn A',
            'vat_tax_code' => null,
            'vat_address' => 'Hà Nội',
            'vat_email' => 'customer@example.com',
            'vat_invoice_no' => 'HD-VAT-000123',
            'vat_invoice_issued_at' => '2026-08-01 11:37:00',
            'created_at' => '2026-08-01 11:30:00',
            'shipping_fee' => 0,
            'discount_amount' => 0,
            'loyalty_discount_amount' => 0,
            'total_amount' => 1_000_000,
        ];
        $items = [[
            'product_name' => 'Sản phẩm Test',
            'quantity' => 1,
            'price' => 1_000_000,
        ]];
        $autoPrint = true;
        $backUrl = '/orders/success';

        ob_start();
        require BASE_PATH . '/views/client/orders/invoice.php';
        $html = (string) ob_get_clean();

        $this->assertStringContainsString('HD-VAT-000123', $html);
        $this->assertStringContainsString('Người mua hàng:', $html);
        $this->assertStringContainsString('Nguyễn Văn A', $html);
        $this->assertStringContainsString('customer@example.com', $html);
        $this->assertStringNotContainsString('Mã số thuế:', $html);
        $this->assertStringContainsString('window.print()', $html);
        $this->assertStringContainsString('Sản phẩm Test', $html);
    }
}
