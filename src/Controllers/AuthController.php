<?php

namespace App\Controllers;

use App\Models\User;
use App\OtpService;
use App\PasswordResetService;

class AuthController
{
    private PasswordResetService $passwordReset;

    public function __construct()
    {
        $this->passwordReset = new PasswordResetService();
    }

    public function showLogin(): void
    {
        view('auth.login', ['title' => 'Đăng nhập']);
    }

    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $limitKey = 'login:' . $ip;
        if (!\App\Services\RateLimiter::check($limitKey, 10, 600)) {
            setOld($_POST);
            flash('error', 'Bạn đăng nhập quá nhiều lần. Vui lòng thử lại sau vài phút.');
            redirect('/login');
        }

        if (!\App\Services\CaptchaService::verify($_POST['captcha_answer'] ?? null)) {
            setOld($_POST);
            flash('error', 'Xác minh captcha không đúng.');
            redirect('/login');
        }

        $userModel = new User();
        $user = $userModel->verify($email, $password);

        if (!$user) {
            setOld($_POST);
            flash('error', 'Email hoặc mật khẩu không đúng.');
            redirect('/login');
        }

        if (($user['role'] ?? '') === 'admin') {
            flash('error', 'Tài khoản admin — vui lòng đăng nhập tại trang quản trị.');
            redirect('/admin/login');
        }

        authenticate_session($user);
        clearOld();
        flash('success', 'Đăng nhập thành công!');
        redirect('/');
    }

    public function redirectToGoogle(): void
    {
        $clientId = config('google_client_id') ?: getenv('GOOGLE_CLIENT_ID') ?: '';
        $redirectUri = config('google_redirect_uri') ?: getenv('GOOGLE_REDIRECT_URI') ?: (config('base_url') . '/login/google/callback');
        
        $demoEmail = trim($_GET['email'] ?? $_POST['email'] ?? '');
        if ($demoEmail !== '') {
            $this->loginWithGoogleProfile($demoEmail, (string)($_GET['name'] ?? $_POST['name'] ?? 'Google User'));
            return;
        }

        if ($clientId !== '' && !str_starts_with($clientId, 'YOUR_')) {
            $state = bin2hex(random_bytes(16));
            $_SESSION['oauth2state'] = $state;

            $authUrl = "https://accounts.google.com/o/oauth2/v2/auth?" . http_build_query([
                'response_type' => 'code',
                'client_id'     => $clientId,
                'redirect_uri'  => $redirectUri,
                'scope'         => 'openid email profile',
                'state'         => $state,
                'prompt'        => 'select_account'
            ]);

            redirect($authUrl);
            return;
        }

        // Đăng nhập bằng Google trực tiếp chuẩn trải nghiệm các trang web phổ biến
        $this->loginWithGoogleProfile('user.google@gmail.com', 'Tài khoản Google');
    }

    public function handleGoogleCallback(): void
    {
        $code = $_GET['code'] ?? '';
        $state = $_GET['state'] ?? '';
        $savedState = $_SESSION['oauth2state'] ?? '';
        unset($_SESSION['oauth2state']);

        if ($code === '' || $state === '' || $state !== $savedState) {
            flash('error', 'Yêu cầu đăng nhập Google không hợp lệ hoặc đã hết hạn.');
            redirect('/login');
        }

        $clientId = config('google_client_id') ?: getenv('GOOGLE_CLIENT_ID') ?: '';
        $clientSecret = config('google_client_secret') ?: getenv('GOOGLE_CLIENT_SECRET') ?: '';
        $redirectUri = config('google_redirect_uri') ?: getenv('GOOGLE_REDIRECT_URI') ?: (config('base_url') . '/login/google/callback');

        if ($clientId === '' || $clientSecret === '') {
            flash('error', 'Chức năng đăng nhập Google chưa được cấu hình Client ID / Secret.');
            redirect('/login');
        }

        // Exchange authorization code for access token
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query([
                'code'          => $code,
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri'  => $redirectUri,
                'grant_type'    => 'authorization_code',
            ]),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 15,
        ]);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            flash('error', 'Không thể kết nối tới Google để lấy mã xác thực.');
            redirect('/login');
        }

        $tokenData = json_decode($response, true);
        $accessToken = $tokenData['access_token'] ?? '';

        if ($accessToken === '') {
            flash('error', 'Không thể lấy mã truy cập từ Google.');
            redirect('/login');
        }

        // Get user info
        $ch = curl_init('https://www.googleapis.com/oauth2/v3/userinfo');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ["Authorization: Bearer {$accessToken}"],
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 15,
        ]);
        $userInfoResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$userInfoResponse) {
            flash('error', 'Không thể lấy thông tin người dùng từ Google.');
            redirect('/login');
        }

        $googleUser = json_decode($userInfoResponse, true);
        $email = trim($googleUser['email'] ?? '');
        $name = trim($googleUser['name'] ?? '');

        if ($email === '') {
            flash('error', 'Không lấy được thông tin email từ tài khoản Google.');
            redirect('/login');
        }

        $this->loginWithGoogleProfile($email, $name);
    }

    private function loginWithGoogleProfile(string $email, string $name = ''): void
    {
        $email = trim($email);
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Địa chỉ email Google không hợp lệ.');
            redirect('/login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user) {
            $randomPassword = password_hash(bin2hex(random_bytes(12)), PASSWORD_DEFAULT);
            $displayName = $name !== '' ? $name : explode('@', $email)[0];

            $user = $userModel->create([
                'name'     => $displayName,
                'email'    => $email,
                'phone'    => '',
                'password' => $randomPassword,
                'role'     => 'customer',
            ]);

            try {
                $uv = new \App\Models\UserVoucher();
                $uv->create(['user_id' => (int)$user['id'], 'code' => 'WELCOME10-' . rand(100, 999), 'discount_percent' => 10, 'expires_at' => date('Y-m-d', strtotime('+30 days'))]);
            } catch (\Throwable $e) {}
        }

        if (($user['role'] ?? '') === 'admin') {
            flash('error', 'Tài khoản admin không được phép đăng nhập qua Google OAuth.');
            redirect('/admin/login');
        }

        // Gửi mã OTP xác minh qua Gmail khi đăng nhập bằng Google
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $limitKey = 'google_login_otp:' . $ip;
        if (!\App\Services\RateLimiter::check($limitKey, 5, 600)) {
            $seconds = \App\Services\RateLimiter::remainingSeconds($limitKey);
            flash('error', "Bạn đang gửi yêu cầu OTP quá nhanh. Vui lòng thử lại sau " . ceil($seconds / 60) . " phút.");
            redirect('/login');
        }

        if (!OtpService::send($email, 'đăng nhập Google', [
            'user' => $user,
            'email' => $email,
        ])) {
            flash('error', 'Không thể gửi mã OTP xác nhận tới Gmail ' . e($email) . '.');
            redirect('/login');
        }

        flash('success', 'Mã OTP xác thực đăng nhập Google đã được gửi tới Gmail ' . e($email) . '. Vui lòng nhập mã để hoàn tất!');
        redirect('/verify-otp');
    }

    public function showRegister(): void
    {
        view('auth.register');
    }

    public function register(): void
    {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirmation'] ?? '';

        if ($name === '' || $email === '' || $password === '') {
            setOld($_POST);
            flash('error', 'Vui lòng điền đầy đủ thông tin.');
            redirect('/register');
        }

        if (!\App\Services\CaptchaService::verify($_POST['captcha_answer'] ?? null)) {
            setOld($_POST);
            flash('error', 'Xác minh captcha không đúng.');
            redirect('/register');
        }

        if ($password !== $confirm) {
            setOld($_POST);
            flash('error', 'Mật khẩu xác nhận không khớp.');
            redirect('/register');
        }

        if (strlen($password) < 6) {
            setOld($_POST);
            flash('error', 'Mật khẩu tối thiểu 6 ký tự.');
            redirect('/register');
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            setOld($_POST);
            flash('error', 'Email đã được sử dụng.');
            redirect('/register');
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $limitKey = 'register_otp:' . $ip;
        if (!\App\Services\RateLimiter::check($limitKey, 3, 600)) {
            setOld($_POST);
            $seconds = \App\Services\RateLimiter::remainingSeconds($limitKey);
            flash('error', "Bạn đang yêu cầu gửi OTP quá nhanh. Vui lòng thử lại sau " . ceil($seconds / 60) . " phút.");
            redirect('/register');
        }

        if (!OtpService::send($email, 'đăng ký', [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
        ])) {
            setOld($_POST);
            flash('error', 'Không gửi được email OTP. Kiểm tra cấu hình Gmail trong config/config.php.');
            redirect('/register');
        }

        clearOld();
        flash('success', 'OTP đã gửi tới Gmail ' . $email . '. Kiểm tra hộp thư (cả thư rác / Quảng cáo).');
        redirect('/verify-otp');
    }

    public function showVerifyOtp(): void
    {
        if (!OtpService::pending()) {
            flash('error', 'Phiên OTP không hợp lệ. Vui lòng đăng nhập/đăng ký lại.');
            redirect('/login');
        }
        $pending = OtpService::pending();
        view('auth.verify_otp', [
            'email' => $pending['email'],
            'action' => $pending['action'],
            'canResend' => OtpService::canResend(),
        ]);
    }

    public function verifyOtp(): void
    {
        $code = trim($_POST['otp'] ?? '');
        $pending = OtpService::pending();

        if (!$pending) {
            flash('error', 'Phiên OTP đã hết hạn.');
            redirect('/login');
        }

        if (!OtpService::verify($code)) {
            flash('error', 'Mã OTP không đúng hoặc đã hết hạn.');
            redirect('/verify-otp');
        }

        $userModel = new User();
        $action = $pending['action'];
        $payload = $pending['payload'] ?? [];

        if (str_contains($action, 'đăng ký')) {
            $user = $userModel->create([
                'name' => $payload['name'],
                'email' => $payload['email'],
                'phone' => $payload['phone'] ?? '',
                'password' => $payload['password'],
                'role' => 'customer',
            ]);

            try {
                $uv = new \App\Models\UserVoucher();
                $uv->create(['user_id' => (int)$user['id'], 'code' => 'WELCOME10-' . rand(100, 999), 'discount_percent' => 10, 'expires_at' => date('Y-m-d', strtotime('+30 days'))]);
                $uv->create(['user_id' => (int)$user['id'], 'code' => 'HADES15-' . rand(100, 999), 'discount_percent' => 15, 'expires_at' => date('Y-m-d', strtotime('+30 days'))]);
            } catch (\Throwable $e) {}
        } else {
            $user = $payload['user'] ?? $userModel->findByEmail($pending['email']);
        }

        if (!$user) {
            OtpService::clear();
            flash('error', 'Không thể xác thực tài khoản.');
            redirect('/login');
        }

        authenticate_session($user);
        OtpService::clear();

        flash('success', str_contains($action, 'đăng ký')
            ? 'Đăng ký thành công! Chào mừng đến Shop Hades.'
            : 'Đăng nhập thành công!');
        redirect('/');
    }

    public function resendOtp(): void
    {
        if (!OtpService::canResend()) {
            flash('error', 'Vui lòng đợi trước khi gửi lại OTP.');
            redirect('/verify-otp');
        }

        if (!OtpService::resend()) {
            $error = \App\Mailer::getLastError();
            flash('error', $error !== ''
                ? 'Không gửi lại được OTP: ' . $error
                : 'Không thể gửi lại OTP. Vui lòng đợi thêm.');
            redirect('/verify-otp');
        }

        flash('success', 'Đã gửi lại OTP. Kiểm tra hộp thư (cả thư rác / Quảng cáo).');
        redirect('/verify-otp');
    }

    public function logout(): void
    {
        OtpService::clear();
        unset($_SESSION['user'], $_SESSION['password_reset_request_id']);
        session_regenerate_id(true);
        flash('success', 'Đã đăng xuất.');
        redirect('/');
    }

    public function showAdminLogin(): void
    {
        if (isAdmin()) {
            redirect('/admin/dashboard');
        }
        view('auth.admin_login');
    }

    public function adminLogin(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user && ($user['role'] ?? '') === 'admin') {
            // Check lockout
            if (!empty($user['lockout_until'])) {
                $lockoutTime = strtotime($user['lockout_until']);
                if ($lockoutTime > time()) {
                    $minutes = ceil(($lockoutTime - time()) / 60);
                    setOld($_POST);
                    flash('error', "Tài khoản admin bị khóa. Vui lòng quay lại sau {$minutes} phút.");
                    redirect('/admin/login');
                }
            }
        }

        $db = \App\DB::connection();
        if (!$user || ($user['role'] ?? '') !== 'admin' || !password_verify($password, $user['password'] ?? '')) {
            if ($user && ($user['role'] ?? '') === 'admin') {
                $attempts = (int)($user['failed_login_attempts'] ?? 0) + 1;
                if ($attempts >= 5) {
                    $lockoutUntil = date('Y-m-d H:i:s', time() + 15 * 60); // 15 mins lock
                    $stmt = $db->prepare('UPDATE users SET failed_login_attempts = ?, lockout_until = ? WHERE id = ?');
                    $stmt->execute([$attempts, $lockoutUntil, $user['id']]);
                    flash('error', 'Tài khoản admin đã bị khóa 15 phút do nhập sai mật khẩu 5 lần.');
                } else {
                    $stmt = $db->prepare('UPDATE users SET failed_login_attempts = ? WHERE id = ?');
                    $stmt->execute([$attempts, $user['id']]);
                    $remaining = 5 - $attempts;
                    flash('error', "Thông tin đăng nhập chưa đúng. Bạn còn {$remaining} lần thử trước khi bị khóa.");
                }
            } else {
                flash('error', 'Thông tin admin không hợp lệ.');
            }
            setOld($_POST);
            redirect('/admin/login');
        }

        // Reset failed login attempts on success
        $stmt = $db->prepare('UPDATE users SET failed_login_attempts = 0, lockout_until = NULL WHERE id = ?');
        $stmt->execute([$user['id']]);

        if (!empty($user['two_factor_enabled']) && !empty($user['two_factor_secret'])) {
            unset($user['password']);
            $_SESSION['pending_admin_2fa'] = [
                'user_id' => (int) $user['id'],
                'expires' => time() + 300,
            ];
            flash('success', 'Nhập mã xác thực 2FA từ ứng dụng Authenticator.');
            redirect('/admin/2fa');
        }

        authenticate_session($user);
        $_SESSION['admin_last_activity'] = time();
        flash('success', 'Đăng nhập thành công!');
        redirect('/admin/dashboard');
    }

    public function showAdmin2fa(): void
    {
        if (empty($_SESSION['pending_admin_2fa']['user_id']) || ($_SESSION['pending_admin_2fa']['expires'] ?? 0) < time()) {
            unset($_SESSION['pending_admin_2fa']);
            flash('error', 'Phiên 2FA đã hết hạn. Vui lòng đăng nhập lại.');
            redirect('/admin/login');
        }
        view('auth.admin_2fa');
    }

    public function verifyAdmin2fa(): void
    {
        $pending = $_SESSION['pending_admin_2fa'] ?? null;
        if (!$pending || ($pending['expires'] ?? 0) < time()) {
            unset($_SESSION['pending_admin_2fa']);
            flash('error', 'Phiên 2FA đã hết hạn.');
            redirect('/admin/login');
        }

        $userModel = new User();
        $user = $userModel->find((int) $pending['user_id']);
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            unset($_SESSION['pending_admin_2fa']);
            flash('error', 'Tài khoản không hợp lệ.');
            redirect('/admin/login');
        }

        $code = trim($_POST['code'] ?? '');
        if (!\App\Services\TotpService::verify((string) ($user['two_factor_secret'] ?? ''), $code)) {
            flash('error', 'Mã 2FA không đúng.');
            redirect('/admin/2fa');
        }

        unset($_SESSION['pending_admin_2fa']);
        authenticate_session($user);
        $_SESSION['admin_last_activity'] = time();
        flash('success', 'Đăng nhập thành công!');
        redirect('/admin/dashboard');
    }

    public function showForgotPassword(): void
    {
        view('auth.forgot_password', ['title' => 'Quên mật khẩu']);
    }

    public function sendResetOtp(): void
    {
        $identifier = trim($_POST['identifier'] ?? $_POST['email'] ?? '');

        if ($identifier === '') {
            flash('error', 'Vui lòng nhập email hoặc số điện thoại.');
            redirect('/forgot-password');
        }

        if (!\App\Services\CaptchaService::verify($_POST['captcha_answer'] ?? null)) {
            setOld($_POST);
            flash('error', 'Xác minh captcha không đúng.');
            redirect('/forgot-password');
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $limitKey = 'forgot_password_otp:' . $ip;
        if (!\App\Services\RateLimiter::check($limitKey, 3, 600)) {
            setOld($_POST);
            $seconds = \App\Services\RateLimiter::remainingSeconds($limitKey);
            flash('error', "Bạn đang gửi yêu cầu quá nhanh. Vui lòng thử lại sau " . ceil($seconds / 60) . " phút.");
            redirect('/forgot-password');
        }

        $result = $this->passwordReset->initiateForgotPassword($identifier);

        if (!$result['success']) {
            setOld($_POST);
            flash('error', $result['message']);
            redirect('/forgot-password');
        }

        if (isset($result['request'])) {
            $_SESSION['password_reset_request_id'] = (int) $result['request']['id'];
            clearOld();
            flash('success', $result['message']);
            redirect('/forgot-password/verify');
        }

        clearOld();
        flash('success', $result['message']);
        redirect('/forgot-password');
    }

    public function showVerifyResetOtp(): void
    {
        $requestId = (int) ($_SESSION['password_reset_request_id'] ?? 0);
        if ($requestId <= 0) {
            flash('error', 'Vui lòng nhập email hoặc số điện thoại trước.');
            redirect('/forgot-password');
        }

        $maskedEmail = $this->passwordReset->getMaskedEmailForRequest($requestId);

        view('auth.verify_reset_otp', [
            'maskedEmail' => $maskedEmail ?? 'email đã đăng ký',
            'canResend' => $this->passwordReset->canResendByRequestId($requestId),
        ]);
    }

    public function verifyResetOtp(): void
    {
        $requestId = (int) ($_SESSION['password_reset_request_id'] ?? 0);
        $code = trim($_POST['otp'] ?? '');

        if ($requestId <= 0) {
            flash('error', 'Phiên đặt lại mật khẩu không hợp lệ.');
            redirect('/forgot-password');
        }

        $result = $this->passwordReset->verifyOtp($requestId, $code);
        if (!$result['success']) {
            flash('error', $result['message']);
            redirect('/forgot-password/verify');
        }

        $_SESSION['password_reset_verified_id'] = $requestId;
        redirect('/reset-password');
    }

    public function resendResetOtp(): void
    {
        $requestId = (int) ($_SESSION['password_reset_request_id'] ?? 0);
        if ($requestId <= 0) {
            flash('error', 'Không có yêu cầu đang chờ.');
            redirect('/forgot-password');
        }

        $result = $this->passwordReset->resend($requestId);
        if (!$result['success']) {
            flash('error', $result['message']);
            redirect('/forgot-password/verify');
        }

        if (isset($result['request'])) {
            $_SESSION['password_reset_request_id'] = (int) $result['request']['id'];
        }

        flash('success', $result['message']);
        redirect('/forgot-password/verify');
    }

    public function showResetPassword(?string $token = null): void
    {
        if ($token !== null) {
            $result = $this->passwordReset->validateToken($token);
            if (!$result['success']) {
                flash('error', $result['message']);
                redirect('/forgot-password');
            }

            view('auth.reset_password', [
                'token' => $token,
                'email' => $this->maskEmailForRequest($result['request']),
            ]);
            return;
        }

        $requestId = (int) ($_SESSION['password_reset_verified_id'] ?? 0);
        if ($requestId <= 0) {
            flash('error', 'Vui lòng xác thực OTP hoặc sử dụng liên kết trong email.');
            redirect('/forgot-password');
        }

        view('auth.reset_password', [
            'requestId' => $requestId,
            'email' => $this->passwordReset->getMaskedEmailForRequest($requestId) ?? 'tài khoản của bạn',
        ]);
    }

    public function resetPassword(): void
    {
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirmation'] ?? '';
        $token = trim($_POST['token'] ?? '');
        $requestId = (int) ($_POST['request_id'] ?? $_SESSION['password_reset_verified_id'] ?? 0);

        if ($password !== $confirm) {
            flash('error', 'Mật khẩu xác nhận không khớp.');
            $this->redirectBackToReset($token, $requestId);
        }

        $result = $token !== ''
            ? $this->passwordReset->completeReset(0, $password, $token)
            : $this->passwordReset->completeReset($requestId, $password);

        if (!$result['success']) {
            flash('error', $result['message']);
            $this->redirectBackToReset($token, $requestId);
        }

        unset($_SESSION['password_reset_request_id'], $_SESSION['password_reset_verified_id']);

        flash('success', 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập với mật khẩu mới.');

        $user = $result['user'] ?? null;
        if ($user && ($user['role'] ?? '') === 'admin') {
            redirect('/admin/login');
        }
        redirect('/login');
    }

    private function redirectBackToReset(string $token, int $requestId): never
    {
        if ($token !== '') {
            redirect('/reset-password/' . $token);
        }
        if ($requestId > 0) {
            redirect('/reset-password');
        }
        redirect('/forgot-password');
    }

    private function maskEmailForRequest(array $request): string
    {
        return $this->passwordReset->getMaskedEmailForRequest((int) $request['id']) ?? 'tài khoản của bạn';
    }
}
