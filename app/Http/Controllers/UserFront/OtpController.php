<?php

namespace App\Http\Controllers\UserFront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class OtpController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

        $twilio->verify->v2->services(env('TWILIO_VERIFY_SID'))
            ->verifications
            ->create($request->input('phone'), 'sms');

        return response()->json(['status' => 'sent']);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code'  => 'required|string',
        ]);

        $twilio = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));

        $check = $twilio->verify->v2->services(env('TWILIO_VERIFY_SID'))
            ->verificationChecks
            ->create([
                'to'   => $request->input('phone'),
                'code' => $request->input('code'),
            ]);

        if ($check->status === 'approved') {
            session([
                'otp_verified_phone' => $request->input('phone'),
                'otp_verified_user_id' => getUser()->id,
            ]);
            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status' => 'invalid'], 422);
    }
}

