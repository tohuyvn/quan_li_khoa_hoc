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

        $selectedPeriod = trim($_GET['period'] ?? 'week');
        $validPeriods = ['day', 'week', 'month', 'quarter', 'year'];
        if (!in_array($selectedPeriod, $validPeriods, true)) {
            $selectedPeriod = 'week';
        }

        // Base KPI Cards metrics
        $todayRevenue = (float)($db->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled' AND DATE(created_at) = CURRENT_DATE()")->fetchColumn() ?? 0);
        $weeklyRevenue = (float)($db->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled' AND YEARWEEK(created_at, 1) = YEARWEEK(CURRENT_DATE(), 1)")->fetchColumn() ?? 0);
        $monthlyRevenue = (float)($db->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled' AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = MONTH(CURRENT_DATE())")->fetchColumn() ?? 0);
        $quarterlyRevenue = (float)($db->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled' AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND QUARTER(created_at) = QUARTER(CURRENT_DATE())")->fetchColumn() ?? 0);
        $yearlyRevenue = (float)($db->query("SELECT SUM(total_amount) FROM orders WHERE status != 'cancelled' AND YEAR(created_at) = YEAR(CURRENT_DATE())")->fetchColumn() ?? 0);

        $pendingOrders = (int)($db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn() ?? 0);
        $lowStock = (int)($db->query("SELECT COUNT(*) FROM products WHERE stock < 5")->fetchColumn() ?? 0);
        $unreadMessages = (int)($db->query("SELECT COUNT(*) FROM support_messages WHERE sender = 'user' AND is_read = 0")->fetchColumn() ?? 0);

        // Calculate chart data & period summary based on selected period
        $dates = [];
        $revenues = [];
        $counts = [];
        $periodLabel = '';

        if ($selectedPeriod === 'day') {
            $periodLabel = 'Theo Ngày (24h Hôm nay)';
            $chartStmt = $db->query("
                SELECT HOUR(created_at) AS hr, COUNT(*) AS order_count, SUM(total_amount) AS revenue
                FROM orders
                WHERE status != 'cancelled' AND DATE(created_at) = CURRENT_DATE()
                GROUP BY HOUR(created_at)
                ORDER BY hr ASC
            ");
            $rows = $chartStmt ? $chartStmt->fetchAll(\PDO::FETCH_ASSOC) : [];
            $hourlyRevenue = array_fill(0, 24, 0.0);
            $hourlyCount = array_fill(0, 24, 0);

            foreach ($rows as $r) {
                $h = (int)$r['hr'];
                $hourlyRevenue[$h] = (float)$r['revenue'];
                $hourlyCount[$h] = (int)$r['order_count'];
            }

            for ($h = 0; $h < 24; $h++) {
                $dates[] = sprintf('%02dh', $h);
                $revenues[] = $hourlyRevenue[$h];
                $counts[] = $hourlyCount[$h];
            }
        } elseif ($selectedPeriod === 'week') {
            $periodLabel = 'Theo Tuần (Các ngày tuần này)';
            $mondayTs = strtotime('monday this week');
            if (date('N') === '1') {
                $mondayTs = strtotime('today');
            }

            $chartStmt = $db->query("
                SELECT DATE(created_at) AS order_date, COUNT(*) AS order_count, SUM(total_amount) AS revenue
                FROM orders
                WHERE status != 'cancelled' AND YEARWEEK(created_at, 1) = YEARWEEK(CURRENT_DATE(), 1)
                GROUP BY DATE(created_at)
                ORDER BY order_date ASC
            ");
            $rows = $chartStmt ? $chartStmt->fetchAll(\PDO::FETCH_ASSOC) : [];
            $revenueMap = [];
            $countMap = [];

            for ($i = 0; $i < 7; $i++) {
                $d = date('Y-m-d', strtotime("+$i days", $mondayTs));
                $dayName = match((int)date('N', strtotime($d))) {
                    1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5',
                    5 => 'Thứ 6', 6 => 'Thứ 7', 7 => 'Chủ nhật', default => ''
                };
                $dates[] = $dayName . ' (' . date('d/m', strtotime($d)) . ')';
                $revenueMap[$d] = 0.0;
                $countMap[$d] = 0;
            }

            foreach ($rows as $r) {
                $d = date('Y-m-d', strtotime($r['order_date']));
                if (isset($revenueMap[$d])) {
                    $revenueMap[$d] = (float)$r['revenue'];
                    $countMap[$d] = (int)$r['order_count'];
                }
            }

            $revenues = array_values($revenueMap);
            $counts = array_values($countMap);
        } elseif ($selectedPeriod === 'month') {
            $periodLabel = 'Theo Tháng (Tháng ' . date('m/Y') . ')';
            $daysInMonth = (int)date('t');
            $yearMonth = date('Y-m');

            $chartStmt = $db->query("
                SELECT DATE(created_at) AS order_date, COUNT(*) AS order_count, SUM(total_amount) AS revenue
                FROM orders
                WHERE status != 'cancelled' AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = MONTH(CURRENT_DATE())
                GROUP BY DATE(created_at)
                ORDER BY order_date ASC
            ");
            $rows = $chartStmt ? $chartStmt->fetchAll(\PDO::FETCH_ASSOC) : [];
            $revenueMap = [];
            $countMap = [];

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $d = sprintf('%s-%02d', $yearMonth, $day);
                $dates[] = sprintf('%02d/%s', $day, date('m'));
                $revenueMap[$d] = 0.0;
                $countMap[$d] = 0;
            }

            foreach ($rows as $r) {
                $d = date('Y-m-d', strtotime($r['order_date']));
                if (isset($revenueMap[$d])) {
                    $revenueMap[$d] = (float)$r['revenue'];
                    $countMap[$d] = (int)$r['order_count'];
                }
            }

            $revenues = array_values($revenueMap);
            $counts = array_values($countMap);
        } elseif ($selectedPeriod === 'quarter') {
            $currentMonth = (int)date('n');
            $currentQuarter = (int)ceil($currentMonth / 3);
            $startMonth = ($currentQuarter - 1) * 3 + 1;
            $endMonth = $startMonth + 2;
            $periodLabel = "Theo Quý (Quý $currentQuarter/" . date('Y') . ")";

            $chartStmt = $db->query("
                SELECT MONTH(created_at) AS m, COUNT(*) AS order_count, SUM(total_amount) AS revenue
                FROM orders
                WHERE status != 'cancelled' 
                  AND YEAR(created_at) = YEAR(CURRENT_DATE()) 
                  AND MONTH(created_at) BETWEEN $startMonth AND $endMonth
                GROUP BY MONTH(created_at)
                ORDER BY m ASC
            ");
            $rows = $chartStmt ? $chartStmt->fetchAll(\PDO::FETCH_ASSOC) : [];
            $revenueMap = [];
            $countMap = [];

            for ($m = $startMonth; $m <= $endMonth; $m++) {
                $dates[] = "Tháng $m";
                $revenueMap[$m] = 0.0;
                $countMap[$m] = 0;
            }

            foreach ($rows as $r) {
                $m = (int)$r['m'];
                if (isset($revenueMap[$m])) {
                    $revenueMap[$m] = (float)$r['revenue'];
                    $countMap[$m] = (int)$r['order_count'];
                }
            }

            $revenues = array_values($revenueMap);
            $counts = array_values($countMap);
        } elseif ($selectedPeriod === 'year') {
            $periodLabel = 'Theo Năm (Năm ' . date('Y') . ')';

            $chartStmt = $db->query("
                SELECT MONTH(created_at) AS m, COUNT(*) AS order_count, SUM(total_amount) AS revenue
                FROM orders
                WHERE status != 'cancelled' AND YEAR(created_at) = YEAR(CURRENT_DATE())
                GROUP BY MONTH(created_at)
                ORDER BY m ASC
            ");
            $rows = $chartStmt ? $chartStmt->fetchAll(\PDO::FETCH_ASSOC) : [];
            $revenueMap = [];
            $countMap = [];

            for ($m = 1; $m <= 12; $m++) {
                $dates[] = "Tháng $m";
                $revenueMap[$m] = 0.0;
                $countMap[$m] = 0;
            }

            foreach ($rows as $r) {
                $m = (int)$r['m'];
                if (isset($revenueMap[$m])) {
                    $revenueMap[$m] = (float)$r['revenue'];
                    $countMap[$m] = (int)$r['order_count'];
                }
            }

            $revenues = array_values($revenueMap);
            $counts = array_values($countMap);
        }

        $selectedPeriodRevenue = match ($selectedPeriod) {
            'day' => $todayRevenue,
            'week' => $weeklyRevenue,
            'month' => $monthlyRevenue,
            'quarter' => $quarterlyRevenue,
            'year' => $yearlyRevenue,
        };

        view('admin.dashboard.index', [
            'title' => 'Tổng quan hệ thống',
            'selectedPeriod' => $selectedPeriod,
            'periodLabel' => $periodLabel,
            'selectedPeriodRevenue' => $selectedPeriodRevenue,
            'todayRevenue' => $todayRevenue,
            'weeklyRevenue' => $weeklyRevenue,
            'monthlyRevenue' => $monthlyRevenue,
            'quarterlyRevenue' => $quarterlyRevenue,
            'yearlyRevenue' => $yearlyRevenue,
            'pendingOrders' => $pendingOrders,
            'lowStock' => $lowStock,
            'unreadMessages' => $unreadMessages,
            'dates' => array_values($dates),
            'revenues' => array_values($revenues),
            'counts' => array_values($counts),
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

