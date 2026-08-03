<?php

namespace App\Controllers\Api;

use App\Models\User;
use App\DB;
use App\Mailer;
use App\JwtService;

class AuthController
{
    private function getJsonInput(): array
    {
        $input = json_decode(file_get_contents('php://input'), true);
        return is_array($input) ? $input : $_POST;
    }

    private function sendResponse(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    private function generateOtp(): string
    {
        $length = (int) config('otp.length', 6);
        $min = (int) str_pad('1', $length, '0');
        $max = (int) str_pad('9', $length, '9');
        return (string) random_int($min, $max);
    }
    
    public function getAuthenticatedUser(): ?array
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        if (empty($authHeader) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }
        
        if (empty($authHeader) || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }
        
        $token = substr($authHeader, 7);
        $decoded = JwtService::decode($token);
        if (!$decoded || empty($decoded['sub'])) {
            return null;
        }
        
        $userModel = new User();
        return $userModel->find((int) $decoded['sub']);
    }

    public function register(): void
    {
        $input = $this->getJsonInput();
        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $phone = trim($input['phone'] ?? '');
        $password = $input['password'] ?? '';
        $confirm = $input['password_confirmation'] ?? '';

        if ($name === '' || $email === '' || $password === '') {
            $this->sendResponse(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin: name, email, password.'], 400);
        }

        if ($password !== $confirm) {
            $this->sendResponse(['success' => false, 'message' => 'Mật khẩu xác nhận không khớp.'], 400);
        }

        if (!is_gmail($email)) {
            $this->sendResponse(['success' => false, 'message' => 'Chỉ chấp nhận email Gmail hợp lệ (@gmail.com).'], 400);
        }

        if (strlen($password) < 6) {
            $this->sendResponse(['success' => false, 'message' => 'Mật khẩu tối thiểu phải từ 6 ký tự.'], 400);
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            $this->sendResponse(['success' => false, 'message' => 'Email này đã được sử dụng.'], 400);
        }

        $code = $this->generateOtp();
        $expire = (int) config('otp.expire_minutes', 5) * 60;

        $db = DB::connection();
        
        // Clear existing register OTPs for this email to prevent duplicate pending registers
        $delStmt = $db->prepare('DELETE FROM api_otps WHERE email = ? AND action = ?');
        $delStmt->execute([$email, 'register']);
        
        $stmt = $db->prepare('INSERT INTO api_otps (email, code, action, payload, expires_at) VALUES (?, ?, ?, ?, ?)');
        $payload = json_encode([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => $password,
        ]);
        $stmt->execute([$email, $code, 'register', $payload, time() + $expire]);

        $appName = config('app_name', 'Shop Hades');
        $subject = "[{$appName}] Mã OTP đăng ký tài khoản của bạn";
        $body = <<<HTML
        <div style="font-family:Arial,sans-serif;max-width:480px;margin:0 auto;padding:24px;border:1px solid #eee;border-radius:12px;">
            <h2 style="color:#d70018;margin:0 0 12px;">{$appName}</h2>
            <p>Mã OTP xác thực đăng ký tài khoản của bạn:</p>
            <p style="font-size:32px;font-weight:bold;letter-spacing:8px;color:#222;">{$code}</p>
            <p style="color:#666;font-size:13px;">Mã có hiệu lực trong {$expire} giây. Không chia sẻ mã này với ai.</p>
        </div>
        HTML;

        if (!Mailer::send($email, $subject, $body)) {
            $this->sendResponse(['success' => false, 'message' => 'Không gửi được email OTP: ' . Mailer::getLastError()], 500);
        }

        $this->sendResponse([
            'success' => true,
            'message' => 'Mã OTP đã được gửi tới Gmail ' . $email . '. Vui lòng xác thực để hoàn tất.',
            'email' => $email,
        ]);
    }

    public function verifyRegisterOtp(): void
    {
        $input = $this->getJsonInput();
        $email = trim($input['email'] ?? '');
        $code = trim($input['otp'] ?? '');

        if ($email === '' || $code === '') {
            $this->sendResponse(['success' => false, 'message' => 'Vui lòng cung cấp đầy đủ email và otp.'], 400);
        }

        $db = DB::connection();
        $stmt = $db->prepare('SELECT * FROM api_otps WHERE email = ? AND code = ? AND action = ? AND expires_at >= ? LIMIT 1');
        $stmt->execute([$email, $code, 'register', time()]);
        $otpRecord = $stmt->fetch();

        if (!$otpRecord) {
            $this->sendResponse(['success' => false, 'message' => 'Mã OTP không đúng hoặc đã hết hạn.'], 400);
        }

        $payload = json_decode($otpRecord['payload'], true);
        if (!$payload) {
            $this->sendResponse(['success' => false, 'message' => 'Dữ liệu OTP không hợp lệ.'], 400);
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            $this->sendResponse(['success' => false, 'message' => 'Email đã đăng ký trong thời gian chờ OTP.'], 400);
        }

        $user = $userModel->create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'phone' => $payload['phone'] ?? '',
            'password' => $payload['password'],
            'role' => 'customer',
        ]);

        $delStmt = $db->prepare('DELETE FROM api_otps WHERE email = ? AND action = ?');
        $delStmt->execute([$email, 'register']);

        $jwtPayload = [
            'iss' => config('base_url', 'http://localhost'),
            'sub' => $user['id'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + (int) config('jwt.expire_seconds', 86400),
        ];
        $token = JwtService::encode($jwtPayload);

        unset($user['password']);
        $this->sendResponse([
            'success' => true,
            'message' => 'Đăng ký tài khoản và xác thực thành công!',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function login(): void
    {
        $input = $this->getJsonInput();
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';

        if ($email === '' || $password === '') {
            $this->sendResponse(['success' => false, 'message' => 'Vui lòng cung cấp email và password.'], 400);
        }

        $userModel = new User();
        $user = $userModel->verify($email, $password);

        if (!$user) {
            $this->sendResponse(['success' => false, 'message' => 'Email hoặc mật khẩu không chính xác.'], 401);
        }

        if (!is_gmail($email)) {
            $this->sendResponse(['success' => false, 'message' => 'Chỉ chấp nhận đăng nhập bằng Gmail (@gmail.com).'], 400);
        }

        $jwtPayload = [
            'iss' => config('base_url', 'http://localhost'),
            'sub' => $user['id'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + (int) config('jwt.expire_seconds', 86400),
        ];
        $token = JwtService::encode($jwtPayload);

        unset($user['password']);
        $this->sendResponse([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function verifyLoginOtp(): void
    {
        $input = $this->getJsonInput();
        $email = trim($input['email'] ?? '');
        $code = trim($input['otp'] ?? '');

        if ($email === '' || $code === '') {
            $this->sendResponse(['success' => false, 'message' => 'Vui lòng cung cấp đầy đủ email và otp.'], 400);
        }

        $db = DB::connection();
        $stmt = $db->prepare('SELECT * FROM api_otps WHERE email = ? AND code = ? AND action = ? AND expires_at >= ? LIMIT 1');
        $stmt->execute([$email, $code, 'login', time()]);
        $otpRecord = $stmt->fetch();

        if (!$otpRecord) {
            $this->sendResponse(['success' => false, 'message' => 'Mã OTP không đúng hoặc đã hết hạn.'], 400);
        }

        $payload = json_decode($otpRecord['payload'], true);
        $userId = $payload['user_id'] ?? null;

        if (!$userId) {
            $this->sendResponse(['success' => false, 'message' => 'Dữ liệu OTP không hợp lệ.'], 400);
        }

        $userModel = new User();
        $user = $userModel->find((int) $userId);

        if (!$user) {
            $this->sendResponse(['success' => false, 'message' => 'Tài khoản người dùng không còn tồn tại.'], 404);
        }

        $delStmt = $db->prepare('DELETE FROM api_otps WHERE email = ? AND action = ?');
        $delStmt->execute([$email, 'login']);

        $jwtPayload = [
            'iss' => config('base_url', 'http://localhost'),
            'sub' => $user['id'],
            'role' => $user['role'],
            'iat' => time(),
            'exp' => time() + (int) config('jwt.expire_seconds', 86400),
        ];
        $token = JwtService::encode($jwtPayload);

        unset($user['password']);
        $this->sendResponse([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function resendOtp(): void
    {
        $input = $this->getJsonInput();
        $email = trim($input['email'] ?? '');
        $action = trim($input['action'] ?? 'login');

        if ($email === '') {
            $this->sendResponse(['success' => false, 'message' => 'Vui lòng cung cấp email.'], 400);
        }

        if (!in_array($action, ['login', 'register'])) {
            $this->sendResponse(['success' => false, 'message' => 'Hành động (action) không hợp lệ.'], 400);
        }

        $db = DB::connection();
        $stmt = $db->prepare('SELECT * FROM api_otps WHERE email = ? AND action = ? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$email, $action]);
        $latest = $stmt->fetch();

        if ($latest) {
            $elapsed = time() - ($latest['expires_at'] - (int) config('otp.expire_minutes', 5) * 60);
            $wait = (int) config('otp.resend_seconds', 60);
            if ($elapsed < $wait) {
                $this->sendResponse([
                    'success' => false, 
                    'message' => 'Vui lòng đợi ' . ($wait - $elapsed) . ' giây trước khi gửi lại OTP.'
                ], 429);
            }
        }

        $code = $this->generateOtp();
        $expire = (int) config('otp.expire_minutes', 5) * 60;

        $delStmt = $db->prepare('DELETE FROM api_otps WHERE email = ? AND action = ?');
        $delStmt->execute([$email, $action]);

        $payload = $latest ? $latest['payload'] : null;
        if ($action === 'register' && !$payload) {
            $this->sendResponse(['success' => false, 'message' => 'Không tìm thấy phiên đăng ký. Vui lòng đăng ký lại.'], 400);
        }

        $insStmt = $db->prepare('INSERT INTO api_otps (email, code, action, payload, expires_at) VALUES (?, ?, ?, ?, ?)');
        $insStmt->execute([$email, $code, $action, $payload, time() + $expire]);

        $appName = config('app_name', 'Shop Hades');
        $subject = "[{$appName}] Mã OTP gửi lại của bạn";
        $body = <<<HTML
        <div style="font-family:Arial,sans-serif;max-width:480px;margin:0 auto;padding:24px;border:1px solid #eee;border-radius:12px;">
            <h2 style="color:#d70018;margin:0 0 12px;">{$appName}</h2>
            <p>Mã OTP mới của bạn:</p>
            <p style="font-size:32px;font-weight:bold;letter-spacing:8px;color:#222;">{$code}</p>
            <p style="color:#666;font-size:13px;">Mã có hiệu lực trong {$expire} giây.</p>
        </div>
        HTML;

        if (!Mailer::send($email, $subject, $body)) {
            $this->sendResponse(['success' => false, 'message' => 'Không gửi được email OTP: ' . Mailer::getLastError()], 500);
        }

        $this->sendResponse([
            'success' => true,
            'message' => 'Mã OTP mới đã được gửi tới Gmail của bạn.',
            'email' => $email,
        ]);
    }

    public function profile(): void
    {
        $user = $this->getAuthenticatedUser();
        if (!$user) {
            $this->sendResponse(['success' => false, 'message' => 'Token không hợp lệ hoặc đã hết hạn.'], 401);
        }

        unset($user['password']);
        $this->sendResponse([
            'success' => true,
            'user' => $user,
        ]);
    }
}
