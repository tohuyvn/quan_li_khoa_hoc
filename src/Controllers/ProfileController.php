<?php

namespace App\Controllers;

use App\Models\User;

class ProfileController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    private function guardClient(): array
    {
        $user = authUser();
        if (!$user) {
            flash('error', 'Vui lòng đăng nhập để tiếp tục.');
            redirect('/login');
        }
        return $user;
    }

    private function guardAdmin(): array
    {
        if (!isAdmin()) {
            flash('error', 'Vui lòng đăng nhập tài khoản quản trị.');
            redirect('/admin/login');
        }
        return authUser();
    }

    public function edit(): void
    {
        $currentUser = $this->guardClient();
        $user = $this->userModel->find($currentUser['id']);
        if (!$user) {
            flash('error', 'Không tìm thấy người dùng.');
            redirect('/');
        }

        $db = \App\DB::connection();
        $stmt = $db->prepare('SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, id DESC');
        $stmt->execute([$currentUser['id']]);
        $addresses = $stmt->fetchAll();

        // Additional Dashboard Stats
        $stmtOrders = $db->prepare('SELECT COUNT(*) FROM orders WHERE user_id = ?');
        $stmtOrders->execute([$currentUser['id']]);
        $ordersCount = (int) $stmtOrders->fetchColumn();

        $stmtSpent = $db->prepare('SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = ? AND payment_status = "paid"');
        $stmtSpent->execute([$currentUser['id']]);
        $totalSpent = (int) $stmtSpent->fetchColumn();

        $walletBalance = \App\WalletService::balance((int) $currentUser['id']);
        $vouchersCount = count((new \App\Models\UserVoucher())->activeForUser((int) $currentUser['id']));
        $wishlistCount = (new \App\Models\Wishlist())->countByUser((int) $currentUser['id']);

        view('client.profile.edit', [
            'title' => 'Thông tin cá nhân',
            'user' => $user,
            'addresses' => $addresses,
            'ordersCount' => $ordersCount,
            'totalSpent' => $totalSpent,
            'walletBalance' => $walletBalance,
            'vouchersCount' => $vouchersCount,
            'wishlistCount' => $wishlistCount,
        ]);
    }

    public function update(): void
    {
        $currentUser = $this->guardClient();
        $user = $this->userModel->find($currentUser['id']);
        if (!$user) {
            flash('error', 'Không tìm thấy người dùng.');
            redirect('/');
        }

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($name === '') {
            flash('error', 'Họ tên không được để trống.');
            redirect('/profile');
        }

        $data = [
            'name' => $name,
            'email' => $user['email'],
            'phone' => $phone,
            'role' => $user['role']
        ];

        // Check if password change is requested
        if ($currentPassword !== '' || $newPassword !== '' || $confirmPassword !== '') {
            if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
                flash('error', 'Vui lòng nhập đầy đủ mật khẩu hiện tại, mật khẩu mới và xác nhận mật khẩu mới.');
                redirect('/profile');
            }

            // Verify current password against DB hash
            $dbUser = $this->userModel->findByEmail($user['email']);
            if (!password_verify($currentPassword, $dbUser['password'])) {
                flash('error', 'Mật khẩu hiện tại không đúng.');
                redirect('/profile');
            }

            if (strlen($newPassword) < 6) {
                flash('error', 'Mật khẩu mới phải từ 6 ký tự trở lên.');
                redirect('/profile');
            }

            if ($newPassword !== $confirmPassword) {
                flash('error', 'Mật khẩu xác nhận không khớp.');
                redirect('/profile');
            }

            $data['password'] = $newPassword;
        }

        if ($this->userModel->update($user['id'], $data)) {
            // Update session
            $updatedUser = $this->userModel->find($user['id']);
            unset($updatedUser['password']);
            $_SESSION['user'] = $updatedUser;

            flash('success', 'Cập nhật thông tin tài khoản thành công.');
            redirect('/profile');
        } else {
            flash('error', 'Có lỗi xảy ra khi cập nhật.');
            redirect('/profile');
        }
    }

    public function adminEdit(): void
    {
        $currentUser = $this->guardAdmin();
        $user = $this->userModel->find($currentUser['id']);
        if (!$user) {
            flash('error', 'Không tìm thấy thông tin quản trị viên.');
            redirect('/admin/products');
        }

        view('admin.profile.edit', [
            'title' => 'Hồ sơ cá nhân',
            'user' => $user
        ]);
    }

    public function adminUpdate(): void
    {
        $currentUser = $this->guardAdmin();
        $user = $this->userModel->find($currentUser['id']);
        if (!$user) {
            flash('error', 'Không tìm thấy thông tin quản trị viên.');
            redirect('/admin/products');
        }

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($name === '') {
            flash('error', 'Họ tên không được để trống.');
            redirect('/admin/profile');
        }

        $data = [
            'name' => $name,
            'email' => $user['email'],
            'phone' => $phone,
            'role' => $user['role']
        ];

        // Check if password change is requested
        if ($currentPassword !== '' || $newPassword !== '' || $confirmPassword !== '') {
            if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
                flash('error', 'Vui lòng nhập đầy đủ mật khẩu hiện tại, mật khẩu mới và xác nhận mật khẩu mới.');
                redirect('/admin/profile');
            }

            $dbUser = $this->userModel->findByEmail($user['email']);
            if (!password_verify($currentPassword, $dbUser['password'])) {
                flash('error', 'Mật khẩu hiện tại không đúng.');
                redirect('/admin/profile');
            }

            if (strlen($newPassword) < 6) {
                flash('error', 'Mật khẩu mới phải từ 6 ký tự trở lên.');
                redirect('/admin/profile');
            }

            if ($newPassword !== $confirmPassword) {
                flash('error', 'Mật khẩu xác nhận không khớp.');
                redirect('/admin/profile');
            }

            $data['password'] = $newPassword;
        }

        if ($this->userModel->update($user['id'], $data)) {
            // Update session
            $updatedUser = $this->userModel->find($user['id']);
            unset($updatedUser['password']);
            $_SESSION['user'] = $updatedUser;

            flash('success', 'Cập nhật hồ sơ quản trị viên thành công.');
            redirect('/admin/profile');
        } else {
            flash('error', 'Có lỗi xảy ra khi cập nhật.');
            redirect('/admin/profile');
        }
    }

    public function enable2fa(): void
    {
        $currentUser = $this->guardAdmin();
        $secret = \App\Services\TotpService::generateSecret();
        $this->userModel->update((int) $currentUser['id'], [
            'name' => $currentUser['name'],
            'email' => $currentUser['email'],
            'phone' => $currentUser['phone'] ?? '',
            'role' => $currentUser['role'],
            'two_factor_secret' => $secret,
            'two_factor_enabled' => 0,
        ]);
        $_SESSION['pending_2fa_secret'] = $secret;
        flash('success', 'Quét mã QR và nhập mã OTP để xác nhận bật 2FA.');
        redirect('/admin/profile');
    }

    public function confirm2fa(): void
    {
        $currentUser = $this->guardAdmin();
        $secret = $_SESSION['pending_2fa_secret'] ?? ($currentUser['two_factor_secret'] ?? '');
        $code = trim($_POST['code'] ?? '');
        if ($secret === '' || !\App\Services\TotpService::verify($secret, $code)) {
            flash('error', 'Mã OTP không đúng. Thử lại.');
            redirect('/admin/profile');
        }
        $this->userModel->update((int) $currentUser['id'], [
            'name' => $currentUser['name'],
            'email' => $currentUser['email'],
            'phone' => $currentUser['phone'] ?? '',
            'role' => $currentUser['role'],
            'two_factor_secret' => $secret,
            'two_factor_enabled' => 1,
        ]);
        unset($_SESSION['pending_2fa_secret']);
        $_SESSION['user'] = $this->userModel->find((int) $currentUser['id']);
        unset($_SESSION['user']['password']);
        flash('success', 'Đã bật xác thực 2 lớp cho tài khoản admin.');
        redirect('/admin/profile');
    }

    public function disable2fa(): void
    {
        $currentUser = $this->guardAdmin();
        $code = trim($_POST['code'] ?? '');
        $secret = (string) ($currentUser['two_factor_secret'] ?? '');
        if ($secret !== '' && !\App\Services\TotpService::verify($secret, $code)) {
            flash('error', 'Mã OTP không đúng — không thể tắt 2FA.');
            redirect('/admin/profile');
        }
        $this->userModel->update((int) $currentUser['id'], [
            'name' => $currentUser['name'],
            'email' => $currentUser['email'],
            'phone' => $currentUser['phone'] ?? '',
            'role' => $currentUser['role'],
            'two_factor_secret' => null,
            'two_factor_enabled' => 0,
        ]);
        unset($_SESSION['pending_2fa_secret']);
        $_SESSION['user'] = $this->userModel->find((int) $currentUser['id']);
        unset($_SESSION['user']['password']);
        flash('success', 'Đã tắt 2FA.');
        redirect('/admin/profile');
    }

    public function storeAddress(): void
    {
        $currentUser = $this->guardClient();

        $label = trim($_POST['label'] ?? '');
        $recipient = trim($_POST['recipient'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $addressLine = trim($_POST['address_line'] ?? '');
        $ward = trim($_POST['ward'] ?? '');
        $district = trim($_POST['district'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $isDefault = isset($_POST['is_default']) ? 1 : 0;

        if ($recipient === '' || $phone === '' || $addressLine === '' || $city === '') {
            flash('error', 'Vui lòng điền đầy đủ thông tin địa chỉ bắt buộc.');
            redirect('/profile');
            return;
        }

        $fullAddress = $addressLine;
        if ($ward !== '') $fullAddress .= ', ' . $ward;
        if ($district !== '') $fullAddress .= ', ' . $district;
        if ($city !== '') $fullAddress .= ', ' . $city;

        $lat = null;
        $lng = null;
        $loc = \App\GoongGeocodeService::geocode($fullAddress);
        if ($loc) {
            $lat = $loc['lat'];
            $lng = $loc['lng'];
        }

        $db = \App\DB::connection();
        $db->beginTransaction();
        try {
            if ($isDefault) {
                $db->prepare('UPDATE user_addresses SET is_default = 0 WHERE user_id = ?')
                   ->execute([$currentUser['id']]);
            } else {
                $stmtCount = $db->prepare('SELECT COUNT(*) FROM user_addresses WHERE user_id = ?');
                $stmtCount->execute([$currentUser['id']]);
                if ((int)$stmtCount->fetchColumn() === 0) {
                    $isDefault = 1;
                }
            }

            $stmt = $db->prepare('INSERT INTO user_addresses (user_id, label, recipient, phone, address_line, ward, district, city, latitude, longitude, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $currentUser['id'],
                $label !== '' ? $label : null,
                $recipient,
                $phone,
                $addressLine,
                $ward !== '' ? $ward : null,
                $district !== '' ? $district : null,
                $city,
                $lat,
                $lng,
                $isDefault
            ]);
            $db->commit();
            flash('success', 'Thêm địa chỉ giao hàng thành công!');
        } catch (\Throwable $e) {
            $db->rollBack();
            flash('error', 'Lỗi khi lưu địa chỉ: ' . $e->getMessage());
        }

        redirect('/profile');
    }

    public function deleteAddress(int $id): void
    {
        $currentUser = $this->guardClient();
        $db = \App\DB::connection();
        
        $stmt = $db->prepare('SELECT * FROM user_addresses WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $currentUser['id']]);
        $address = $stmt->fetch();

        if (!$address) {
            flash('error', 'Không tìm thấy địa chỉ.');
            redirect('/profile');
            return;
        }

        $db->beginTransaction();
        try {
            $db->prepare('DELETE FROM user_addresses WHERE id = ?')->execute([$id]);
            if ($address['is_default']) {
                $next = $db->prepare('SELECT id FROM user_addresses WHERE user_id = ? LIMIT 1');
                $next->execute([$currentUser['id']]);
                $nextId = $next->fetchColumn();
                if ($nextId) {
                    $db->prepare('UPDATE user_addresses SET is_default = 1 WHERE id = ?')->execute([$nextId]);
                }
            }
            $db->commit();
            flash('success', 'Xóa địa chỉ thành công.');
        } catch (\Throwable $e) {
            $db->rollBack();
            flash('error', 'Lỗi khi xóa địa chỉ: ' . $e->getMessage());
        }

        redirect('/profile');
    }

    public function setDefaultAddress(int $id): void
    {
        $currentUser = $this->guardClient();
        $db = \App\DB::connection();

        $stmt = $db->prepare('SELECT * FROM user_addresses WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $currentUser['id']]);
        $address = $stmt->fetch();

        if (!$address) {
            flash('error', 'Không tìm thấy địa chỉ.');
            redirect('/profile');
            return;
        }

        $db->beginTransaction();
        try {
            $db->prepare('UPDATE user_addresses SET is_default = 0 WHERE user_id = ?')->execute([$currentUser['id']]);
            $db->prepare('UPDATE user_addresses SET is_default = 1 WHERE id = ?')->execute([$id]);
            $db->commit();
            flash('success', 'Đã đặt làm địa chỉ mặc định.');
        } catch (\Throwable $e) {
            $db->rollBack();
            flash('error', 'Lỗi: ' . $e->getMessage());
        }

        redirect('/profile');
    }
}
