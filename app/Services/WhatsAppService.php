<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $url;

    public function __construct()
    {
        $this->url = env('WA_BRIDGE_URL', 'http://localhost:3000/send');
    }

    public function sendMessage(string $phone, string $message): bool
    {
        $formattedPhone = $this->formatPhoneNumber($phone);
        Log::channel('whatsapp')->info("Sending message to: " . $formattedPhone . " with message: " . $message);
        try {
            $response = Http::post($this->url, [
                'phone' => $formattedPhone,
                'message' => $message,
            ]);

            if ($response->successful() && $response->json('status') === 'success') {
                Log::channel('whatsapp')->info("Pesan berhasil dikirim ke: " . $formattedPhone);
                return true;
            }

            Log::error('Gagal mengirim WA via Go Service', [
                'body' => $response->body(),
                'phone' => $formattedPhone,
            ]);

            return false;
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error('Koneksi ke Go WA service terputus: ' . $e->getMessage());
            return false;
        }
    }

    protected function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // 1. Cek nomor Indonesia (diawali 0 atau 62)
        if (str_starts_with($phone, '0')) {
            return '62' . substr($phone, 1);
        }
        if (str_starts_with($phone, '62')) {
            return $phone;
        }

        // 2. Cek nomor Hongkong (diawali 852 atau 852 dengan panjang standar)
        if (str_starts_with($phone, '852')) {
            return $phone;
        }
        // Jika format lokal Hongkong tanpa kode negara (biasanya 8 digit, misal: 9xxxxxxx atau 5xxxxxxx)
        if (strlen($phone) === 8) {
            return '852' . $phone;
        }

        // 3. Jika tidak memenuhi kriteria Indonesia maupun Hongkong
        throw new \InvalidArgumentException('Format nomor telepon tidak valid (hanya mendukung Indonesia dan Hongkong).');
    }
}