<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Services\NewWhatsAppService;


class WhatsappController extends Controller
{
    protected $newWhatsAppService;

    public function __construct(NewWhatsAppService $newWhatsAppService)
    {
        $this->newWhatsAppService = $newWhatsAppService;
    }

    public function list() {
        $data = $this->newWhatsAppService->getActiveSessions();
        return response()->json($data, 200);
    }

    public function createSession(Request $request)
    {
        $result = $this->newWhatsAppService->createSession(['name' => 'satu']);

        return response()->json($result);
    }
}