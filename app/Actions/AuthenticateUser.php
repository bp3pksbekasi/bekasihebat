<?php

declare(strict_types=1);

namespace App\Actions;

use App\Support\PhoneNormalizer;
use Illuminate\Support\Facades\Auth;

class AuthenticateUser
{
    public function execute(string $identifier, string $password, bool $remember = false): bool
    {
        $identifier = trim($identifier);

        $field = match (true) {
            str_contains($identifier, '@') => 'email',
            (bool) preg_match('/^\d{3}\.\d{3}\.\d{3}$/', $identifier) => 'nia',
            default => 'phone',
        };

        $value = $field === 'phone'
            ? PhoneNormalizer::normalize($identifier)
            : $identifier;

        $credentials = [
            $field => $value,
            'password' => $password,
            'status' => 'aktif',
        ];

        // #region debug-point A:auth-attempt
        (function () use ($field, $value, $remember) {
            $u = 'http://127.0.0.1:7777/event';
            $s = 'bedah-dapil-redirect';
            $p = base_path('.dbg/bedah-dapil-redirect.env');
            if (is_file($p)) {
                $e = @file_get_contents($p) ?: '';
                preg_match('/DEBUG_SERVER_URL=(.+)/', $e, $m1);
                preg_match('/DEBUG_SESSION_ID=(.+)/', $e, $m2);
                $u = $m1[1] ?? $u;
                $s = $m2[1] ?? $s;
            }
            @file_get_contents($u, false, stream_context_create(['http' => ['method' => 'POST', 'header' => "Content-Type: application/json\r\n", 'content' => json_encode(['sessionId' => $s, 'runId' => 'pre-fix', 'hypothesisId' => 'A', 'location' => 'AuthenticateUser::execute', 'msg' => '[DEBUG] auth attempt started', 'data' => ['field' => $field, 'value' => $value, 'remember' => $remember, 'session_id' => request()->session()->getId(), 'user_id' => optional(auth()->user())->id], 'ts' => (int) round(microtime(true) * 1000)])]]));
        })();
        // #endregion

        if (Auth::attempt($credentials, $remember)) {
            request()->session()->regenerate();

            // #region debug-point A:auth-success
            (function () use ($field, $value) {
                $u = 'http://127.0.0.1:7777/event';
                $s = 'bedah-dapil-redirect';
                $p = base_path('.dbg/bedah-dapil-redirect.env');
                if (is_file($p)) {
                    $e = @file_get_contents($p) ?: '';
                    preg_match('/DEBUG_SERVER_URL=(.+)/', $e, $m1);
                    preg_match('/DEBUG_SESSION_ID=(.+)/', $e, $m2);
                    $u = $m1[1] ?? $u;
                    $s = $m2[1] ?? $s;
                }
                @file_get_contents($u, false, stream_context_create(['http' => ['method' => 'POST', 'header' => "Content-Type: application/json\r\n", 'content' => json_encode(['sessionId' => $s, 'runId' => 'pre-fix', 'hypothesisId' => 'A', 'location' => 'AuthenticateUser::execute', 'msg' => '[DEBUG] auth attempt succeeded', 'data' => ['field' => $field, 'value' => $value, 'session_id' => request()->session()->getId(), 'user_id' => optional(auth()->user())->id], 'ts' => (int) round(microtime(true) * 1000)])]]));
            })();
            // #endregion

            return true;
        }

        // #region debug-point A:auth-failed
        (function () use ($field, $value) {
            $u = 'http://127.0.0.1:7777/event';
            $s = 'bedah-dapil-redirect';
            $p = base_path('.dbg/bedah-dapil-redirect.env');
            if (is_file($p)) {
                $e = @file_get_contents($p) ?: '';
                preg_match('/DEBUG_SERVER_URL=(.+)/', $e, $m1);
                preg_match('/DEBUG_SESSION_ID=(.+)/', $e, $m2);
                $u = $m1[1] ?? $u;
                $s = $m2[1] ?? $s;
            }
            @file_get_contents($u, false, stream_context_create(['http' => ['method' => 'POST', 'header' => "Content-Type: application/json\r\n", 'content' => json_encode(['sessionId' => $s, 'runId' => 'pre-fix', 'hypothesisId' => 'A', 'location' => 'AuthenticateUser::execute', 'msg' => '[DEBUG] auth attempt failed', 'data' => ['field' => $field, 'value' => $value, 'session_id' => request()->session()->getId(), 'user_id' => optional(auth()->user())->id], 'ts' => (int) round(microtime(true) * 1000)])]]));
        })();
        // #endregion

        return false;
    }
}
