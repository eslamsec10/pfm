<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\NotificationSettings;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $url = 'https://whatsappapisender.com/api/create-message';

   protected $appkey  =  'ca65d618-8880-4c33-b64e-9772fab004f5';//NotificationSettings::where('key' ,'appkey')->first()->value ;
    protected $authkey =  'BQlJU7aX7bNy7nMJRTywmHiNfDeouwyepgBvNc1RFrXmQcc2QG'; //NotificationSettings::where('key' ,'authkey')->first()->value ;


    // public function __construct()
    // {
    //     $this->appkey = NotificationSettings::where('key','appkey')->value('value');

    //     $this->authkey = NotificationSettings::where('key','authkey')->value('value');
    // }

    public function send($to, $message, $file = null, $sandbox = true)
    {
        try {

            $data = [
                'appkey'  => $this->appkey,
                'authkey' => $this->authkey,
                'to'      => $to,
                'message' => $message,
                'sandbox' => $sandbox ,
            ];
 
            if ($file) {
                $data['file'] = $file;
            }

            $response = Http::asMultipart()
                ->withoutVerifying() 
                ->post($this->url, $data);

            return [
                'success' => $response->successful(),
                'status'  => $response->status(),
                'data'    => $response->json()
            ];

        } catch (\Throwable $e) {

            Log::error('WhatsApp Send Error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error'   => $e->getMessage()
            ];
        }
    }
}
