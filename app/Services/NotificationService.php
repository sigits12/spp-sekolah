<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function formatPesanPembayaran(array $data): string
    {
        $rincianTeks = "";
        foreach ($data['rincian'] as $item) {
            $rincianTeks .= "- {$item['nama']} : Rp " . number_format($item['nominal'], 0, ',', '.') . "\n";
        }
        $metode = strtoupper($data['metode']);
        $template = "*Info Sistem Pembayaran SD Tahfizhul Quran Mutiara Islam*\n\n" .
            "Bismillahirrahmānirrahīm\n" .
            "Assalamu'alaikum Warahmatullahi Wabarakatuh\n\n" .
            "Yth. Orang Tua/Wali Siswa *{$data['nama_siswa']}* (*Kelas {$data['nama_kelas']}*)\n\n" .
            "Alhamdulillah, pembayaran administrasi sekolah Ananda melalui *{$metode}* telah berhasil kami terima dan terdata di sistem pada *{$data['tanggal_verifikasi']}*\n".
            "Berikut rincian alokasi pembayarannya:\n\n".
            rtrim($rincianTeks) . "\n\n" .
            "Total : *Rp " . number_format($data['total_masuk'], 0, ',', '.') . "*\n\n" .
            "Status keuangan Ananda telah terbarui secara otomatis. Jika ada hal yang ingin ditanyakan, Tim Keuangan kami siap membantu Bapak/Ibu.\n\n".
            "Kami mengucapkan Jazakumullah Khairan atas kepercayaan dan kerja samanya\n".
            "Semoga nafkah untuk pendidikan Ananda menjadi amal jariyah dan dibalas keberkahan berlipat ganda oleh Allah Ta'ala.\n\n".
            "Wassalamu'alaikum Warahmatullahi Wabarakatuh\n\n" .
            "Admin Tim Keuangan SD Tahfizhul Quran Mutiara Islam";

        return $template;
    }
}