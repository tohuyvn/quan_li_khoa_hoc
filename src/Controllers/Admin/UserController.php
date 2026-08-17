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

        $search = trim($_GET['q'] ?? '');
        $segmentFilter = trim($_GET['segment'] ?? '');

        $db = \App\DB::connection();
        $sql = "
            SELECT 
                u.*,
                COALESCE(
                    (SELECT COUNT(*) FROM orders o WHERE (o.user_id = u.id OR (o.user_id IS NULL AND o.email = u.email))), 0
                ) AS total_orders,
                COALESCE(
                    (SELECT SUM(o.total_amount) FROM orders o WHERE (o.user_id = u.id OR (o.user_id IS NULL AND o.email = u.email)) AND o.status != 'cancelled'), 0
                ) AS total_spent,
                (SELECT MAX(o.created_at) FROM orders o WHERE (o.user_id = u.id OR (o.user_id IS NULL AND o.email = u.email))) AS last_order_date
            FROM users u
            WHERE 1=1
        ";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY u.id DESC";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $allUsers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $segmentCounts = [
            'total' => count($allUsers),
            'vip' => 0,
            'potential' => 0,
            'new' => 0,
            'inactive' => 0,
            'admin' => 0,
        ];

        $filteredUsers = [];

        foreach ($allUsers as &$u) {
            $u['id'] = (int)$u['id'];
            $u['total_orders'] = (int)$u['total_orders'];
            $u['total_spent'] = (float)$u['total_spent'];

            $segment = $this->determineSegment($u);
            $u['segment'] = $segment;
            $segmentCounts[$segment['code']]++;

            if ($segmentFilter === '' || $segmentFilter === $segment['code']) {
                $filteredUsers[] = $u;
            }
        }
        unset($u);

        view('admin.users.index', [
            'users' => $filteredUsers,
            'search' => $search,
            'segmentFilter' => $segmentFilter,
            'segmentCounts' => $segmentCounts,
        ]);
    }

    private function determineSegment(array $u): array
    {
        if (($u['role'] ?? '') === 'admin') {
            return [
                'code' => 'admin',
                'name' => 'Quản Trị Viên',
                'badge' => 'bg-danger text-white',
                'icon' => 'bi-shield-lock-fill',
                'desc' => 'Tài khoản quản lý hệ thống'
            ];
        }

        $totalSpent = (float)($u['total_spent'] ?? 0);
        $totalOrders = (int)($u['total_orders'] ?? 0);
        $createdAt = strtotime($u['created_at'] ?? 'now');
        $daysSinceReg = (time() - $createdAt) / 86400;

        if ($totalSpent >= 20000000 || $totalOrders >= 5) {
            return [
                'code' => 'vip',
                'name' => 'Khách VIP',
                'badge' => 'bg-warning text-dark',
                'icon' => 'bi-star-fill',
                'desc' => 'Doanh thu >20tr hoặc >= 5 đơn'
            ];
        }

        if ($totalOrders >= 1 || $totalSpent >= 1000000) {
            return [
                'code' => 'potential',
                'name' => 'Khách Tiềm Năng',
                'badge' => 'bg-success text-white',
                'icon' => 'bi-graph-up-arrow',
                'desc' => 'Đã có 1-4 đơn hàng'
            ];
        }

        if ($daysSinceReg <= 30) {
            return [
                'code' => 'new',
                'name' => 'Khách Mới',
                'badge' => 'bg-info text-white',
                'icon' => 'bi-person-plus-fill',
                'desc' => 'Mới đăng ký trong 30 ngày'
            ];
        }

        return [
            'code' => 'inactive',
            'name' => 'Chưa Mua / Ngủ Đông',
            'badge' => 'bg-secondary text-white',
            'icon' => 'bi-moon-stars-fill',
            'desc' => 'Đăng ký lâu nhưng chưa mua'
        ];
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
