<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $appkey;
    protected $authkey;
    protected $url;

    public function __construct()
    {
        $this->appkey  = config('services.whatsapp.appkey');
        $this->authkey = config('services.whatsapp.authkey');
        $this->url     = config('services.whatsapp.url');
    }

    public function sendMessage($to, $message, $sandbox = false)
    {
        try {

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->withoutVerifying()  
            ->post($this->url, [
                'appkey'  => $this->appkey,
                'authkey' => $this->authkey,
                'to'      => $to,
                'message' => $message,
                'sandbox' => $sandbox
            ]);

            return [
                'success' => $response->successful(),
                'data'    => $response->json(),
                'status'  => $response->status()
            ];

        } catch (\Throwable $e) {

            Log::error('WhatsApp API Error', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }
}
