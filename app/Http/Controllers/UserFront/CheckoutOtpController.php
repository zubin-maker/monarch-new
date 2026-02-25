<?php

namespace App\Http\Controllers\UserFront;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CheckoutOtpController extends Controller
{
    /**
     * After OTP is verified: find customer by phone and log in, or ask for address to create account.
     */
    public function completeCheckout(Request $request)
    {
        $request->validate(['phone' => 'required|string']);
        $phone = $request->input('phone');
        $user = getUser();

        if (session('otp_verified_phone') !== $phone || (int) session('otp_verified_user_id') !== (int) $user->id) {
            return response()->json(['error' => 'Session expired. Please verify OTP again.'], 403);
        }

        $customer = Customer::where('user_id', $user->id)
            ->where('contact_number', $phone)
            ->first();

        if ($customer) {
            Auth::guard('customer')->login($customer);
            session()->forget(['otp_verified_phone', 'otp_verified_user_id']);
            return response()->json([
                'redirect' => route('front.user.checkout.final_step', getParam()),
            ]);
        }

        return response()->json(['need_address' => true]);
    }

    /**
     * Create customer with address after OTP verified, then log in and redirect to checkout.
     */
    public function registerGuest(Request $request)
    {
        $phone = $request->input('phone');
        $user = getUser();

        if (session('otp_verified_phone') !== $phone || (int) session('otp_verified_user_id') !== (int) $user->id) {
            return response()->json(['error' => 'Session expired. Please verify OTP again.'], 403);
        }

        $rules = [
            'phone' => 'required|string',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email',
            'billing_address' => 'required|string',
            'billing_city' => 'required|string|max:100',
            'billing_country' => 'required|string|max:100',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $email = $request->input('email');
        $existing = Customer::where('user_id', $user->id)->where('email', $email)->first();
        if ($existing) {
            return response()->json(['errors' => ['email' => ['An account with this email already exists. Please log in.']]], 422);
        }

        $customer = Customer::create([
            'user_id' => $user->id,
            'contact_number' => $phone,
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $email,
            'username' => $email,
            'password' => Hash::make(Str::random(16)),
            'status' => 1,
            'email_verified' => 0,
            'billing_fname' => $request->input('first_name'),
            'billing_lname' => $request->input('last_name'),
            'billing_email' => $email,
            'billing_number' => $phone,
            'billing_address' => $request->input('billing_address'),
            'billing_city' => $request->input('billing_city'),
            'billing_state' => $request->input('billing_state', ''),
            'billing_country' => $request->input('billing_country'),
        ]);

        Auth::guard('customer')->login($customer);
        session()->forget(['otp_verified_phone', 'otp_verified_user_id']);

        return response()->json([
            'redirect' => route('front.user.checkout.final_step', getParam()),
        ]);
    }
}
