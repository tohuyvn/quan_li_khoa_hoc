<?php

namespace App\Controllers;

class PageController
{
    public function about(): void
    {
        view('client.pages.about', ['title' => 'Về chúng tôi']);
    }

    public function returnPolicy(): void
    {
        view('client.pages.return_policy', ['title' => 'Chính sách đổi trả']);
    }

    public function warranty(): void
    {
        view('client.pages.warranty', ['title' => 'Chính sách bảo hành']);
    }

    public function buyingGuide(): void
    {
        view('client.pages.buying_guide', ['title' => 'Hướng dẫn mua hàng']);
    }
}
