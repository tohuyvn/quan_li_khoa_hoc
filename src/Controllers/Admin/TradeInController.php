<?php

namespace App\Controllers\Admin;

use App\DB;

class TradeInController
{
    public function index(): void
    {
        $db = DB::connection();
        $stmt = $db->query('
            SELECT t.*, u.name as user_name 
            FROM trade_ins t 
            LEFT JOIN users u ON t.user_id = u.id 
            ORDER BY t.created_at DESC
        ');
        $tradeIns = $stmt->fetchAll();

        view('admin.trade_ins.index', [
            'tradeIns' => $tradeIns,
        ]);
    }

    public function updateStatus(int $id): void
    {
        $status = trim($_POST['status'] ?? '');
        if (!in_array($status, ['pending', 'approved', 'completed', 'cancelled'], true)) {
            flash('error', 'Trạng thái không hợp lệ.');
            redirect('/admin/trade-ins');
        }

        $db = DB::connection();
        $stmt = $db->prepare('UPDATE trade_ins SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);

        // Audit Log
        $admin = authUser();
        $stmtAudit = $db->prepare('
            INSERT INTO admin_audit_logs (admin_id, admin_name, action, details)
            VALUES (?, ?, \'update_trade_in_status\', ?)
        ');
        $stmtAudit->execute([
            $admin ? (int) $admin['id'] : null,
            $admin ? $admin['name'] : 'System',
            "Cập nhật yêu cầu Trade-in #{$id} sang trạng thái: {$status}"
        ]);

        flash('success', 'Cập nhật trạng thái Trade-in thành công.');
        redirect('/admin/trade-ins');
    }
}
