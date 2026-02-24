<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\Language;
use App\Models\User\UserContact;
use App\Models\BulkOrder;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        // first, get the language info from db
        $language = Language::where('code', $request->language)->where('user_id', Auth::guard('web')->user()->id)->first();
        // then, get the service section heading info of that language from db
        $information['data'] = UserContact::where('language_id', $language->id)->where('user_id', Auth::guard('web')->user()->id)->first();
        // get all the languages from db
        return view('user.contact', $information);
    }

    public function update(Request $request, $language)
    {
        $lang = Language::where('code', $language)->where('user_id', Auth::guard('web')->user()->id)->first();
        $data = UserContact::where([
            ['user_id', Auth::guard('web')->user()->id],
            ['language_id', $lang->id]
        ])->first();
        if (is_null($data)) {
            $data = new UserContact;
        }

        $rules = [
            'contact_form_title' => 'nullable|max:255',
            'contact_form_subtitle' => 'nullable|max:255',
            'contact_addresses' => 'nullable',
            'contact_numbers' => 'nullable',
            'contact_mails' => 'nullable|max:255',
            'latitude' => 'nullable|max:255',
            'longitude' => 'nullable|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $data->contact_form_title = $request->contact_form_title;
        $data->contact_form_subtitle = $request->contact_form_subtitle;
        $data->contact_addresses = clean($request->contact_addresses);
        $data->contact_numbers = $request->contact_numbers;
        $data->contact_mails = $request->contact_mails;
        $data->latitude = $request->latitude;
        $data->longitude = $request->longitude;
        $data->language_id = $lang->id;
        $data->user_id = Auth::guard('web')->user()->id;
        $data->save();
        Session::flash('success', __('Updated Successfully'));
        return back();
    }
    
    
    public function bulkOrder(){
        
        $bulkOrders = BulkOrder::latest()->get();
        
        
    return view('user.bulk-order.index', compact('bulkOrders'));
    }
    
    public function bulkOrderShow($id)
    {
    $order = BulkOrder::findOrFail($id);
    $categoriesid = json_decode($order->category_id, true);
    
    $categories = \App\Models\User\UserItemCategory::where('user_id', 11)
    ->whereIn('id', $categoriesid) // Filter by decoded category IDs
    ->select([
        'id',
        'name',
    ])
    ->get();
  
    $products = json_decode($order->item_id, true);
    
    $items = \App\Models\User\UserItem::where('user_items.user_id', 11)
    ->whereIn('user_items.id', $products) // Use whereIn to filter by multiple IDs
    ->join('user_item_contents', 'user_items.id', '=', 'user_item_contents.item_id')
    ->where('user_item_contents.language_id', 17)
    ->select([
        'user_items.id as id',
        'user_item_contents.title',
    ])
    ->get();
   
    $quantities = json_decode($order->quantity, true);
    
    return view('user.bulk-order.show', compact('order','categories','items'));
}

public function bulkOrderDelete($id)
{
    $order = BulkOrder::findOrFail($id);
    $order->delete();
    return redirect()->route('user.bulk-order')->with('success', 'Bulk order deleted successfully.');
}
}
