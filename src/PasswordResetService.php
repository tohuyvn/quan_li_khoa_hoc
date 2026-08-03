<?php

namespace App;

use App\Models\PasswordResetRequest;
use App\Models\User;

class PasswordResetService
{
    private PasswordResetRequest $requests;
    private User $users;

    public function __construct()
    {
        $this->requests = new PasswordResetRequest();
        $this->users = new User();
    }

    /**
     * @return array{success: bool, message: string, request?: array, otp?: string}
     */
    public function initiateForgotPassword(string $identifier): array
    {
        $user = $this->resolveUser($identifier);
        $genericMessage = 'Nếu tài khoản tồn tại, chúng tôi đã gửi hướng dẫn đặt lại mật khẩu tới email đăng ký.';

        if (!$user) {
            return ['success' => true, 'message' => $genericMessage];
        }

        if (!$this->canSendRequest((int) $user['id'])) {
            return [
                'success' => false,
                'message' => 'Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau.',
            ];
        }

        $request = $this->createRequest(
            (int) $user['id'],
            PasswordResetRequest::TYPE_FORGOT_PASSWORD,
            PasswordResetRequest::CREATED_BY_USER,
            (int) $user['id']
        );

        if (!$request['email_sent']) {
            return [
                'success' => false,
                'message' => 'Không gửi được email. Kiểm tra cấu hình mail hoặc thử lại sau.',
            ];
        }

        return [
            'success' => true,
            'message' => $genericMessage,
            'request' => $request['request'],
        ];
    }

    /**
     * @return array{success: bool, message: string, request?: array}
     */
    public function initiateAdminReset(int $userId, int $adminId): array
    {
        $user = $this->users->find($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'Không tìm thấy người dùng.'];
        }

        if (!$this->canSendRequest($userId)) {
            return [
                'success' => false,
                'message' => 'Đã gửi quá nhiều yêu cầu cho người dùng này. Vui lòng đợi.',
            ];
        }

        $result = $this->createRequest(
            $userId,
            PasswordResetRequest::TYPE_ADMIN_RESET,
            PasswordResetRequest::CREATED_BY_ADMIN,
            $adminId
        );

        if (!$result['email_sent']) {
            return [
                'success' => false,
                'message' => 'Không gửi được email đặt lại mật khẩu.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Đã gửi email đặt lại mật khẩu tới ' . $user['email'] . '.',
            'request' => $result['request'],
        ];
    }

    /**
     * @return array{success: bool, message: string, request?: array}
     */
    public function verifyOtp(int $requestId, string $otp): array
    {
        $request = $this->requests->find($requestId);
        if (!$request || $request['status'] !== PasswordResetRequest::STATUS_PENDING) {
            return ['success' => false, 'message' => 'Yêu cầu không hợp lệ hoặc đã hết hạn.'];
        }

        if ($this->isOtpExpired($request)) {
            $this->requests->markExpired($requestId);
            return ['success' => false, 'message' => 'Mã OTP đã hết hạn. Vui lòng yêu cầu mã mới.'];
        }

        $maxAttempts = (int) config('password_reset.max_verify_attempts', 5);
        if ($request['verify_attempts'] >= $maxAttempts) {
            $this->requests->markExpired($requestId);
            return ['success' => false, 'message' => 'Đã nhập sai quá số lần cho phép. Vui lòng yêu cầu mã mới.'];
        }

        if (!password_verify(trim($otp), (string) $request['otp_hash'])) {
            $this->requests->incrementVerifyAttempts($requestId);
            $remaining = $maxAttempts - $request['verify_attempts'] - 1;
            return [
                'success' => false,
                'message' => $remaining > 0
                    ? "Mã OTP không đúng. Còn {$remaining} lần thử."
                    : 'Mã OTP không đúng. Vui lòng yêu cầu mã mới.',
            ];
        }

        return ['success' => true, 'message' => 'Xác thực OTP thành công.', 'request' => $request];
    }

    /**
     * @return array{success: bool, message: string, request?: array}
     */
    public function validateToken(string $token): array
    {
        $request = $this->requests->findByToken($token);
        if (!$request) {
            return ['success' => false, 'message' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã được sử dụng.'];
        }

        if ($this->isTokenExpired($request)) {
            $this->requests->markExpired((int) $request['id']);
            return ['success' => false, 'message' => 'Liên kết đặt lại mật khẩu đã hết hạn.'];
        }

        return ['success' => true, 'message' => 'Token hợp lệ.', 'request' => $request];
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function completeReset(int $requestId, string $newPassword, ?string $token = null): array
    {
        $request = $token !== null
            ? $this->requests->findByToken($token)
            : $this->requests->find($requestId);

        if (!$request || $request['status'] !== PasswordResetRequest::STATUS_PENDING) {
            return ['success' => false, 'message' => 'Yêu cầu đặt lại mật khẩu không hợp lệ.'];
        }

        if ($this->isTokenExpired($request) && $this->isOtpExpired($request)) {
            $this->requests->markExpired((int) $request['id']);
            return ['success' => false, 'message' => 'Yêu cầu đã hết hạn.'];
        }

        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'Mật khẩu tối thiểu 6 ký tự.'];
        }

        $user = $this->users->find((int) $request['user_id']);
        if (!$user) {
            return ['success' => false, 'message' => 'Không tìm thấy tài khoản.'];
        }

        $updated = $this->users->update((int) $user['id'], [
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'role' => $user['role'],
            'password' => $newPassword,
        ]);

        if (!$updated) {
            return ['success' => false, 'message' => 'Không thể cập nhật mật khẩu.'];
        }

        $this->requests->markUsed((int) $request['id']);
        $this->requests->expirePendingForUser((int) $user['id']);

        return ['success' => true, 'message' => 'Đặt lại mật khẩu thành công.', 'user' => $user];
    }

    /**
     * @return array{success: bool, message: string, request?: array}
     */
    public function resend(int $requestId): array
    {
        $request = $this->requests->find($requestId);
        if (!$request || $request['status'] !== PasswordResetRequest::STATUS_PENDING) {
            return ['success' => false, 'message' => 'Không có yêu cầu đang chờ để gửi lại.'];
        }

        if (!$this->canResend($request)) {
            return ['success' => false, 'message' => 'Vui lòng đợi trước khi gửi lại.'];
        }

        if (!$this->canSendRequest((int) $request['user_id'])) {
            return ['success' => false, 'message' => 'Đã vượt giới hạn gửi email trong 1 giờ.'];
        }

        $result = $this->createRequest(
            (int) $request['user_id'],
            $request['request_type'],
            $request['created_by'],
            $request['created_by_id']
        );

        if (!$result['email_sent']) {
            return ['success' => false, 'message' => 'Không gửi lại được email.'];
        }

        return [
            'success' => true,
            'message' => 'Đã gửi lại email đặt lại mật khẩu.',
            'request' => $result['request'],
        ];
    }

    public function getMaskedEmailForRequest(int $requestId): ?string
    {
        $request = $this->requests->find($requestId);
        if (!$request) {
            return null;
        }
        $user = $this->users->find((int) $request['user_id']);

        return $user ? $this->maskEmail($user['email']) : null;
    }

    private function createRequest(
        int $userId,
        string $requestType,
        string $createdBy,
        ?int $createdById
    ): array {
        $this->requests->expirePendingForUser($userId);

        $otp = $this->generateOtp();
        $token = bin2hex(random_bytes(32));
        $otpExpireMinutes = (int) config('password_reset.otp_expire_minutes', 5);
        $tokenExpireMinutes = (int) config('password_reset.token_expire_minutes', 15);
        $expireMinutes = max($otpExpireMinutes, $tokenExpireMinutes);

        $request = $this->requests->create([
            'user_id' => $userId,
            'request_type' => $requestType,
            'otp_hash' => password_hash($otp, PASSWORD_DEFAULT),
            'token' => $token,
            'expired_at' => date('Y-m-d H:i:s', time() + $expireMinutes * 60),
            'created_by' => $createdBy,
            'created_by_id' => $createdById,
        ]);

        $user = $this->users->find($userId);
        $emailSent = $this->sendResetEmail($user, $request, $otp, $token, $requestType);

        return ['request' => $request, 'email_sent' => $emailSent, 'otp' => $otp];
    }

    private function sendResetEmail(
        ?array $user,
        array $request,
        string $otp,
        string $token,
        string $requestType
    ): bool {
        if (!$user) {
            return false;
        }

        $appName = config('app_name', 'Shop Hades');
        $baseUrl = rtrim(config('base_url', 'http://127.0.0.1:8000'), '/');
        $resetLink = $baseUrl . '/reset-password/' . $token;
        $otpMinutes = (int) config('password_reset.otp_expire_minutes', 5);
        $tokenMinutes = (int) config('password_reset.token_expire_minutes', 15);

        $isAdminReset = $requestType === PasswordResetRequest::TYPE_ADMIN_RESET;
        $subject = $isAdminReset
            ? "[{$appName}] Quản trị viên yêu cầu đặt lại mật khẩu"
            : "[{$appName}] Đặt lại mật khẩu";

        $intro = $isAdminReset
            ? '<p><strong>Quản trị viên đã yêu cầu đặt lại mật khẩu cho tài khoản của bạn.</strong></p>
               <p>Vì lý do bảo mật, chúng tôi không thể cho biết mật khẩu mới. Bạn cần tự đặt mật khẩu mới bằng một trong hai cách bên dưới.</p>'
            : '<p>Bạn đã yêu cầu đặt lại mật khẩu. Sử dụng mã OTP hoặc liên kết bên dưới để tiếp tục.</p>';

        $logoUrl = $baseUrl . '/image/logo.webp';
        $introText = $isAdminReset
            ? 'Quản trị viên đã yêu cầu đặt lại mật khẩu cho tài khoản của bạn. Vì lý do bảo mật, bạn cần tự đặt mật khẩu mới bằng một trong hai cách bên dưới.'
            : 'Bạn đã yêu cầu đặt lại mật khẩu. Sử dụng mã OTP hoặc nút liên kết bên dưới để tiếp tục.';

        $body = <<<HTML
        <div style="background-color: #0f172a; padding: 40px 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100%; color: #f8fafc;">
            <div style="max-width: 520px; margin: 0 auto; background-color: #1e293b; border: 1px solid #334155; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);">
                <div style="background-color: #020617; padding: 24px; text-align: center; border-bottom: 1px solid #334155;">
                    <img src="{$logoUrl}" alt="{$appName} Logo" style="height: 48px; display: inline-block;">
                </div>
                <div style="padding: 32px 24px; text-align: center;">
                    <h3 style="color: #f8fafc; margin: 0 0 16px 0; font-size: 20px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Đặt Lại Mật Khẩu</h3>
                    <p style="color: #94a3b8; font-size: 14px; margin: 0 0 24px 0; line-height: 1.6; text-align: left;">{$introText}</p>
                    
                    <div style="background-color: #0f172a; border: 1px dashed #ef4444; border-radius: 12px; padding: 18px; margin-bottom: 24px; display: inline-block; width: 80%;">
                        <span style="font-size: 32px; font-weight: 800; letter-spacing: 6px; color: #ef4444; font-family: monospace;">{$otp}</span>
                    </div>
                    
                    <p style="color: #64748b; font-size: 12px; margin: 0 0 24px 0; line-height: 1.5;">Mã OTP này có hiệu lực trong <strong>{$otpMinutes} phút</strong>.</p>
                    
                    <div style="margin: 24px 0;">
                        <a href="{$resetLink}" style="display: inline-block; padding: 14px 28px; background-color: #d70018; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px; transition: background 0.2s;">
                            Đặt Lại Mật Khẩu
                        </a>
                    </div>
                    
                    <p style="color: #64748b; font-size: 11px; margin: 0; line-height: 1.5; text-align: left; border-top: 1px solid #334155; padding-top: 16px;">
                        Liên kết đặt lại mật khẩu có hiệu lực trong <strong>{$tokenMinutes} phút</strong>. Nếu nút trên không hoạt động, hãy copy đường dẫn sau dán vào trình duyệt:<br>
                        <a href="{$resetLink}" style="color: #3b82f6; text-decoration: underline; word-break: break-all;">{$resetLink}</a>
                    </p>
                </div>
                <div style="background-color: #020617; padding: 16px; text-align: center; border-top: 1px solid #334155;">
                    <p style="color: #475569; font-size: 11px; margin: 0;">© 2026 Shop Hades. All rights reserved.</p>
                    <p style="color: #475569; font-size: 11px; margin: 4px 0 0 0;">Hotline hỗ trợ: 1800 1234</p>
                </div>
            </div>
        </div>
        HTML;

        $sent = Mailer::send($user['email'], $subject, $body);

        if (!$sent) {
            $this->logOtpFallback($user['email'], $otp, $token, $requestType);
        }

        return true;
    }

    private function logOtpFallback(string $email, string $otp, string $token, string $requestType): void
    {
        if (config('app_env') !== 'local') {
            return;
        }
        $logDir = BASE_PATH . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }
        file_put_contents(
            $logDir . '/otp.log',
            sprintf(
                "[%s] RESET %s | %s | OTP: %s | Token: %s | ERROR: %s\n",
                date('Y-m-d H:i:s'),
                $requestType,
                $email,
                $otp,
                $token,
                Mailer::getLastError()
            ),
            FILE_APPEND
        );
    }

    private function canSendRequest(int $userId): bool
    {
        $max = (int) config('password_reset.max_send_per_hour', 5);
        return $this->requests->countRecentByUser($userId, 1) < $max;
    }

    public function canResendByRequestId(int $requestId): bool
    {
        $request = $this->requests->find($requestId);

        return $request ? $this->canResend($request) : false;
    }

    private function isOtpExpired(array $request): bool
    {
        $minutes = (int) config('password_reset.otp_expire_minutes', 5);
        $expires = strtotime($request['created_at']) + $minutes * 60;

        return time() > $expires;
    }

    private function isTokenExpired(array $request): bool
    {
        $minutes = (int) config('password_reset.token_expire_minutes', 15);
        $expires = strtotime($request['created_at']) + $minutes * 60;

        return time() > $expires;
    }

    private function canResend(array $request): bool
    {
        $wait = (int) config('password_reset.resend_seconds', 60);
        $createdAt = strtotime($request['created_at']);

        return time() - $createdAt >= $wait;
    }

    private function resolveUser(string $identifier): ?array
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }

        if (str_contains($identifier, '@')) {
            return $this->users->findByEmail($identifier);
        }

        return $this->users->findByPhone($identifier);
    }

    private function generateOtp(): string
    {
        $length = (int) config('otp.length', 6);
        $min = (int) str_pad('1', $length, '0');
        $max = (int) str_pad('9', $length, '9');

        return (string) random_int($min, $max);
    }

    private function maskEmail(string $email): string
    {
        if (!str_contains($email, '@')) {
            return '***';
        }
        [$local, $domain] = explode('@', $email, 2);
        $visible = substr($local, 0, min(2, strlen($local)));

        return $visible . '***@' . $domain;
    }
}
