<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Payment\MyFatoorahController as MembershipMyFatoorahController;
use App\Http\Controllers\User\Payment\MyfatoorahController as ProductMyFatoorahController;
use Illuminate\Http\Request;
use Session;

class MyFatoorahController extends Controller
{
    public function callback(Request $request)
    {
        $type = Session::get('myfatoorah_payment_type');
        if ($type == 'buy_plan') {
            $data = new MembershipMyFatoorahController();
            $data = $data->successPayment($request);
            Session::forget('myfatoorah_payment_type');
            if ($data['status'] == 'success') {
                return redirect()->route('success.page');
            } else {
                return redirect()->route('membership.cancel');
            }
        } elseif ($type == 'product_purchase') {
            try {
                $data = new ProductMyFatoorahController();
                $data = $data->successPayment($request);
                $getparam = Session::get('getparam');
                Session::forget('myfatoorah_payment_type');
                Session::forget('myfatoorah_user');
                Session::forget('cancel_url');
                Session::forget('getparam');
                if ($data['status'] == 'success') {
                    return redirect()->route('customer.success.page', $getparam);
                } else {
                    $cancel_url = Session::get('cancel_url');
                    return redirect()->route($cancel_url);
                }
            } catch (\Exception $e) {

            }
        }
    }

    public function cancel(Request $request)
    {
        $type = Session::get('myfatoorah_payment_type');
        $getparam = Session::get('getparam');
        Session::flash('warning', __('cancel_payment'));
        if ($type == 'buy_plan') {
            return redirect()->route('membership.cancel');
        }else{
            return redirect()->route('front.user.checkout.final_step', $getparam);
        }
    }
}
