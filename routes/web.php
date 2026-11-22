<?php

use App\Controllers\Admin\LmsController as AdminLmsController;
use App\Controllers\Admin\UserController as AdminUserController;
use App\Controllers\AuthController;
use App\Controllers\ClassroomController;
use App\Controllers\CertificateController;
use App\Controllers\LmsCourseController;
use App\Controllers\MoMoController;
use App\Controllers\OrderController;
use App\Controllers\ProfileController;
use App\Controllers\QuizController;
use App\Controllers\VietQrController;
use App\Controllers\VnpayController;
use App\Router;

$router = new Router();
$GLOBALS['router'] = $router;

// Health Check
$router->get('/health', function () {
    $health = \App\DB::health();
    http_response_code($health['ok'] ? 200 : 503);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode([
        'ok' => $health['ok'],
        'app' => config('app_name', 'Hades Academy'),
        'time' => date(DATE_ATOM),
        'database' => $health,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
});

// Trang chủ -> Catalog khóa học
$router->get('/', fn() => (new LmsCourseController())->index());

// ==========================================
// LMS CLIENT ROUTES (Học viên & Khóa học)
// ==========================================
$router->get('/courses', fn() => (new LmsCourseController())->index());
$router->get('/courses/{id}/enroll', fn($id) => (new LmsCourseController())->enroll((int)$id));
$router->post('/courses/{id}/enroll', fn($id) => (new LmsCourseController())->enroll((int)$id));
$router->get('/courses/{id}/checkout', fn($id) => (new LmsCourseController())->checkout((int)$id));
$router->get('/courses/{slug}', fn($slug) => (new LmsCourseController())->show($slug));
$router->get('/courses/{id}/payment', fn($id) => redirect('/courses/' . (int)$id . '/checkout'));
$router->post('/courses/{id}/payment', fn($id) => (new LmsCourseController())->processPayment((int)$id));
$router->post('/courses/{id}/review', fn($id) => (new LmsCourseController())->storeReview((int)$id));
$router->get('/my-courses', fn() => (new LmsCourseController())->myCourses());

// Phòng học trực tuyến (Classroom), Tiến độ & Ghi chú
$router->get('/learn/{courseId}', fn($courseId) => (new ClassroomController())->learn((int)$courseId));
$router->get('/learn/{courseId}/lesson/{lessonId}', fn($courseId, $lessonId) => (new ClassroomController())->learn((int)$courseId, (int)$lessonId));
$router->post('/api/progress/update', fn() => (new ClassroomController())->updateProgress());
$router->get('/learn/{courseId}/download/{materialId}', fn($courseId, $materialId) => (new ClassroomController())->downloadMaterial((int)$courseId, (int)$materialId));
$router->post('/learn/{courseId}/lesson/{lessonId}/discussion', fn($courseId, $lessonId) => (new ClassroomController())->postDiscussion((int)$courseId, (int)$lessonId));
$router->post('/api/lessons/{lessonId}/notes', fn($lessonId) => (new ClassroomController())->saveNote((int)$lessonId));
$router->post('/api/notes/{noteId}/delete', fn($noteId) => (new ClassroomController())->deleteNote((int)$noteId));
$router->delete('/api/notes/{noteId}', fn($noteId) => (new ClassroomController())->deleteNote((int)$noteId));

// Thi trắc nghiệm & Kiểm tra 15 phút
$router->get('/quiz/{id}', fn($id) => (new QuizController())->show((int)$id));
$router->post('/quiz/{id}/submit', fn($id) => (new QuizController())->submit((int)$id));

// Chứng chỉ số & Tra cứu QR công khai
$router->get('/certificates/{code}', fn($code) => (new CertificateController())->show($code));
$router->get('/verify-certificate', fn() => (new CertificateController())->verify());
$router->post('/verify-certificate', fn() => (new CertificateController())->verify());

// ==========================================
// THANH TOÁN KHÓA HỌC (VNPay, MoMo, VietQR, Tiền mặt)
// ==========================================
$router->get('/payment/vnpay/pay', fn() => (new VnpayController())->pay());
$router->post('/payment/vnpay/process', fn() => (new VnpayController())->process());
$router->get('/payment/vnpay/gateway', fn() => (new VnpayController())->gateway());
$router->get('/payment/vnpay/return', fn() => (new VnpayController())->return());

$router->get('/payment/momo/pay', fn() => (new MoMoController())->pay());
$router->post('/payment/momo/process', fn() => (new MoMoController())->process());
$router->get('/payment/momo/gateway', fn() => (new MoMoController())->gateway());
$router->get('/payment/momo/status', fn() => (new MoMoController())->status());
$router->get('/payment/momo/return', fn() => (new MoMoController())->return());
$router->post('/payment/momo/callback', fn() => (new MoMoController())->callback());

$router->get('/payment/vietqr/pay', fn() => (new VietQrController())->pay());
$router->get('/payment/vietqr/status', fn() => (new VietQrController())->status());
$router->post('/payment/vietqr/webhook', fn() => (new VietQrController())->webhook());
$router->post('/payment/vietqr/simulate', fn() => (new VietQrController())->simulatePaid());
$router->post('/payment/vietqr/confirm', fn() => (new VietQrController())->confirmTransferred());
$router->get('/payment/vietqr', fn() => (new VietQrController())->pay());

$router->post('/payment/cash/simulate-confirm', function() {
    $orderId = (int) ($_POST['order_id'] ?? $_SESSION['last_order_id'] ?? 0);
    $orderModel = new \App\Models\Order();
    $order = $orderId > 0 ? $orderModel->find($orderId) : null;
    if (!$order || ($order['payment_method'] ?? '') !== 'cash') {
        flash('error', 'Không tìm thấy đơn hàng tiền mặt.');
        redirect('/orders/success');
    }
    \App\PaymentService::markPaid($orderId);
    flash('success', 'Thu ngân đã xác nhận nhận tiền mặt thành công! Khóa học đã được mở khóa.');
    redirect('/orders/success');
});

// Lịch sử đăng ký & Hóa đơn khóa học
$router->get('/orders', fn() => (new OrderController())->index());
$router->get('/orders/{id}', fn($id) => (new OrderController())->show((int) $id));
$router->get('/orders/{id}/invoice', fn($id) => (new OrderController())->invoice((int) $id));
$router->get('/orders/success', fn() => (new OrderController())->success());

// ==========================================
// XÁC THỰC & TÀI KHOẢN (Auth & Profile)
// ==========================================
$router->get('/login', fn() => (new AuthController())->showLogin());
$router->post('/login', fn() => (new AuthController())->login());
$router->get('/login/google', fn() => (new AuthController())->redirectToGoogle());
$router->get('/login/google/callback', fn() => (new AuthController())->handleGoogleCallback());
$router->get('/register', fn() => (new AuthController())->showRegister());
$router->post('/register', fn() => (new AuthController())->register());
$router->get('/verify-otp', fn() => (new AuthController())->showVerifyOtp());
$router->post('/verify-otp', fn() => (new AuthController())->verifyOtp());
$router->post('/resend-otp', fn() => (new AuthController())->resendOtp());
$router->post('/logout', fn() => (new AuthController())->logout());
$router->get('/profile', fn() => (new ProfileController())->edit());
$router->post('/profile', fn() => (new ProfileController())->update());

// Quên mật khẩu
$router->get('/forgot-password', fn() => (new AuthController())->showForgotPassword());
$router->post('/forgot-password', fn() => (new AuthController())->sendResetOtp());
$router->get('/forgot-password/verify', fn() => (new AuthController())->showVerifyResetOtp());
$router->post('/forgot-password/verify', fn() => (new AuthController())->verifyResetOtp());
$router->post('/forgot-password/resend', fn() => (new AuthController())->resendResetOtp());
$router->get('/reset-password', fn() => (new AuthController())->showResetPassword());
$router->get('/reset-password/{token}', fn($token) => (new AuthController())->showResetPassword($token));
$router->post('/reset-password', fn() => (new AuthController())->resetPassword());

// ==========================================
// LMS ADMIN ROUTES (Quản trị hệ thống)
// ==========================================
$router->get('/admin/login', fn() => (new AuthController())->showAdminLogin());
$router->post('/admin/login', fn() => (new AuthController())->adminLogin());
$router->get('/admin', admin(fn() => (new AdminLmsController())->dashboard()));
$router->get('/admin/dashboard', admin(fn() => (new AdminLmsController())->dashboard()));
$router->get('/admin/lms', admin(fn() => (new AdminLmsController())->dashboard()));
$router->get('/admin/lms/dashboard', admin(fn() => (new AdminLmsController())->dashboard()));

// Quản trị Khóa học, Chương, Bài giảng & Tài liệu
$router->get('/admin/lms/courses', admin(fn() => (new AdminLmsController())->courses()));
$router->get('/admin/lms/courses/create', admin(fn() => (new AdminLmsController())->createCourse()));
$router->post('/admin/lms/courses/store', admin(fn() => (new AdminLmsController())->storeCourse()));
$router->get('/admin/lms/courses/{id}/edit', admin(fn($id) => (new AdminLmsController())->editCourse((int)$id)));
$router->post('/admin/lms/courses/{id}/update', admin(fn($id) => (new AdminLmsController())->updateCourse((int)$id)));
$router->post('/admin/lms/courses/{id}/delete', admin(fn($id) => (new AdminLmsController())->deleteCourse((int)$id)));
$router->get('/admin/lms/courses/{id}/curriculum', admin(fn($id) => (new AdminLmsController())->curriculum((int)$id)));
$router->post('/admin/lms/courses/{id}/sections', admin(fn($id) => (new AdminLmsController())->storeSection((int)$id)));
$router->post('/admin/lms/courses/{id}/sections/{sid}/delete', admin(fn($id, $sid) => (new AdminLmsController())->deleteSection((int)$id, (int)$sid)));
$router->post('/admin/lms/courses/{id}/lessons', admin(fn($id) => (new AdminLmsController())->storeLesson((int)$id)));
$router->post('/admin/lms/courses/{id}/lessons/{lid}/delete', admin(fn($id, $lid) => (new AdminLmsController())->deleteLesson((int)$id, (int)$lid)));
$router->post('/admin/lms/courses/{id}/lessons/{lid}/materials', admin(fn($id, $lid) => (new AdminLmsController())->storeMaterial((int)$id, (int)$lid)));

// Quản trị Đề thi & Ngân hàng câu hỏi
$router->get('/admin/lms/quizzes', admin(fn() => (new AdminLmsController())->quizzes()));
$router->get('/admin/lms/quizzes/{id}/questions', admin(fn($id) => (new AdminLmsController())->quizQuestions((int)$id)));
$router->post('/admin/lms/quizzes/{id}/questions', admin(fn($id) => (new AdminLmsController())->storeQuestion((int)$id)));
$router->post('/admin/lms/quizzes/{id}/questions/{qid}/delete', admin(fn($id, $qid) => (new AdminLmsController())->deleteQuestion((int)$id, (int)$qid)));

// Báo cáo kết quả thi
$router->get('/admin/lms/quiz-results', admin(fn() => (new AdminLmsController())->quizResults()));
$router->get('/admin/lms/quiz-results/{id}', admin(fn($id) => (new AdminLmsController())->quizResultDetail((int)$id)));

// Học viên, Giảng viên & Chứng chỉ
$router->get('/admin/lms/students', admin(fn() => (new AdminLmsController())->students()));
$router->get('/admin/lms/instructors', admin(fn() => (new AdminLmsController())->instructors()));
$router->get('/admin/lms/certificates', admin(fn() => (new AdminLmsController())->certificates()));
$router->post('/admin/lms/certificates/{id}/toggle', admin(fn($id) => (new AdminLmsController())->toggleCertificate((int)$id)));
$router->post('/admin/lms/certificates/issue', admin(fn() => (new AdminLmsController())->issueCertificateManual()));

// Quản lý người dùng
$router->get('/admin/users', admin(fn() => (new AdminUserController())->index()));
$router->get('/admin/users/create', admin(fn() => (new AdminUserController())->createForm()));
$router->post('/admin/users', admin(fn() => (new AdminUserController())->store()));
$router->get('/admin/users/{id}/edit', admin(fn($id) => (new AdminUserController())->editForm((int) $id)));
$router->post('/admin/users/{id}/update', admin(fn($id) => (new AdminUserController())->update((int) $id)));
$router->post('/admin/users/{id}/delete', admin(fn($id) => (new AdminUserController())->destroy((int) $id)));

// Quản lý Đơn hàng khóa học
$router->get('/admin/orders', admin(fn() => (new \App\Controllers\Admin\OrderController())->index()));
$router->get('/admin/orders/{id}', admin(fn($id) => (new \App\Controllers\Admin\OrderController())->show((int) $id)));
$router->post('/admin/orders/{id}/status', admin(fn($id) => (new \App\Controllers\Admin\OrderController())->updateStatus((int) $id)));
$router->get('/admin/orders/{id}/invoice', admin(fn($id) => (new \App\Controllers\Admin\OrderController())->invoice((int) $id)));

// API Migration CSDL LMS
$router->get('/api/lms/setup', function () {
    header('Content-Type: application/json; charset=UTF-8');
    try {
        $logs = \App\LmsMigrator::run();
        $seedLogs = \App\Models\LmsCourseSeeder::seed();
        $pdo = \App\DB::connection();
        $courses = $pdo->query("SELECT id, title, thumbnail, price, sale_price FROM courses")->fetchAll(\PDO::FETCH_ASSOC);

        echo json_encode([
            'ok' => true,
            'message' => 'LMS Database migration and course seeding executed successfully!',
            'migration_logs' => $logs,
            'seed_logs' => $seedLogs,
            'courses' => $courses,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } catch (\Throwable $e) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
    }
});
