<?php

namespace App\Http\Helpers;

use Config;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Str;

class BasicMailer
{
//   public static function sendMail($data)
//   {
//     if ($data['smtp_status'] == 1) {
//       $smtp = [
//         'transport' => 'smtp',
//         'host' => $data['smtp_host'],
//         'port' => $data['smtp_port'],
//         'encryption' => $data['encryption'],
//         'username' => $data['smtp_username'],
//         'password' => $data['smtp_password'],
//         'timeout' => null,
//         'auth_mode' => null,
//       ];
//       Config::set('mail.mailers.smtp', $smtp);

//       // add other informations and send the mail
//       try {
//         Mail::send([], [], function (Message $message) use ($data) {
//           $fromMail = $data['from_mail'];
//           $subject = $data['subject'];
//           $message->to($data['recipient'])
//             ->from($fromMail)
//             ->subject($subject)
//             ->html($data['body'], 'text/html');
// // $ccEmails = ['madhubalagam16@gmail.com', 'extra_cc@example.com']; // replace or pass dynamically
// //                     foreach ($ccEmails as $cc) {
// //                         $message->cc($cc);
// //                     }

//                     // Add BCC emails (dynamic or fixed)
//                     $bccEmails = ['shivanand.g@techsters.in', 'admin@monarchergo.com','edp@monarchergo.com','Accounts@monarchergo.com','Accounts.plant@monarchergo.com','satish.n@monarchergo.com','srdhrn.kumeran@gmail.com','uhas@monarchergo.com','Fa.lead@monarchergo.com','Seatings@monarchergo.com','sunil.n@monarchergo.com']; // replace or pass dynamically
//                     foreach ($bccEmails as $bcc) {
//                         $message->bcc($bcc);
//                     }
//           if (array_key_exists('invoice', $data)) {
//             $message->attach($data['invoice']);
//           }
//         });
//       } catch (\Exception $e) {
//         Session::flash('warning', 'Mail could not be sent. Mailer Error: ' . Str::limit($e->getMessage(), 120));
//       }
//     }
//   }

//   public static function sendMail($data)
// {
//     if ($data['smtp_status'] == 1) {
//         $smtp = [
//             'transport' => 'smtp',
//             'host' => $data['smtp_host'],
//             'port' => $data['smtp_port'],
//             'encryption' => $data['encryption'],
//             'username' => $data['smtp_username'],
//             'password' => $data['smtp_password'],
//             'timeout' => null,
//             'auth_mode' => null,
//         ];
//         Config::set('mail.mailers.smtp', $smtp);

//         $recipients = [
//             'admin@monarchergo.com',
//             'edp@monarchergo.com',
//             'Accounts@monarchergo.com',
//             'Accounts.plant@monarchergo.com',
//             'satish.n@monarchergo.com',
//             'srdhrn.kumeran@gmail.com',
//             'suhas@monarchergo.com',
//             'Fa.lead@monarchergo.com',
//             'Seatings@monarchergo.com',
//             'sunil.n@monarchergo.com',
//             'reach@monarchergo.com',
//             'shivanand.g@techsters.in'
//         ];

//         try {
//             foreach ($recipients as $recipient) {
//                 Mail::send([], [], function ($message) use ($data, $recipient) {

//                     $fromMail = $data['from_mail'];
//                     $subject  = $data['subject'];

//                     $message->to($recipient)
//                             ->from($fromMail)
//                             ->subject($subject)
//                             ->html($data['body']); // Use html() instead of setBody()
                    
//                     if (!empty($data['invoice'])) {
//                         $message->attach($data['invoice']);
//                     }
//                 });
//             }
//         } catch (\Exception $e) {
//             Session::flash('warning', 'Mail could not be sent. Mailer Error: ' . Str::limit($e->getMessage(), 120));
//         }
//     }
// }


public static function sendMail($data)
{
    if ($data['smtp_status'] != 1) {
        return;
    }

    Config::set('mail.mailers.smtp', [
        'transport' => 'smtp',
        'host' => $data['smtp_host'],
        'port' => $data['smtp_port'],
        'encryption' => $data['encryption'],
        'username' => $data['smtp_username'],
        'password' => $data['smtp_password'],
    ]);

    try {

        Mail::send([], [], function ($message) use ($data) {

            $message->to($data['recipient']) // CUSTOMER EMAIL
                ->from($data['from_mail'])
                ->subject($data['subject'])
                ->html($data['body']);

            if (!empty($data['invoice']) && file_exists($data['invoice'])) {
                $message->attach($data['invoice']);
            }
        });

        $adminRecipients = [
            'admin@monarchergo.com',
            'edp@monarchergo.com',
            'Accounts@monarchergo.com',
            'Accounts.plant@monarchergo.com',
            'satish.n@monarchergo.com',
            'srdhrn.kumeran@gmail.com',
            'suhas@monarchergo.com',
            'Fa.lead@monarchergo.com',
            'Seatings@monarchergo.com',
            'sunil.n@monarchergo.com',
            'reach@monarchergo.com',
            'shivanand.g@techsters.in'
        ];

        foreach ($adminRecipients as $adminEmail) {
            Mail::send([], [], function ($message) use ($data, $adminEmail) {

                $message->to($adminEmail)
                    ->from($data['from_mail'])
                    ->subject('New Order Received - ' . $data['subject'])
                    ->html($data['body']);

                if (!empty($data['invoice']) && file_exists($data['invoice'])) {
                    $message->attach($data['invoice']);
                }
            });
        }

    } catch (\Exception $e) {
        Session::flash(
            'warning',
            'Mail Error: ' . \Str::limit($e->getMessage(), 150)
        );
    }
}

}
