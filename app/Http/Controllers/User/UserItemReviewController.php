<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\Language;
use App\Models\User\{UserItemContent,UserItem};
use App\Models\User\UserItemSubCategory;
use App\Models\User\UserItemReview;
use App\Models\VariantContent;
use App\Models\VariantOption;
use App\Models\VariantOptionContent;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Session;

class UserItemReviewController extends Controller
{
  public function index(Request $request, $id)
{
    $user_id = auth()->id(); // Logged-in user ID

    // Get item belonging to this user
    $item = UserItem::where('id', $id)
                    ->where('user_id', $user_id)
                    ->firstOrFail();

    // Get item title
    $title = UserItemContent::where('item_id', $item->id)
                ->pluck('title')
                ->first();

    // If your reviews table DOES NOT have item_id
    // $reviews = UserItemReview::orderBy('created_at', 'DESC')->get();

    // If your reviews table HAS item_id, use this instead:
    $reviews = UserItemReview::where('item_id', $id)
                 ->latest()
                 ->get();

    return view('user.item.reviews', compact('item', 'title', 'reviews'));
}

  public function store(Request $request)
{
    $request->validate([
        'item_id' => 'required',
        'rating.*' => 'required|numeric|min:1|max:5',
        'description.*' => 'required'
    ]);

    foreach ($request->name as $key => $name) {
                $input['item_id'] = $request->item_id;
                $input['review'] = $request->rating[$key];
                $input['comment'] = $request->description[$key];
                $input['customer_id'] = 1;
                $data = new UserItemReview();
                $data->create($input);
                $avgreview = UserItemReview::where('item_id', $request->item_id)->avg('review');
                UserItem::find($request->item_id)->update([
                    'rating' => $avgreview
                ]);
                // Session::flash('success', $keywords['Your review has been submitted successfully'] ?? __('Your review has been submitted successfully'));
                // return back();
            
        // \App\Models\UserItemReview::create([
        //     'item_id' => $request->item_id,
        //     'name' => $name,
        //     'rating' => $request->rating[$key],
        //     'description' => $request->description[$key],
        // ]);
    }

    return back()->with('success', 'Reviews Added Successfully!');
}

public function update(Request $request, $id)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'rating' => 'required|numeric|min:1|max:5',
        'description' => 'required|string',
    ]);

    $review = \App\Models\UserItemReview::findOrFail($id);

    $review->update([
        'name' => $request->name,
        'rating' => $request->rating,
        'description' => $request->description,
    ]);

    return back()->with('success', 'Review Updated Successfully!');
}

public function destroy($id)
{
    $review = \App\Models\UserItemReview::findOrFail($id);
    $review->delete();

    return back()->with('success', 'Review Deleted Successfully!');
}

}
