<?php

namespace App\Controllers\Admin;

use App\Models\User;

class UserController
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function index(): void
    {
        $this->guard();
        view('admin.users.index', [
            'users' => $this->users->all(),
        ]);
    }

    public function createForm(): void
    {
        $this->guard();
        view('admin.users.form', ['user' => null]);
    }

    public function store(): void
    {
        $this->guard();
        $data = $this->validatedData();
        if ($data === null) {
            redirect('/admin/users/create');
        }

        if ($this->users->findByEmail($data['email'])) {
            flash('error', 'Email đã tồn tại.');
            redirect('/admin/users/create');
        }

        $this->users->create($data);
        flash('success', 'Thêm người dùng thành công.');
        redirect('/admin/users');
    }

    public function editForm(int $id): void
    {
        $this->guard();
        $user = $this->users->find($id);
        if (!$user) {
            flash('error', 'Không tìm thấy người dùng.');
            redirect('/admin/users');
        }

        view('admin.users.form', ['user' => $user]);
    }

    public function update(int $id): void
    {
        $this->guard();
        $user = $this->users->find($id);
        if (!$user) {
            flash('error', 'Không tìm thấy người dùng.');
            redirect('/admin/users');
        }

        $data = $this->validatedData($user);
        if ($data === null) {
            redirect("/admin/users/{$id}/edit");
        }

        $existing = $this->users->findByEmail($data['email']);
        if ($existing && (int) $existing['id'] !== $id) {
            flash('error', 'Email đã được sử dụng.');
            redirect("/admin/users/{$id}/edit");
        }

        if (isset($data['password'])) {
            logAdminAction('user_password_changed_directly', "Admin directly updated password for user ID: {$id} ({$user['email']})");
        }

        $this->users->update($id, $data);
        flash('success', 'Cập nhật người dùng thành công.');
        redirect('/admin/users');
    }

    public function destroy(int $id): void
    {
        $this->guard();
        if (!$this->users->delete($id)) {
            flash('error', 'Không thể xóa (có thể là tài khoản admin).');
        } else {
            flash('success', 'Đã xóa người dùng.');
        }
        redirect('/admin/users');
    }

    public function requestPasswordReset(int $id): void
    {
        $this->guard();
        $user = $this->users->find($id);
        if (!$user) {
            flash('error', 'Không tìm thấy người dùng.');
            redirect('/admin/users');
        }

        $admin = authUser();
        $service = new \App\PasswordResetService();
        $result = $service->initiateAdminReset($id, (int) ($admin['id'] ?? 0));

        if ($result['success']) {
            logAdminAction('password_reset', "Admin initiated password reset for user ID: {$id} ({$user['email']})");
        }

        flash($result['success'] ? 'success' : 'error', $result['message']);
        redirect('/admin/users');
    }

    private function guard(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }
    }

    private function validatedData(?array $existing = null): ?array
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'customer';

        if ($name === '' || $email === '') {
            flash('error', 'Họ tên và email không được trống.');
            return null;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Email không hợp lệ.');
            return null;
        }

        if ($existing === null && strlen($password) < 6) {
            flash('error', 'Mật khẩu tối thiểu 6 ký tự.');
            return null;
        }

        if ($existing !== null && $password !== '' && strlen($password) < 6) {
            flash('error', 'Mật khẩu mới tối thiểu 6 ký tự.');
            return null;
        }

        $data = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role' => $role,
        ];

        if ($password !== '') {
            $data['password'] = $password;
        } elseif ($existing === null) {
            flash('error', 'Mật khẩu không được trống.');
            return null;
        }

        return $data;
    }
}
