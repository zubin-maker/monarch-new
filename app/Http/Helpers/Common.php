<?php

namespace App\Http\Helpers;

use PDF;
use App\Models\Language;
use App\Models\User\UserCurrency;
use App\Models\User\UserItem;
use App\Models\User\UserOfflineGateway;
use App\Models\User\UserOrder;
use App\Models\User\UserShippingCharge;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User\ProductVariantOption;
use App\Models\User\UserItemContent;
use App\Models\User\UserOrderItem;
use Carbon\Carbon;
use App\Models\User\BasicSetting;
use App\Models\User\UserEmailTemplate;
use App\Models\User\UserShopSetting;
use DB;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Common
{
    use AuthorizesRequests, ValidatesRequests;

    public static function sendMailFacadeMail($request, $file_name, $be, $subject, $body, $email, $name)
    {
        /******** Send mail to user ********/
        $data = [];
        $data['smtp_status'] = $be->is_smtp;
        $data['smtp_host'] = $be->smtp_host;
        $data['smtp_port'] = $be->smtp_port;
        $data['encryption'] = $be->encryption;
        $data['smtp_username'] = $be->smtp_username;
        $data['smtp_password'] = $be->smtp_password;

        //mail info in array
        $data['from_mail'] = $be->from_mail;
        $data['recipient'] = $email;
        $data['subject'] = $subject;
        $data['body'] = $body;
        $data['cc'] = ['madhubalagam16@gmail.com'];
        $data['bcc'] = ['shivanand.g@techsters.in'];
        BasicMailer::sendMail($data);
        if ($file_name) {
            @unlink(public_path('assets/front/invoices/') . $file_name);
        }
    }

    public static function makeInvoice($request, $key, $member, $password, $amount, $payment_method, $phone, $base_currency_text_position, $base_currency_symbol, $base_currency_text, $order_id, $package_title, $status)
    {
        $file_name = uniqid($key) . ".pdf";
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ])->loadView('pdf.membership', compact('request', 'member', 'password', 'amount', 'payment_method', 'phone', 'base_currency_text_position', 'base_currency_symbol', 'base_currency_text', 'order_id', 'package_title', 'status'));
        $output = $pdf->output();
        $dir = public_path('assets/front/invoices/');
        @mkdir($dir, '0775', true);
        @file_put_contents($dir . $file_name, $output);
        return $file_name;
    }

    public static function resetPasswordMail($email, $name, $subject, $body)
    {
        $currentLang = session()->has('lang') ?
            (Language::where('code', session()->get('lang'))->first())
            : (Language::where('is_default', 1)->first());
        $be = $currentLang->basic_extended;
        /******** Send mail to user ********/
        $data = [];
        $data['smtp_status'] = $be->is_smtp;
        $data['smtp_host'] = $be->smtp_host;
        $data['smtp_port'] = $be->smtp_port;
        $data['encryption'] = $be->encryption;
        $data['smtp_username'] = $be->smtp_username;
        $data['smtp_password'] = $be->smtp_password;

        //mail info in array
        $data['from_mail'] = $be->from_mail;
        $data['recipient'] = $email;
        $data['subject'] = $subject;
        $data['body'] = $body;
        BasicMailer::sendMail($data);
    }

    // items checkout
    public static function orderTotal($shipping, $user_id)
    {
        if ($shipping != 0) {
            $shipping = UserShippingCharge::findOrFail($shipping);
            $shippig_charge = $shipping->charge;
        } else {
            $shippig_charge = 0;
        }

        //cartTotal
        $cartTotal =  Self::cartTotal($user_id);
        //tax
        $tax =  Self::tax($user_id);

        $total = round(($cartTotal - coupon()) + $shippig_charge + $tax, 2);

        return round($total, 2);
    }

    public static function tax($user_id)
    {
        $username = app('user')->username;
        $bex = UserShopSetting::where('user_id', $user_id)->first();
        $tax = $bex->tax;
        if (session()->has('cart_' . $username) && !empty(session()->get('cart_' . $username))) {
            $cartSubTotal =  Self::cartSubTotal($user_id);
            $tax = ($cartSubTotal * $tax) / 100;
        }

        return round($tax, 2);
    }

    public static function cartSubTotal($user_id)
    {
        $username = app('user')->username;
        $coupon = session()->has('user_coupon_' . $username) && !empty(session()->get('user_coupon_' . $username)) ? session()->get('user_coupon_' . $username) : 0;
        //cartTotal
        $cartTotal =  Self::cartTotal($user_id);
        $subTotal = $cartTotal - $coupon;

        return round($subTotal, 2);
    }
    public static function tax_percentage($user_id)
    {

        $bex = UserShopSetting::where('user_id', $user_id)->first();
        return $bex->tax;
    }
    public static function cartTotal($user_id)
    {
        $username = app('user')->username;
        $total = 0;
        if (session()->has('cart_' . $username) && !empty(session()->get('cart_' . $username))) {
            $cart = session()->get('cart_' . $username);

            if (!is_null($cart) && is_array($cart)) {
                $cart = array_filter($cart, function ($item) use ($user_id) {
                    return $item['user_id'] == $user_id;
                });
                foreach ($cart as $key => $cartItem) {
                    $total += $cartItem['total'];
                }
            }
        }

        return round($total, 2);
    }

    public static function orderValidation($request, $gtype = 'online', $user_id = null)
    {
        $rules = [
            'billing_fname' => 'required',
            'billing_lname' => 'required',
            'billing_number' => 'required',
            'billing_email' => 'required',
            'billing_city' => 'required',
            'billing_country' => 'required',
            'billing_address' => 'required',
            'payment_method' => 'required',

            'shipping_fname' => $request->checkbox == 'on' ? 'required' : '',
            'shipping_lname' => $request->checkbox == 'on' ? 'required' : '',
            'shipping_number' => $request->checkbox == 'on' ? 'required' : '',
            'shipping_email' => $request->checkbox == 'on' ? 'required' : '',
            'shipping_city' => $request->checkbox == 'on' ? 'required' : '',
            'shipping_country' => $request->checkbox == 'on' ? 'required' : '',
            'shipping_address' => $request->checkbox == 'on' ? 'required' : '',
            'identity_number' => $request->payment_method == 'Iyzico' ? 'required' : '',
            'zip_code' => $request->payment_method == 'Iyzico' ? 'required' : '',
        ];

        if ($gtype == 'offline') {
            $gateway = UserOfflineGateway::where([['name', $request->payment_method], ['user_id', $user_id]])->first();

            if ($gateway->is_receipt == 1) {
                $rules['receipt'] = [
                    'required',
                    function ($attribute, $value, $fail) use ($request) {
                        $ext = $request->file('receipt')->getClientOriginalExtension();
                        if (!in_array($ext, array('jpg', 'png', 'jpeg'))) {
                            return $fail("Only png, jpg, jpeg image is allowed");
                        }
                    },
                ];
            }
        }

        $request->validate($rules);
    }

    public static function saveOrder($request, $txnId, $chargeId, $paymentStatus = 'Pending', $gtype = 'online', $user_id)
    {
        $username = app('user')->username;
        $shpp_chrg = 0;
        if (!empty($request["shipping_charge"])) {
            $shpp_chrg = $request["shipping_charge"];
        }
        $total = Common::orderTotal($shpp_chrg, $user_id);

        $coupon_amount = session()->get('user_coupon_' . $username);
       $coupen_code = session()->get('code_' . $username);
        
      
        $total = $total - session()->get('user_coupon_' . $username);
        if ($shpp_chrg != 0) {
            $shipping = UserShippingCharge::findOrFail($shpp_chrg);
            $shippig_charge = currency_converter_shipping($shipping->charge, $shipping->id);;
            $shipping_method = $shipping->title;
        } else {
            $shippig_charge = 0;
            $shipping_method = NULL;
        }


        if (Session::has('myfatoorah_user')) {
            $user = Session::get('myfatoorah_user');
        } else {
            $user = getUser();
        }

        $timeZone = DB::table('user_basic_settings')->where('user_id', $user->id)->value('timezone');
        $now = Carbon::now($timeZone);

        $order_status = 'pending';
        $cart = session()->get('cart_' . $user->username, []);
        if (count($cart) == 1) {
            foreach ($cart as $itemCart)
                $itemType = UserItem::where([['user_id', $user->id], ['id', $itemCart['id']]])->pluck('type')->first();
            if ($itemType == 'digital') {
                $order_status = 'completed';
            }
        }

        $order = new UserOrder();
        $order->customer_id = Auth::guard('customer')->check() ? Auth::guard('customer')->user()->id : 9999999;
        $order->user_id = $user->id;
        $order->billing_fname = $request['billing_fname'];
        $order->billing_lname = $request['billing_lname'];
        $order->billing_email = $request['billing_email'];
        $order->billing_address = $request['billing_address'];
        $order->billing_city = $request['billing_city'];
        $order->billing_state = $request['billing_state'];
        $order->billing_country = $request['billing_country'];
        $order->billing_company = $request['billing_company'];
        $order->billing_gst = $request['billing_gst'];
        $order->billing_number = $request['billing_number'];
        $order->shipping_fname = !is_null($request['shipping_fname']) ? $request['shipping_fname'] : $request['billing_fname'];
        $order->shipping_lname = !is_null($request['shipping_lname']) ? $request['shipping_lname'] : $request['billing_lname'];
        $order->shipping_email = !is_null($request['shipping_email']) ? $request['shipping_email'] : $request['billing_email'];
        $order->shipping_address = !is_null($request['shipping_address']) ? $request['shipping_address'] : $request['billing_address'];
        $order->shipping_city = !is_null($request['shipping_city']) ? $request['shipping_city'] : $request['billing_city'];
        $order->shipping_state = !is_null($request['shipping_state']) ? $request['shipping_state'] : $request['billing_state'];
        $order->shipping_country = !is_null($request['shipping_country']) ? $request['shipping_country'] : $request['billing_country'];
        $order->shipping_company = !is_null($request['shipping_company']) ? $request['shipping_company'] : $request['billing_company'];
        $order->shipping_gst = !is_null($request['shipping_gst']) ? $request['shipping_gst'] : $request['billing_gst'];
        $order->shipping_number = !is_null($request['shipping_number']) ? $request['shipping_number'] : $request['billing_number'];
        $order->order_status = $order_status;
        $order->gateway_type = $gtype;
        if (is_array($request)) {
            if (array_key_exists('conversation_id', $request)) {
                $conversation_id = $request['conversation_id'];
            } else {
                $conversation_id = null;
            }
        } else {
            $conversation_id = null;
        }
        $order->conversation_id = $conversation_id;
        $order->cart_total = Self::cartTotal($user->id);
        $order->tax = Self::tax($user->id);
        $order->tax_percentage = Self::tax_percentage($user->id);
        $order->discount = $coupon_amount;
        $order->coupon_code = $coupen_code;
        $order->total = $total;
        $order->shipping_method = $shipping_method;
        $order->shipping_charge = round($shippig_charge, 2);
        if ($gtype == 'online') {
            $order->method = $request['payment_method'];
        } elseif ($gtype == 'offline') {
            $gateway =  UserOfflineGateway::where([['user_id', $user->id], ['name', $request['payment_method']]])
                ->first();
            $order->method = $gateway->name;
            if ($request->hasFile('receipt')) {
                $dir = public_path('assets/front/receipt/');
                $receipt = Uploader::upload_picture($dir, $request->file('receipt'));
                $order->receipt = $receipt;
            } else {
                $order->receipt = NULL;
            }
        }
        $userCurrentCurr = app('userCurrentCurr');
        $CurrentCurr = UserCurrency::where('id', $userCurrentCurr->id)->first();
        $order->currency_code = $CurrentCurr->text;
        $order->currency_text_position = $CurrentCurr->text_position;
        $order->currency_sign = $CurrentCurr->symbol;
        $order->currency_position = $CurrentCurr->symbol_position;
        $order['currency_id'] = $CurrentCurr->id;
        $order['order_number'] = Self::generateOrderNumber();
        $order['payment_status'] = $paymentStatus;
        $order['txnid'] = $txnId;
        $order['charge_id'] = $chargeId;
        $order->created_at = $now;
        $order->updated_at = $now;
        $order->save();

        return $order;
    }

    public static function saveOrderedItems($orderId)
    {
        if (Session::has('myfatoorah_user')) {
            $user = Session::get('myfatoorah_user');
        } else {
            $user = getUser();
        }

        $cart = Session::get('cart_' . $user->username);
        $items = [];
        $qty = [];
        $variations = [];
        foreach ($cart as $id => $item) {
            $qty[] = $item['qty'];
            $variations[] = json_encode($item['variations']);
            $items[] = UserItem::findOrFail($item['id']);
        }

        $userCurrentLang = app('userCurrentLang');

        foreach ($items as $key => $item) {
            if (!empty($item->category)) {
                $category = $item->category->name;
            } else {
                $category = '';
            }
            $itemcontent = UserItemContent::where('item_id', $item->id)->where('language_id', $userCurrentLang->id)->first();
            $item_price = currency_converter(($item->flash == 1 ?  ($item->current_price - ($item->current_price * ($item->flash_amount / 100))) : $item->current_price), $item->id);

            $orderderd_variations = json_decode($variations[$key]);
            if ($orderderd_variations) {
                foreach ($orderderd_variations as $vkey => $value) {
                    $option = ProductVariantOption::where('id', intval($value->option_id))->first();
                    if ($option) {
                        $option->stock = $option->stock - $qty[$key];
                        $option->save();
                    }
                }
            } else {
                foreach ($cart as $id => $proId) {
                    $product = UserItem::findOrFail($proId['id']);
                    $stock = $product->stock - $proId['qty'];
                    UserItem::where('id', $proId['id'])->update([
                        'stock' => $stock
                    ]);
                }
            }

            $timeZone = DB::table('user_basic_settings')->where('user_id', $user->id)->value('timezone');

            UserOrderItem::insert([
                'user_order_id' => $orderId,
                'customer_id' => Auth::guard('customer')->check() ? Auth::guard('customer')->user()->id : 9999999,
                'user_id' => $user->id,
                'item_id' => $item->id,
                'title' => $itemcontent->title,
                'sku' => $item->sku,
                'qty' => $qty[$key],
                'variations' => $variations[$key] != 'null' ? $variations[$key] : null,
                'category' => $itemcontent->category_id,
                'price' => $item_price,
                'previous_price' => $item->previous_price,
                'image' => $item->thumbnail,
                'summary' => $itemcontent->summary ?? '',
                'description' => $itemcontent->description ?? '',
                'created_at' => Carbon::now($timeZone),
            ]);
        }
    }

    public static function sendMails($order)
    {
        $user = getUser();
        $data['user'] = $user;
        $data['userBs'] = BasicSetting::where('user_id', $user->id)->first();
        $fileName = \Str::random(4) . time() . '.pdf';
        $dir = public_path('assets/front/invoices/');
        $path = $dir . $fileName;
        @mkdir($dir, 0777, true);
        $data['order']  = $order;
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ])->loadView('pdf.item', $data)->save($path);
        UserOrder::where('id', $order->id)->update([
            'invoice_number' => $fileName
        ]);

        // Send Mail to Buyer
        $mailer = new MegaMailer();
        $data = [
            'toMail' => $order->billing_email,
            'toName' => $order->billing_fname,
            'attachment' => $fileName,
            'customer_name' => $order->billing_fname,
            'order_number' => $order->order_number,
            'order_link' => !empty($order->customer_id) ? "<strong>Order Details:</strong> <a href='" . route('customer.orders-details', ['id' => $order->id, getParam()]) . "'>" . route('customer.orders-details', ['id' => $order->id, getParam()]) . "</a>" : "",
            'website_title' => $data['userBs']->website_title,
            'templateType' => 'product_order',
            'type' => 'productOrder'
        ];
        $mailer->mailFromUser($data);
        Session::forget('cart_' . $user->username);
        Session::forget('coupon');
    }
    public static function generateOrderNumber()
    {
        // Format: YYYY-YY (financial year)
        $year = date('Y');
        $nextYear = date('y', strtotime('+1 year'));
        $prefix = $year . '-' . $nextYear; // Example: 2025-26
    
        // Get last order for this prefix
        $lastOrder = UserOrder::where('order_number', 'LIKE', $prefix . '-%')
            ->orderBy('id', 'DESC')
            ->first();
    
        if ($lastOrder) {
            // Extract the last incremental number
            $lastIncrement = intval(substr($lastOrder->order_number, strlen($prefix) + 1));
            $nextIncrement = str_pad($lastIncrement + 1, 3, '0', STR_PAD_LEFT);
        } else {
            // First order of the year
            $nextIncrement = '001';
        }
    
        return $prefix . '-' . $nextIncrement;
    }

    public static function generateInvoice($order, $user)
    {

        $data['userBs'] = BasicSetting::where('user_id', $user->id)->first();
        $data['user'] = $user;
        $fileName = \Str::random(4) . time() . '.pdf';
        $dir = public_path('assets/front/invoices/');
        $path = $dir . $fileName;
        @mkdir($dir, 0777, true);
        $data['order']  = $order;
        $pdf = PDF::setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'logOutputFile' => storage_path('logs/log.htm'),
            'tempDir' => storage_path('logs/')
        ])->loadView('pdf.item', $data)->save($path);
        UserOrder::where('id', $order->id)->update([
            'invoice_number' => $fileName
        ]);
        return $fileName;
    }

    public static function OrderCompletedMail($order, $user)
    {
        // first, get the mail template information from db
        $mailTemplate = UserEmailTemplate::where([['email_type', 'product_order'], ['user_id', $user->id]])->first();
        $mailSubject = $mailTemplate->email_subject;
        $mailBody = $mailTemplate->email_body;

        // second, send a password reset link to user via email
        $info = DB::table('basic_extendeds')
            ->select('is_smtp', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
            ->first();

        $website_title = $user->shop_name;
        $link = '<a href=' . route('customer.orders-details', ['id' => $order->id, $user->username]) . '>Order Details</a>';
        $mailBody = str_replace('{customer_name}', $order->billing_fname . ' ' . $order->billing_lname, $mailBody);
        $mailBody = str_replace('{order_number}', $order->order_number, $mailBody);

        $mailBody = str_replace('{shipping_fname}', $order->shipping_fname, $mailBody);
        $mailBody = str_replace('{shipping_lname}', $order->shipping_lname, $mailBody);
        $mailBody = str_replace('{shipping_address}', $order->shipping_address, $mailBody);
        $mailBody = str_replace('{shipping_city}', $order->shipping_city, $mailBody);
        $mailBody = str_replace('{shipping_country}', $order->shipping_country, $mailBody);
        $mailBody = str_replace('{shipping_number}', $order->shipping_number, $mailBody);

        $mailBody = str_replace('{billing_fname}', $order->billing_fname, $mailBody);
        $mailBody = str_replace('{billing_lname}', $order->billing_lname, $mailBody);
        $mailBody = str_replace('{billing_address}', $order->billing_address, $mailBody);
        $mailBody = str_replace('{billing_city}', $order->billing_city, $mailBody);
        $mailBody = str_replace('{billing_country}', $order->billing_country, $mailBody);
        $mailBody = str_replace('{billing_number}', $order->billing_number, $mailBody);
        $mailBody = str_replace('{order_link}', $link, $mailBody);
        $mailBody = str_replace('{website_title}', $website_title, $mailBody);

        $data = [];
        $data['smtp_status'] = $info->is_smtp;
        $data['smtp_host'] = $info->smtp_host;
        $data['smtp_port'] = $info->smtp_port;
        $data['encryption'] = $info->encryption;
        $data['smtp_username'] = $info->smtp_username;
        $data['smtp_password'] = $info->smtp_password;

        //mail info in array
        $data['from_mail'] = $info->from_mail;
        $data['recipient'] = $order->billing_email;
        $data['subject'] = $mailSubject;
        $data['body'] = $mailBody;
        $data['invoice'] = public_path('assets/front/invoices/' . $order->invoice_number);
        BasicMailer::sendMail($data);
        return;
    }

    public static function getUserCurrentCurrency($userId)
    {
        return app('userCurrentCurr');
    }

    public static function get_keywords()
    {
        $userCurrentLang = app('userCurrentLang');
        return json_decode($userCurrentLang->keywords, true);
    }
}
