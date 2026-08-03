<?php

namespace App\Controllers\Admin;

use App\DB;

class DashboardController
{
    public function index(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $db = DB::connection();

        // 1. Today's revenue (status != 'cancelled')
        $todayRevenueStmt = $db->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled' AND DATE(created_at) = CURRENT_DATE()");
        $todayRevenue = (float)($todayRevenueStmt ? $todayRevenueStmt->fetchColumn() : 0);

        // 2. Weekly revenue (status != 'cancelled')
        $weeklyRevenueStmt = $db->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled' AND YEARWEEK(created_at, 1) = YEARWEEK(CURRENT_DATE(), 1)");
        $weeklyRevenue = (float)($weeklyRevenueStmt ? $weeklyRevenueStmt->fetchColumn() : 0);

        // 3. Orders awaiting processing (status = 'pending')
        $pendingOrdersStmt = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
        $pendingOrders = (int)($pendingOrdersStmt ? $pendingOrdersStmt->fetchColumn() : 0);

        // 4. Low-stock products (stock < 5)
        $lowStockStmt = $db->query("SELECT COUNT(*) FROM products WHERE stock < 5");
        $lowStock = (int)($lowStockStmt ? $lowStockStmt->fetchColumn() : 0);

        // 5. Unread messages
        $unreadMessagesStmt = $db->query("SELECT COUNT(*) FROM support_messages WHERE sender = 'user' AND is_read = 0");
        $unreadMessages = (int)($unreadMessagesStmt ? $unreadMessagesStmt->fetchColumn() : 0);

        // 6. 7-day order history for chart (dates and daily revenue)
        $chartStmt = $db->query("
            SELECT DATE(created_at) AS order_date, COUNT(*) AS order_count, SUM(total_amount) AS revenue
            FROM orders
            WHERE status != 'cancelled' AND created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 DAY)
            GROUP BY DATE(created_at)
            ORDER BY order_date ASC
        ");
        $chartData = $chartStmt ? $chartStmt->fetchAll(\PDO::FETCH_ASSOC) : [];

        // Build contiguous 7 days array
        $dates = [];
        $revenueMap = [];
        $countMap = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $dates[] = date('d/m', strtotime($d));
            $revenueMap[$d] = 0;
            $countMap[$d] = 0;
        }

        foreach ($chartData as $row) {
            $d = $row['order_date'];
            // Normalize potential mysql date format difference
            $formattedD = date('Y-m-d', strtotime($d));
            if (isset($revenueMap[$formattedD])) {
                $revenueMap[$formattedD] = (float)$row['revenue'];
                $countMap[$formattedD] = (int)$row['order_count'];
            }
        }

        view('admin.dashboard.index', [
            'title' => 'Tổng quan hệ thống',
            'todayRevenue' => $todayRevenue,
            'weeklyRevenue' => $weeklyRevenue,
            'pendingOrders' => $pendingOrders,
            'lowStock' => $lowStock,
            'unreadMessages' => $unreadMessages,
            'dates' => array_values($dates),
            'revenues' => array_values($revenueMap),
            'counts' => array_values($countMap),
        ]);
    }

    public function auditLogs(): void
    {
        if (!isAdmin()) {
            redirect('/admin/login');
        }

        $db = DB::connection();
        $stmt = $db->query('SELECT * FROM admin_audit_logs ORDER BY created_at DESC LIMIT 100');
        $logs = $stmt ? $stmt->fetchAll(\PDO::FETCH_ASSOC) : [];

        view('admin.dashboard.audit_logs', [
            'title' => 'Nhật ký hoạt động hệ thống',
            'logs' => $logs,
        ]);
    }
}
