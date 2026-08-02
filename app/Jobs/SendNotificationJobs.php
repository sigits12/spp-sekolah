<?php

namespace App\Jobs;

use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $pesan;
   
    /**
     * Create a new job instance.
     */
    public function __construct(array $pesan)
    {
        $this->pesan = $pesan;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $waService): void
    {
        Log::channel('whatsapp')->info("SendNotificationJobs handle: " . json_encode($this->pesan));
        try {
            $no = $this->pesan['no_whatsapp'];
            $msg = $this->pesan['pesan'];
            $result = $waService->sendMessage($no, $msg);

            if ($result) {
                Log::channel('whatsapp')->info("Notifikasi berhasil dikirim ke: " . $no);
            } else {
                Log::channel('whatsapp')->error("Gagal mengirim notifikasi ke: " . $no);
            }
        } catch (\Exception $e) {
            Log::channel('whatsapp')->error("Gagal mengirim notifikasi: " . $e->getMessage());
        }
    }
}
