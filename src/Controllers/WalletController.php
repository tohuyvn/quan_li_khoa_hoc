<?php

namespace App\Controllers;

use App\Models\UserVoucher;
use App\WalletService;

class WalletController
{
    public function index(): void
    {
        $user = authUser();
        if (!$user) {
            flash('error', 'Vui lòng đăng nhập để xem Ví Hades.');
            redirect('/login');
        }

        $userId = (int) $user['id'];
        $voucherModel = new UserVoucher();

        view('client.profile.wallet', [
            'title'        => 'Ví Hades',
            'balance'      => WalletService::balance($userId),
            'transactions' => WalletService::transactions($userId, 30),
            'vouchers'     => $voucherModel->activeForUser($userId),
        ]);
    }
}
