<?php

namespace App\Controllers\Api;

use App\PasswordResetService;

class PasswordResetController
{
    private PasswordResetService $service;

    public function __construct()
    {
        $this->service = new PasswordResetService();
    }

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

    /** POST /api/password/forgot */
    public function forgot(): void
    {
        $input = $this->getJsonInput();
        $identifier = trim($input['identifier'] ?? $input['email'] ?? $input['phone'] ?? '');

        if ($identifier === '') {
            $this->sendResponse(['success' => false, 'message' => 'Vui lòng cung cấp email hoặc số điện thoại.'], 400);
        }

        $result = $this->service->initiateForgotPassword($identifier);

        $response = [
            'success' => $result['success'],
            'message' => $result['message'],
        ];

        if ($result['success'] && isset($result['request'])) {
            $response['request_id'] = (int) $result['request']['id'];
        }

        $this->sendResponse($response, $result['success'] ? 200 : 429);
    }

    /** POST /api/password/verify-otp */
    public function verifyOtp(): void
    {
        $input = $this->getJsonInput();
        $requestId = (int) ($input['request_id'] ?? 0);
        $otp = trim($input['otp'] ?? '');

        if ($requestId <= 0 || $otp === '') {
            $this->sendResponse(['success' => false, 'message' => 'Thiếu request_id hoặc otp.'], 400);
        }

        $result = $this->service->verifyOtp($requestId, $otp);
        $this->sendResponse([
            'success' => $result['success'],
            'message' => $result['message'],
            'verified' => $result['success'],
        ], $result['success'] ? 200 : 400);
    }

    /** POST /api/password/reset */
    public function reset(): void
    {
        $input = $this->getJsonInput();
        $password = $input['password'] ?? '';
        $confirm = $input['password_confirmation'] ?? '';
        $requestId = (int) ($input['request_id'] ?? 0);
        $token = trim($input['token'] ?? '');

        if ($password === '' || $password !== $confirm) {
            $this->sendResponse(['success' => false, 'message' => 'Mật khẩu không hợp lệ hoặc xác nhận không khớp.'], 400);
        }

        if ($token === '' && $requestId <= 0) {
            $this->sendResponse(['success' => false, 'message' => 'Cần request_id (sau OTP) hoặc token (từ link).'], 400);
        }

        $result = $token !== ''
            ? $this->service->completeReset(0, $password, $token)
            : $this->service->completeReset($requestId, $password);

        $this->sendResponse([
            'success' => $result['success'],
            'message' => $result['message'],
        ], $result['success'] ? 200 : 400);
    }

    /** POST /api/password/resend */
    public function resend(): void
    {
        $input = $this->getJsonInput();
        $requestId = (int) ($input['request_id'] ?? 0);

        if ($requestId <= 0) {
            $this->sendResponse(['success' => false, 'message' => 'Thiếu request_id.'], 400);
        }

        $result = $this->service->resend($requestId);
        $response = [
            'success' => $result['success'],
            'message' => $result['message'],
        ];

        if ($result['success'] && isset($result['request'])) {
            $response['request_id'] = (int) $result['request']['id'];
        }

        $this->sendResponse($response, $result['success'] ? 200 : 429);
    }

    /** POST /api/admin/users/{id}/reset-password — requires admin JWT */
    public function adminReset(int $userId): void
    {
        $auth = new AuthController();
        $admin = $auth->getAuthenticatedUser();

        if (!$admin || ($admin['role'] ?? '') !== 'admin') {
            $this->sendResponse(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $result = $this->service->initiateAdminReset($userId, (int) $admin['id']);
        $this->sendResponse([
            'success' => $result['success'],
            'message' => $result['message'],
        ], $result['success'] ? 200 : 400);
    }
}
