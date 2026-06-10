<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhapifyService
{
    /**
     * Send a WhatsApp message using Whapify API.
     *
     * @param string $recipient
     * @param string $message
     * @return array
     */
    public static function sendMessage(string $recipient, string $message): array
    {
        $secret = Setting::get('whapify_secret');
        $account = Setting::get('whapify_account');

        if (!$secret || !$account) {
            return [
                'success' => false,
                'message' => 'Konfigurasi Whapify (Secret atau Account ID) belum diatur.',
            ];
        }

        try {
            $response = Http::asMultipart()->post('https://whapify.id/api/send/whatsapp', [
                'secret' => $secret,
                'account' => $account,
                'recipient' => $recipient,
                'type' => 'text',
                'message' => $message,
            ]);

            if ($response->successful()) {
                $body = $response->json();
                return [
                    'success' => true,
                    'message' => 'Pesan berhasil dikirim.',
                    'response' => $body,
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal mengirim pesan. Status: ' . $response->status() . ' - ' . $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('Whapify send exception: ' . $e->getMessage(), [
                'recipient' => $recipient,
                'exception' => $e,
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
            ];
        }
    }
}
