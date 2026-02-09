<?php 

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Mail\GeneralNotificationTransactionMail;

class NotificationDispatcher
{
    public function send($channels,$tenant,$company,$payload)
    {
        if(in_array('email',$channels) && $tenant->email){

            Mail::to($tenant->email)
                ->send(new GeneralNotificationTransactionMail(
                    $payload['title'],
                    $tenant,
                    $company,
                    $payload['table']
                ));
        }

        if(in_array('whatsapp',$channels)){

            $message = "Hello Dear {$tenant->name}\n";
            $message .= $payload['title']." in {$company->name}\n";

            foreach($payload['table'] as $row){
                $message .= "{$row['unit']} | {$row['rent']} | {$row['date']}\n";
            }

            sendWhatsApp([
                'to' => $tenant->whatsapp_no ?? $tenant->contact_no,
                'message' => $message
            ]);
        }

        if(in_array('system',$channels)){
            // create system notification هنا
        }
    }
}
