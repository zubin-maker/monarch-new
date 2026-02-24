<?php

namespace App\Http\Controllers\User;

use App\Http\Helpers\UserPermissionHelper;
use App\Models\User\UserCurrency;
use Illuminate\Http\Request;
use App\Models\User\Language;
use App\Models\User\UserItem;
use App\Http\Helpers\Uploader;
use App\Models\User\UserItemImage;
use Illuminate\Support\Facades\DB;
use Mews\Purifier\Facades\Purifier;
use App\Http\Controllers\Controller;
use App\Http\Helpers\BasicMailer;
use App\Http\Helpers\Common;
use App\Models\User;
use App\Models\User\ProductVariantOption;
use App\Models\User\ProductVariantOptionContent;
use App\Models\User\ProductVariation;
use App\Models\User\ProductVariationContent;
use App\Models\User\UserItemContent;
use Illuminate\Support\Facades\Auth;
use App\Models\User\UserItemCategory;
use App\Models\User\UserItemReview;
use Illuminate\Support\Facades\Session;
use App\Models\User\UserItemSubCategory;
use App\Models\User\UserItemVariation;
use App\Models\User\UserOrder;
use App\Models\User\UserShopSetting;
use App\Models\VariantContent;
use App\Models\VariantOptionContent;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $lang = Language::where('code', $request->language)->where('user_id', Auth::guard('web')->user()->id)->first();
        $lang_id = $lang->id;
        $current_package = UserPermissionHelper::currentPackagePermission(Auth::guard('web')->user()->id);
        $data['item_limit'] = $current_package->product_limit;
        $data['total_item'] = UserItemContent::where('language_id', $lang->id)->where('user_id', Auth::guard('web')->user()->id)->count();
        $title = null;
        if ($request->filled('title')) {
            $title = $request->title;
        }

        $data['items'] = DB::table('user_items')->where('user_items.user_id', Auth::guard('web')->user()->id)
            ->Join('user_item_contents', 'user_items.id', '=', 'user_item_contents.item_id')
            ->join('user_item_categories', 'user_item_contents.category_id', '=', 'user_item_categories.id')
            ->select('user_items.*', 'user_items.id AS item_id', 'user_item_contents.*', 'user_item_categories.name AS category')
            ->orderBy('user_items.id', 'DESC')
            ->where('user_item_contents.language_id', '=', $lang_id)
            ->where('user_item_categories.language_id', '=', $lang_id)
            ->when($title, function ($query) use ($title) {
                return $query->where('user_item_contents.title', 'LIKE', '%' . $title . '%');
            })
            ->paginate(10);
        $data['lang_id'] = $lang_id;
        $data['currency'] = UserCurrency::where('user_id', Auth::guard('web')->user()->id)->where('is_default', 1)->first();
        return view('user.item.index', $data);
    }


    public function type(Request $request)
    {
        $user_id = Auth::guard('web')->user()->id;
        $data['digitalCount'] = UserItem::where([['type', 'digital'], ['user_id', $user_id]])->count();
        $data['physicalCount'] = UserItem::where([['type', 'physical'], ['user_id', $user_id]])->count();
        return view('user.item.type', $data);
    }

    public function create(Request $request)
    { 
        $data['lang'] = Language::where('code', $request->language)->where('user_id', Auth::guard('web')->user()->id)->first();
        $data['languages'] = Language::where('user_id', Auth::guard('web')->user()->id)->get();
        $data['currency'] = UserCurrency::where('user_id', Auth::guard('web')->user()->id)->where('is_default', 1)->first();

        $data['categories'] = UserItemCategory::where('language_id', $data['lang']->id)
            ->where('user_id', Auth::guard('web')->user()->id)
            ->where('status', 1)
            ->orderBy('name', 'asc')
            ->get();

        return view('user.item.create', $data);
    }

    public function uploadUpdate(Request $request, $id)
    {
        $img = $request->file('file');
        $allowedExts = array('jpg', 'png', 'jpeg');
        $rules = [
            'file' => [
                function ($attribute, $value, $fail) use ($img, $allowedExts) {
                    if (!empty($img)) {
                        $ext = $img->getClientOriginalExtension();
                        if (!in_array($ext, $allowedExts)) {
                            return $fail(__("Only png, jpg, jpeg image is allowed"));
                        }
                    }
                },
            ],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $validator->getMessageBag()->add('error', 'true');
            return response()->json(['errors' => $validator->errors(), 'id' => 'slider']);
        }
        $product = UserItem::findOrFail($id);
        if ($request->hasFile('file')) {
            $dir = public_path('assets/front/img/product/featured/');
            @unlink($dir . $product->feature_image);
            $product->feature_image = Uploader::upload_picture($dir, $request->file('file'));
            $product->save();
        }
        return response()->json(['status' => "success", "image" => "Product image", 'product' => $product]);
    }
    public function getCategory($langid)
    {
        $category = UserItemCategory::where('language_id', $langid)->where('status', 1)->get();
        return $category;
    }

    // public function store(Request $request)
    // {
    //     $current_package = UserPermissionHelper::currentPackagePermission(Auth::guard('web')->user()->id);
    //     $item_limit = $current_package->product_limit;

    //     $total_item = UserItem::where('user_id', Auth::guard('web')->user()->id)->count();
    //     $total_item = $total_item + 1;

    //     if ($item_limit < $total_item) {
    //         Session::flash('warning', __('Item Limit Exceeded'));
    //         return 'success';
    //     }

    //     $languages = Language::where('user_id', Auth::guard('web')->user()->id)->get();
    //     $defaulLang = Language::where([['user_id', Auth::guard('web')->user()->id], ['is_default', 1]])->first();
    //     $messages = [];
    //     $rules = [];
    //     $sliderImgURLs = $request->has('image') ? $request->image : [];
    //     $allowedExtensions = array('jpg', 'jpeg', 'png', 'svg');
    //     $sliderImgExts = [];
    //     $rules['image'] = [
    //         'required',
    //         function ($attribute, $value, $fail) use ($allowedExtensions, $sliderImgExts) {
    //             if (!empty($sliderImgExts)) {
    //                 foreach ($sliderImgExts as $sliderImgExt) {
    //                     if (!in_array($sliderImgExt, $allowedExtensions)) {
    //                         $fail(__('Only jpeg,png,svg,jpg files are allowed'));
    //                         break;
    //                     }
    //                 }
    //             }
    //         }
    //     ];
    //     // $rules[$code . '_slug'] = [
    //     //         'required',
    //     //         'max:255',
    //     //         Rule::unique('user_item_contents', 'slug')
    //     //             ->where('language_id', $language->id)
    //     //             ->where('user_id', Auth::guard('web')->user()->id)
    //     //     ];
    //     $rules['three_d_image'] = 'nullable';
    //     $rules['thumbnail'] = 'required';
    //     // if product type is 'physical'
    //     if ($request->type == 'physical') {
    //         $rules['stock'] = 'required';
    //         $rules['sku'] = 'required|unique:user_items';
    //     }
    //     $rules['status'] = 'required';
    //     // pplimorp
    //     $rules['current_price'] = 'required|numeric|min:0.01';
    //     $rules['previous_price'] = 'nullable|numeric|min:0.01';
    //     $rules['category'] = 'required';
    //     $messages['image.required'] = __('The slider Image is required') . '.';

    //     $catUIds = [];
    //     $catUIds = UserItemCategory::where('id', $request->category)
    //         ->pluck('unique_id')->toArray();

    //     $categoryLangIds = UserItemCategory::whereIn('unique_id', $catUIds)->pluck('language_id')->toArray();

    //     foreach ($languages as $language) {
    //         $code = $language->code;
    //         if (
    //             $language->is_default == 1 ||
    //             $request->input($code . '_title') ||
    //             $request->input($code . '_label_id') ||
    //             $request->input($code . '_summary') ||
    //             $request->input($code . '_description') ||
    //             $request->input($code . '_meta_title') ||
    //             $request->input($code . '_meta_keywords') ||
    //             $request->input($code . '_meta_description')
    //         ) {
    //             //check category is exist for every input langauge
    //             if (!in_array($language->id, $categoryLangIds)) {
    //                 $rules[$code . '_category'] = 'required';
    //                 $messages[$code . '_category.required'] = __('Please add') . ' ' . $language->name . ' ' . __('content for this category before submitting content in this language.');
    //             }
    //             $rules[$code . '_title'] = [
    //                 'required',
    //                 'max:255',
    //                 function ($attribute, $value, $fail) use ($language, $request, $code) {
    //                     $slug = make_slug($request[$code . '_title']);
    //                     $ics = UserItemContent::where('language_id', $language->id)->where('user_id', Auth::guard('web')->user()->id)->get();
    //                     foreach ($ics as $key => $ic) {
    //                         if (strtolower($slug) == strtolower($ic->slug)) {
    //                             $fail(__('The title field must be unique for') . ' ' . $language->name . ' ' . __('language'));
    //                         }
    //                     }
    //                 }
    //             ];
    //             $rules[$code . '_summary'] = 'required';
    //             $rules[$code . '_description'] = 'required';
    //         }
    //         $messages[$language->code . '_title.required'] = __('The title field is required for') . ' ' . $language->name . ' ' . __('language');
    //         $messages[$language->code . '_summary.required'] = __('The summary field is required for') . ' ' . $language->name . ' ' . __('language');
    //         $messages[$language->code . '_description.required'] = __('The description field is required for') . ' ' . $language->name . ' ' . __('language');
    //     }

    //     // if product type is 'digital'
    //     if ($request->type == 'digital') {
    //         $rules['file_type'] = 'required';
    //         // if 'file upload' is chosen
    //         if ($request->has('file_type') && $request->file_type == 'upload') {
    //             $rules['download_file'] = 'required|mimes:zip';
    //         } elseif ($request->has('file_type') && $request->file_type == 'link') {
    //             $rules['download_link'] = 'required';
    //         }
    //     }

    //     $validator = Validator::make($request->all(), $rules, $messages);
    //     if (!empty($sliderImgURLs)) {
    //         foreach ($sliderImgURLs as $sliderImgURL) {
    //             $n = strrpos($sliderImgURL, ".");
    //             $extension = ($n === false) ? "" : substr($sliderImgURL, $n + 1);
    //             array_push($sliderImgExts, $extension);
    //         }
    //     }

    //     if ($validator->fails()) {
    //         return Response::json([
    //             'errors' => $validator->getMessageBag()->toArray()
    //         ], 400);
    //     }

    //     // if the type is digital && 'upload file' method is selected, then store the downloadable file
    //     if ($request->type == 'digital' && $request->file_type == 'upload') {
    //         if ($request->hasFile('download_file')) {
    //             $digitalFile = $request->file('download_file');
    //             $filename = time() . '-' . uniqid() . "." . $digitalFile->extension();
    //             $directory = storage_path('/digital_products');
    //             @mkdir($directory, 0775, true);
    //             $digitalFile->move($directory, $filename);
    //         }
    //     }

    //     $user_currency = UserCurrency::where('is_default', 1)->where('user_id', Auth::guard('web')->user()->id)->first();
    //     $currency_id = $user_currency->id;

    //     $item = new UserItem();
        
    //     $thumbnail = $request->file('thumbnail');
    //     if ($request->hasFile('thumbnail')) {
    //         $dir = public_path('assets/front/img/user/items/thumbnail/');

    //         $thumbnail_name = uniqid() . '.webp';
    //         $image = Image::make($thumbnail->getRealPath());

    //         @mkdir($dir, 0775, true);
    //         $image->resize(255, 255);
    //         $image->save($dir . $thumbnail_name);
    //     }

    //     $sliderDir = public_path('assets/front/img/user/items/slider-images/');
    //     @mkdir($sliderDir, 0775, true);
    //     $item->user_id = Auth::guard('web')->user()->id;
    //     $item->stock = $request->stock;
    //     $item->sku = $request->sku;
    //     $item->thumbnail = $thumbnail_name;
    //     $item->status = $request->status;
    //     $item->current_price = $request->current_price;
    //     $item->previous_price = $request->previous_price;
    //     $item->currency_id = $currency_id;
    //     $item->type = $request->type;
    //     $item->download_file = $filename ?? null;
    //     $item->download_link = $request->download_link;
    //     $item->background_color = $request->background_color;
    //     $item->three_d_image = $request->filled('three_d_image') ? $request->three_d_image : null;
    //              $item->you_tube = $request->filled('you_tube') ? $request->you_tube : null;
    //     $item->save();
    //     foreach ($request->image as $value) {
    //         UserItemImage::create([
    //             'item_id' => $item->id,
    //             'image' => $value,
    //         ]);
    //     }
    //     // store varations as json
    //     $catUnique_id = UserItemCategory::where('id', $request->category)
    //         ->pluck('unique_id')->first();
    //     $subcatUnique_id = UserItemSubCategory::where('id', $request->subcategory)
    //         ->pluck('unique_id')->first();

    //     foreach ($languages as $language) {
    //         $code = $language->code;
    //         if (
    //             $language->is_default == 1 ||
    //             $request->input($code . '_title') ||
    //             $request->input($code . '_label_id') ||
    //             $request->input($code . '_summary') ||
    //             $request->input($code . '_description') ||
    //             $request->input($code . '_meta_keywords') ||
    //             $request->input($code . '_meta_description')
    //         ) {
    //             $categoryId = UserItemCategory::where([['language_id', $language->id], ['unique_id', $catUnique_id]])->pluck('id')->first();
    //             $subcategoryId = UserItemSubCategory::where([['language_id', $language->id], ['unique_id', $subcatUnique_id]])->pluck('id')->first();

    //             $adContent = new UserItemContent();
    //             $adContent->item_id = $item->id;
    //             $adContent->user_id = Auth::guard('web')->user()->id;
    //             $adContent->language_id = $language->id;
    //             $adContent->category_id = $categoryId;
    //             $adContent->subcategory_id = $subcategoryId;
    //             $adContent->label_id = $request[$code . '_label_id'];
    //             $adContent->title = $request[$code . '_title'];
    //           $adContent->slug = $request->filled($code . '_slug')
    //             ? make_slug($request[$code . '_slug'])
    //             : make_slug($request[$code . '_title']);
    //             $adContent->summary = Purifier::clean($request[$code . '_summary'], 'youtube');
    //             $adContent->description = Purifier::clean($request[$code . '_description'], 'youtube');
    //             $adContent->meta_keywords = $request[$code . '_meta_keywords'];
    //             $adContent->meta_description = $request[$code . '_meta_description'];
    //             $adContent->save();
    //         }
    //     }
    //     Session::flash('success', __('Created successfully'));
    //     return 'success';
    // }
         public function store(Request $request)
     {
    // Check item limit based on user's package
    $current_package = UserPermissionHelper::currentPackagePermission(Auth::guard('web')->user()->id);
    $item_limit = $current_package->product_limit;
    $total_item = UserItem::where('user_id', Auth::guard('web')->user()->id)->count();

    if ($item_limit <= $total_item) {
        Session::flash('warning', __('Item Limit Exceeded'));
        return 'success'; // This seems intentional in your code (AJAX response)
    }

    $user = Auth::guard('web')->user();
    $languages = Language::where('user_id', $user->id)->get();

    // Validation rules
    $rules = [
        'thumbnail'       => 'required|image|mimes:jpeg,png,jpg,webp',
       'image'           => 'nullable|array',
        'image.*'         => 'string',
        'status'          => 'required|in:0,1',
        'current_price'   => 'required|numeric|min:0.01',
        'previous_price'  => 'nullable|numeric|min:0.01',
        'category'        => 'required|exists:user_item_categories,id',
        'three_d_image'   => 'nullable|url',
        'you_tube'        => 'nullable|url',
    ];

    $messages = [
        'image.required' => __('The slider images field is required.'),
        'thumbnail.required' => __('The thumbnail image is required.'),
    ];

    // Type-specific rules
    if ($request->type === 'physical') {
        $rules['stock'] = 'required|integer|min:0';
        $rules['sku']   = 'required|unique:user_items,sku';
    }

    if ($request->type === 'digital') {
        $rules['file_type'] = 'required|in:upload,link';

        if ($request->file_type === 'upload') {
            $rules['download_file'] = 'required|mimes:zip';
        } elseif ($request->file_type === 'link') {
            $rules['download_link'] = 'required|url';
        }
    }

    // Language-specific validation
    foreach ($languages as $language) {
        $code = $language->code;

        $hasAnyContent = $language->is_default ||
            $request->filled("{$code}_title") ||
            $request->filled("{$code}_summary") ||
            $request->filled("{$code}_description");

        if ($hasAnyContent || $language->is_default) {
            $rules["{$code}_title"] = 'required|max:255';
            $rules["{$code}_summary"] = 'required';
            $rules["{$code}_description"] = 'required';
        }

        $messages["{$code}_title.required"] = __('The title field is required for') . ' ' . $language->name . ' ' . __('language');
        $messages["{$code}_summary.required"] = __('The summary field is required for') . ' ' . $language->name . ' ' . __('language');
        $messages["{$code}_description.required"] = __('The description field is required for') . ' ' . $language->name . ' ' . __('language');
    }

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    // Handle thumbnail upload (converted to webp)
    $thumbnailName = 'default.jpg'; // fallback
    if ($request->hasFile('thumbnail')) {
        $thumbnail = $request->file('thumbnail');
        $thumbnailName = 'thumb_' . uniqid() . '.webp';
        $thumbnailPath = public_path('assets/front/img/user/items/thumbnail/');

        if (!file_exists($thumbnailPath)) {
            mkdir($thumbnailPath, 0775, true);
        }

        Image::make($thumbnail)->resize(255, 255)->save($thumbnailPath . $thumbnailName);
    }

    // Handle digital file upload
    $downloadFilename = null;
    if ($request->type === 'digital' && $request->file_type === 'upload' && $request->hasFile('download_file')) {
        $file = $request->file('download_file');
        $downloadFilename = time() . '_' . uniqid() . '.zip';
        $directory = storage_path('app/digital_products');

        if (!file_exists($directory)) {
            mkdir($directory, 0775, true);
        }

        $file->move($directory, $downloadFilename);
    }

    // Get default currency
    $currency = UserCurrency::where('user_id', $user->id)->where('is_default', 1)->first();

    // Create the main item
    $item = new UserItem();
    $item->user_id          = $user->id;
    $item->type             = $request->type;
    $item->thumbnail        = $thumbnailName;
    $item->status           = $request->status;
    $item->current_price    = $request->current_price;
    $item->previous_price   = $request->previous_price;
    $item->stock            = $request->filled('stock') ? $request->stock : null;
    $item->sku              = $request->filled('sku') ? $request->sku : null;
    $item->currency_id      = $currency->id;
    $item->download_file    = $downloadFilename;
    $item->download_link    = $request->filled('download_link') ? $request->download_link : null;
    $item->three_d_image    = $request->filled('three_d_image') ? $request->three_d_image : null;
    $item->you_tube         = $request->filled('you_tube') ? $request->you_tube : null;
    $item->background_color = $request->filled('background_color') ? $request->background_color : null;
    $item->save();

    // Store slider images (from Dropzone)
    if ($request->has('image') && is_array($request->image)) {
        foreach ($request->image as $sliderImage) {
            UserItemImage::create([
                'item_id' => $item->id,
                'image'   => $sliderImage,
            ]);
        }
    }

    // Get category and subcategory unique IDs for cross-language consistency
    $mainCategory = UserItemCategory::find($request->category);
    $catUniqueId = $mainCategory->unique_id;

    $subcatUniqueId = null;
    if ($request->filled('subcategory')) {
        $subcat = UserItemSubCategory::find($request->subcategory);
        $subcatUniqueId = $subcat?->unique_id;
    }

    // Store content for each language
    foreach ($languages as $language) {
        $code = $language->code;

        $hasContent = $language->is_default ||
            $request->filled("{$code}_title") ||
            $request->filled("{$code}_summary") ||
            $request->filled("{$code}_description");

        if ($hasContent) {
            // Get category/subcategory ID for this language
            $categoryId = UserItemCategory::where('language_id', $language->id)
                ->where('unique_id', $catUniqueId)
                ->value('id');

            $subcategoryId = $subcatUniqueId
                ? UserItemSubCategory::where('language_id', $language->id)
                    ->where('unique_id', $subcatUniqueId)
                    ->value('id')
                : null;

            $content = new UserItemContent();
            $content->item_id         = $item->id;
            $content->user_id         = $user->id;
            $content->language_id     = $language->id;
            $content->category_id     = $categoryId;
            $content->subcategory_id  = $subcategoryId;
            $content->label_id        = $request->filled("{$code}_label_id") ? $request["{$code}_label_id"] : null;
            $content->title           = $request["{$code}_title"];
            $content->slug            = $request->filled("{$code}_slug")
                ? make_slug($request["{$code}_slug"])
                : make_slug($request["{$code}_title"]);
            $content->summary         = Purifier::clean($request["{$code}_summary"], 'youtube');
            $content->description     = Purifier::clean($request["{$code}_description"], 'youtube');

            // Correctly store meta fields
            $content->meta_title       = $request->filled("{$code}_meta_title") ? $request["{$code}_meta_title"] : null;
            $content->meta_keywords    = $request->filled("{$code}_meta_keywords") ? $request["{$code}_meta_keywords"] : null;
            $content->meta_description = $request->filled("{$code}_meta_description") ? $request["{$code}_meta_description"] : null;

            $content->save();
        }
    }

    Session::flash('success', __('Item created successfully!'));
    return 'success';
}
    
    public function edit(Request $request, $id)
    {
        
        $currentLang = Language::where('code', $request->language)->pluck('id')->firstOrFail();
        $user_id = Auth::guard('web')->user()->id;
        $data['languages'] = Language::where('user_id', $user_id)->get();
        $data['item'] = UserItem::where([['id', $id], ['user_id', $user_id]])->firstOrFail();

        $data['title'] = UserItemContent::where([['item_id', $data['item']->id], ['language_id', $currentLang]])->pluck('title')->first();

        $current_package = UserPermissionHelper::currentPackagePermission($user_id);
        $item_limit = $current_package->product_limit;
        $lang = Language::where('code', $request->language)->where('user_id', $user_id)->first();
        $data['lang'] = $lang;
        $total_item = UserItem::where('user_id', $user_id)->count();
        if ($total_item > $item_limit) {
            Session::flash('warning', __('Delete item to enable editing'));
            return redirect()->back()->with('success');
        }

        $data['currency'] = UserCurrency::where('user_id', $user_id)->where('is_default', 1)->first();

        return view('user.item.edit', $data);
    }


    



// public function update(Request $request)
// {
//     $item = UserItem::findOrFail($request->item_id);

//     // Validation
//     $rules = [
//         'title' => 'nullable|max:255|unique:user_item_contents,title,' . $request->item_id . ',item_id,language_id,11',
//         'slug' => 'nullable|unique:user_item_contents,slug,' . $request->item_id . ',item_id,language_id,11',
//         'summary' => 'nullable',
//         'description' => 'nullable',
//         'category' => 'required|exists:user_item_categories,id',
//         'current_price' => 'required|numeric|min:0.01',
//         'status' => 'required|in:0,1',
//     ];

//     if ($item->type == 'physical') {
//         $rules['stock'] = 'required|integer|min:0';
//         $rules['sku'] = 'required|unique:user_items,sku,' . $item->id;
//     }

//     if ($item->type == 'digital') {
//         if ($request->file_type == 'upload') {
//             $rules['download_file'] = 'required|mimes:zip|max:51200';
//         } elseif ($request->file_type == 'link') {
//             $rules['download_link'] = 'required|url';
//         }
//     }

//     $validator = Validator::make($request->all(), $rules);

//     if ($validator->fails()) {
//         return response()->json([
//             'success' => false,
//             'errors' => $validator->errors()
//         ], 422);
//     }

//     /** DIGITAL FILE HANDLING **/
//     if ($item->type == 'digital') {
//         if ($request->file_type == 'upload' && $request->hasFile('download_file')) {

//             if ($item->download_file) {
//                 @unlink(storage_path('digital_products/' . $item->download_file));
//             }

//             $file = $request->file('download_file');
//             $filename = time() . '-' . uniqid() . '.zip';
//             $directory = storage_path('digital_products/');

//             if (!file_exists($directory)) {
//                 mkdir($directory, 0775, true);
//             }

//             $file->move($directory, $filename);
//             $item->download_file = $filename;
//             $item->download_link = null;

//         } elseif ($request->file_type == 'link') {

//             if ($item->download_file) {
//                 @unlink(storage_path('digital_products/' . $item->download_file));
//                 $item->download_file = null;
//             }

//             $item->download_link = $request->download_link;
//         }
//     }

//     /** THUMBNAIL **/
//     if ($request->hasFile('thumbnail')) {

//         $dir = public_path('assets/front/img/user/items/thumbnail/');

//         if (!file_exists($dir)) {
//             mkdir($dir, 0775, true);
//         }

//         if ($item->thumbnail) {
//             @unlink($dir . $item->thumbnail);
//         }

//         $thumbnail = $request->file('thumbnail');
//         $name = uniqid() . '.webp';

//         $img = Image::make($thumbnail->getRealPath())
//             ->resize(255, 255, function ($c) {
//                 $c->aspectRatio();
//                 $c->upsize();
//             })
//             ->encode('webp', 80);

//         $img->save($dir . $name);
//         $item->thumbnail = $name;
//     }

//     /** UPDATE MAIN ITEM **/
//     $item->stock = $request->stock ?? $item->stock;
//     $item->sku = $request->sku ?? $item->sku;
//     $item->status = $request->status;
//     $item->current_price = $request->current_price;
//     $item->previous_price = $request->previous_price ?: null;
//     $item->three_d_image = $request->three_d_image ?: null;
//     $item->you_tube = $request->you_tube ?: null;
//     $item->save();

//     /** UPDATE LANGUAGE CONTENT (ID = 11) **/
//     UserItemContent::updateOrCreate(
//         [
//             'item_id' => $item->id,
//             'language_id' => 11
//         ],
//         [
//             'user_id' => auth()->id(),
//             'category_id' => $request->category,
//             'subcategory_id' => $request->subcategory ?? null,
//             'label_id' => $request->label_id ?? null,
//             'title' => $request->title,
//             'slug' => make_slug($request->slug),
//             'summary' => Purifier::clean($request->summary),
//             'description' => Purifier::clean($request->description, 'youtube'),
//             'meta_title' => $request->meta_title,
//             'meta_keywords' => $request->meta_keywords ?? null,
//             'meta_description' => $request->meta_description ?? null,
//         ]
//     );

//     Session::flash('success', 'Item updated successfully!');

//     return response()->json([
//         'success' => true,
//         'message' => 'Item updated successfully!'
//     ]);
// }

   public function update(Request $request)
    {
    $item = UserItem::findOrFail($request->item_id);
    $user = Auth::guard('web')->user();

    $languages = Language::where('user_id', $user->id)->get();
    $rules = [];
    $messages = [];

    // Base rules
    $rules['current_price'] = 'required|numeric|min:0.01';
    $rules['previous_price'] = 'nullable|numeric|min:0.01';
    $rules['category'] = 'required';

    // Type-specific rules
    if ($item->type === 'physical') {
        $rules['stock'] = 'required|integer|min:0';
        $rules['sku'] = 'required|unique:user_items,sku,' . $item->id;
    }

    if ($item->type === 'digital') {
        if ($request->file_type === 'upload' && empty($item->download_file) && !$request->hasFile('download_file')) {
            $rules['download_file'] = 'required|mimes:zip';
        } elseif ($request->file_type === 'link') {
            $rules['download_link'] = 'required|url';
        }
    }

    // Slider image validation
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'svg'];
    $sliderImgURLs = $request->image ?? [];
    if (!empty($sliderImgURLs)) {
        $rules['image'] = function ($attribute, $value, $fail) use ($allowedExtensions, $sliderImgURLs) {
            foreach ($sliderImgURLs as $sliderImgURL) {
                $ext = pathinfo($sliderImgURL, PATHINFO_EXTENSION);
                if (!in_array(strtolower($ext), $allowedExtensions)) {
                    $fail(__('Only jpeg,png,svg,jpg files are allowed'));
                    break;
                }
            }
        };
    }

    // Language-specific validation
    $catUIds = UserItemCategory::where('id', $request->category)->pluck('unique_id')->toArray();
    $categoryLangIds = UserItemCategory::whereIn('unique_id', $catUIds)->pluck('language_id')->toArray();

    foreach ($languages as $language) {
        $code = $language->code;
        $langName = ' ' . $language->name . ' ' . __('language');

        if ($language->is_default || $request->filled("{$code}_title") || $request->filled("{$code}_summary") || $request->filled("{$code}_description")) {
            if (!in_array($language->id, $categoryLangIds)) {
                $rules["{$code}_category"] = 'required';
                $messages["{$code}_category.required"] = __('Please add') . ' ' . $language->name . ' ' . __('content for this category before submitting content in this language.');
            }

            $rules["{$code}_title"] = [
                'required',
                'max:255',
                function ($attribute, $value, $fail) use ($language, $request, $code) {
                    $slug = make_slug($request[$code . '_title']);
                    $ics = UserItemContent::where('language_id', $language->id)
                        ->where('user_id', Auth::guard('web')->user()->id)
                        ->where('item_id', '<>', $request->item_id)
                        ->get();
                    foreach ($ics as $ic) {
                        if (strtolower($slug) == strtolower($ic->slug)) {
                            $fail(__('The title field must be unique for') . ' ' . $language->name . ' ' . __('language'));
                        }
                    }
                }
            ];

            $rules["{$code}_summary"] = 'required';
            $rules["{$code}_description"] = 'required';

            $messages["{$code}_title.required"] = __('The title field is required for') . $langName;
            $messages["{$code}_summary.required"] = __('The summary field is required for') . $langName;
            $messages["{$code}_description.required"] = __('The description field is required for') . $langName;
        }
    }

    $validator = Validator::make($request->all(), $rules, $messages);
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    // Handle thumbnail upload
    if ($request->hasFile('thumbnail')) {
        $dir = public_path('assets/front/img/user/items/thumbnail/');
        @mkdir($dir, 0775, true);
        @unlink($dir . $item->thumbnail);

        $thumbnail_name = uniqid() . '.webp';
        $image = Image::make($request->file('thumbnail')->getRealPath());
        $image->resize(255, 255);
        $image->save($dir . $thumbnail_name);
        $item->thumbnail = $thumbnail_name;
    }

    // Handle digital files
    if ($item->type === 'digital') {
        $directory = storage_path('digital_products/');
        @mkdir($directory, 0775, true);

        if ($request->file_type === 'upload' && $request->hasFile('download_file')) {
            @unlink($directory . $item->download_file);
            $filename = time() . '-' . uniqid() . '.' . $request->file('download_file')->extension();
            $request->file('download_file')->move($directory, $filename);
            $item->download_file = $filename;
            $item->download_link = null;
        } elseif ($request->file_type === 'link') {
            @unlink($directory . $item->download_file);
            $item->download_file = null;
            $item->download_link = $request->download_link;
        }
    }

    // Update main item fields
    $item->stock = $request->stock ?? $item->stock;
    $item->sku = $request->sku ?? $item->sku;
    $item->status = $request->status;
    $item->current_price = $request->current_price;
    $item->previous_price = $request->previous_price;
    $item->type = $request->type;
    $item->background_color = $request->background_color ?? null;
    $item->three_d_image = $request->three_d_image ?? null;
    $item->you_tube = $request->you_tube ?? null;
    $item->fake_review_count = $request->fake_review_count;
    $item->save();

    // Update slider images
    if ($request->image) {
        // Optionally, you may want to delete old slider images here
        foreach ($request->image as $sliderImage) {
            UserItemImage::create([
                'item_id' => $item->id,
                'image' => $sliderImage,
            ]);
        }
    }

    // Category and subcategory unique IDs
    $catUnique_id = UserItemCategory::where('id', $request->category)->value('unique_id');
    $subcatUnique_id = $request->subcategory ? UserItemSubCategory::where('id', $request->subcategory)->value('unique_id') : null;

    // Update content for all languages
    foreach ($languages as $language) {
        $code = $language->code;

        $categoryId = UserItemCategory::where([
            ['language_id', $language->id],
            ['unique_id', $catUnique_id]
        ])->value('id');

        $subcategoryId = $subcatUnique_id ? UserItemSubCategory::where([
            ['language_id', $language->id],
            ['unique_id', $subcatUnique_id]
        ])->value('id') : null;

        $adContent = UserItemContent::firstOrNew([
            'item_id' => $item->id,
            'language_id' => $language->id,
        ]);
        $featuresText = $request->input("{$code}_features", '');
        $featuresArray = array_filter(array_map('trim', explode("\n", $featuresText)));
        $adContent->features = !empty($featuresArray) ? $featuresArray : null;
      if ($request->filled("{$code}_title") || 
            $request->filled("{$code}_summary") || 
            $request->filled("{$code}_description") || 
            $request->filled("{$code}_meta_title") || 
            $request->filled("{$code}_meta_keywords") || 
            $request->filled("{$code}_meta_description") ||
            !empty($featuresArray)) {   // Also save if only features are filled

            $adContent->user_id          = $user->id;
            $adContent->category_id      = $categoryId;
            $adContent->subcategory_id   = $subcategoryId;
            $adContent->label_id         = $request->input("{$code}_label_id");
            $adContent->title            = $request->input("{$code}_title");
            $adContent->slug             = $request->filled("{$code}_slug")
                ? make_slug($request["{$code}_slug"])
                : make_slug($request["{$code}_title"]);
            $adContent->summary          = Purifier::clean($request->input("{$code}_summary"), 'youtube');
            $adContent->description      = Purifier::clean($request->input("{$code}_description"), 'youtube');
            $adContent->meta_title       = $request->filled("{$code}_meta_title") ? $request->input("{$code}_meta_title") : $adContent->meta_title;
            $adContent->meta_keywords    = $request->input("{$code}_meta_keywords");
            $adContent->meta_description = $request->input("{$code}_meta_description");

            // Save features as JSON array
            $adContent->features = !empty($featuresArray) ? $featuresArray : null;

            $adContent->save();
        }
    }

    Session::flash('success', __('Updated Successfully'));
    return "success";
}



    public function feature(Request $request)
    {

        $item = UserItem::findOrFail($request->item_id);
        $item->is_feature = $request->is_feature;
        $item->save();

        Session::flash('success', __('Updated Successfully'));
        return back();
    }
    public function specialOffer(Request $request)
    {
        $item = UserItem::findOrFail($request->item_id);
        $item->secial_offer = $request->secial_offer;
        $item->save();
        if ($request->secial_offer == 1) {
            Session::flash('success', 'Item added to Special offer successfully!');
        } else {
            Session::flash('success', 'Item remove from Special offer successfully!');
        }
        return back();
    }


    public function delete(Request $request)
    {
        $item = UserItem::findOrFail($request->item_id);
        @unlink(public_path('assets/front/img/user/items/thumbnail/') . $item->thumbnail);
        foreach ($item->sliders as $key => $image) {
            @unlink(public_path('assets/front/img/user/items/slider-images/') . $image->image);
            $image->delete();
        }
        $item->itemContents()->delete();

        //delete reviews
        $reviews = UserItemReview::where('item_id', $item->id)->get();
        foreach ($reviews as $review) {
            $review->delete();
        }

        //delete product variation
        $product_variations = ProductVariation::where('item_id', $request->item_id)->get();
        foreach ($product_variations as $product_variation) {

            //delete product variation contents
            $product_variation_contents = ProductVariationContent::where('product_variation_id', $product_variation->id)->get();
            foreach ($product_variation_contents as $product_variation_content) {
                $product_variation_content->delete();
            }

            //delete product_variant_options
            $product_variant_options = ProductVariantOption::where('product_variation_id', $product_variation->id)->get();
            foreach ($product_variant_options as $product_variant_option) {
                //delete product_variant_option_contents
                $product_variant_option_contents = ProductVariantOptionContent::where('product_variant_option_id', $product_variant_option->id)->get();
                foreach ($product_variant_option_contents as $product_variant_option_content) {
                    $product_variant_option_content->delete();
                }

                $product_variant_option->delete();
            }

            $product_variation->delete();
        }

        $item->delete();
        @unlink(storage_path('digital_products/') . $item->download_file);
        Session::flash('success', __('Deleted successfully'));
        return back();
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->ids;
        foreach ($ids as $id) {
            $item = UserItem::where('id', $id)->first();
            if ($item) {
                @unlink(public_path('assets/front/img/user/items/thumbnail/') . $item->thumbnail);
                foreach ($item->sliders as $key => $image) {
                    @unlink(public_path('assets/front/img/user/items/slider-images/') . $image->image);
                    $image->delete();
                }
                $item->itemContents()->delete();


                //delete product variation
                $product_variations = ProductVariation::where('item_id', $id)->get();
                foreach ($product_variations as $product_variation) {

                    //delete product variation contents
                    $product_variation_contents = ProductVariationContent::where('product_variation_id', $product_variation->id)->get();
                    foreach ($product_variation_contents as $product_variation_content) {
                        $product_variation_content->delete();
                    }

                    //delete product_variant_options
                    $product_variant_options = ProductVariantOption::where('product_variation_id', $product_variation->id)->get();
                    foreach ($product_variant_options as $product_variant_option) {
                        //delete product_variant_option_contents
                        $product_variant_option_contents = ProductVariantOptionContent::where('product_variant_option_id', $product_variant_option->id)->get();
                        foreach ($product_variant_option_contents as $product_variant_option_content) {
                            $product_variant_option_content->delete();
                        }

                        $product_variant_option->delete();
                    }

                    $product_variation->delete();
                }

                @unlink(storage_path('digital_products/') . $item->download_file);
                $item->delete();
            }
        }
        Session::flash('success', __('Deleted successfully'));
        return "success";
    }
    public function variants($pid, $lang)
    {
        $variations = UserItemVariation::where('item_content_id', $pid)->where('language_id', $lang)->get();
        $variants = [];
        $i = 0;
        foreach ($variations as $key => $value) {
            $variants[$i] = [
                'name' => str_replace("_", " ", $value->variant_name),
                'uniqid' => uniqid(),
            ];
            $option_names = json_decode($value->option_name);
            $option_prices = json_decode($value->option_price);
            $option_stocks = json_decode($value->option_stock);
            $j = 0;
            foreach ($option_names as $okey => $val) {
                $variants[$i]['options'][$j]['name'] = $val;
                $variants[$i]['options'][$j]['price'] = $option_prices[$okey];
                $variants[$i]['options'][$j]['stock'] = $option_stocks[$okey];
                $j++;
            }
            $i++;
        }
        return response()->json($variants);
    }

    public function variations($id, Request $request)
    {
       
        $currentLang = Language::where('code', $request->language)->pluck('id')->firstOrFail();
        $current_package = UserPermissionHelper::currentPackagePermission(Auth::guard('web')->user()->id);
        $item_limit = $current_package->product_limit;
        $total_item = UserItem::where('user_id', Auth::guard('web')->user()->id)->count();
        if ($item_limit < $total_item) {
            Session::flash('warning', __('Item limit exceeded'));
            return back();
        }

        $id = (int)$id;
        $data['item_id'] = $id;
        $data['variations'] = ProductVariation::where('item_id', $id)->get();
        
        

        $data['title'] = UserItemContent::where([['item_id', $id], ['language_id', $currentLang]])->pluck('title')->first();
        $data['currency'] = UserCurrency::where('user_id', Auth::guard('web')->user()->id)->where('is_default', 1)->first();

        return view('user.item.variation', $data);
    }

    public function getVariation(Request $request)
    {
        $variation_content = VariantContent::where('id', $request->variation_content_id)->select('variant_id')->first();
        $variant_option_contents = VariantOptionContent::where([
            ['variant_id', $variation_content->variant_id],
            ['language_id', $request->language_id],
        ])->get();
        return $variant_option_contents;
    }

    public function variationStore(Request $request)
    {
        // Get the user ID and user languages
        $user_id = Auth::guard('web')->user()->id;

        // Initialize validation rules and messages arrays
        $rules = [];
        $messages = [];

        // Default rules for variations and options
        $rules['unique_id.*'] = 'required';
        $messages['unique_id.*.required'] = __('The variation ID is required');
        $rules["*variation_name.*"] = 'required';
        $rules["*option_name.*"] = 'required';
        $messages["*variation_name.*.required"] = __('The variation name is required.');
        $messages["*option_name.*.required"] = __('The variation name is required.');

        // Rules for price and stock
        $rules['*_price.*'] = 'required|numeric';
        $messages['*_price.*.required'] = __('The price is required');
        $messages['*_price.*.numeric'] = __('The price must be a number');

        $rules['*_stock.*'] = 'required|numeric';
        $messages['*_stock.*.required'] = __('The stock is required');
        $messages['*_stock.*.numeric'] = __('The stock must be a number');

        // Create validator instance
        $validator = Validator::make($request->all(), $rules, $messages);

        // Check if validation fails
        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }

        $user_id = Auth::guard('web')->user()->id;
        $languages = Language::where('user_id', $user_id)->get();
        $item_id = $request->item_id;
        $unique_ids = $request->unique_id;
        if (!is_null($unique_ids)) {
            foreach ($unique_ids as $index => $unique_id) {
                // Create the main variation
                $variation = ProductVariation::firstOrNew(['unique_id' => $unique_id]);
                if (!$variation->exists) {
                    $variation->user_id = $user_id;
                    $variation->item_id = $item_id;
                    $variation->unique_id = $unique_id;
                    $variation->save();
                }

                // Save the variation content for each language
                $variation_name_key = $unique_id . '_variation_name';
                $selected_variation_ids = $request->input($variation_name_key) ?? [];

                // Fetch existing variation content IDs to identify outdated ones
                $existingContentIds = ProductVariationContent::where('product_variation_id', $variation->id)
                    ->where('user_id', $user_id)
                    ->pluck('variation_name')
                    ->toArray();

                // Determine which IDs to delete (those not in the current selection)
                $idsToDelete = array_diff($existingContentIds, $selected_variation_ids);

                // Delete outdated ProductVariationContent records
                ProductVariationContent::where('product_variation_id', $variation->id)
                    ->whereIn('variation_name', $idsToDelete)
                    ->delete();


                $_variant = VariantContent::whereIn('id', $selected_variation_ids)->select('variant_id')->get();

                foreach ($languages as $language) {
                    foreach ($_variant as $variant) {
                        // Check for an existing variation name in the language
                        $variation_name = VariantContent::where([
                            ['variant_id', $variant->variant_id],
                            ['language_id', $language->id],
                        ])->select('id')->first();

                        if ($variation_name) {
                            // Find or create the variation content for this language
                            $variationContent = ProductVariationContent::firstOrNew([
                                'user_id' => $user_id,
                                'product_variation_id' => $variation->id,
                                'language_id' => $language->id,
                                'variation_name' => $variation_name->id,
                            ]);

                            // Update necessary fields
                            $variationContent->item_id = $item_id;
                            $variationContent->save();
                        }
                    }
                }


                // Save the variant options
                $prices = $request->input("{$unique_id}_price") ?? [];
                $stocks = $request->input("{$unique_id}_stock") ?? [];
                $option_ids = $request->input("{$unique_id}_optionid") ?? [];

                foreach ($prices as $option_index => $price) {

                    $stock = $stocks[$option_index] ?? null;
                    $option_id = $option_ids[$option_index] ?? null;


                    if ($price !== null && $stock !== null) {
                        if ($option_id == 'new') {
                            $variantOption = new ProductVariantOption();
                        } else {
                            $variantOption = ProductVariantOption::where('id', $option_id)->first();
                            // If the $variantOption is still null, create a new instance
                            if (is_null($variantOption)) {
                                $variantOption = new ProductVariantOption();
                            }
                        }
                        $variantOption->product_variation_id = $variation->id;
                        $variantOption->unique_id = $variation->unique_id;
                        $variantOption->user_id = $user_id;
                        $variantOption->item_id = $item_id;
                        $variantOption->price = $price ?? 0;
                        $variantOption->stock = $stock ?? 0;
                        $variantOption->save();

                        $option_name_key = "{$unique_id}_option_name";
                        $option_name = $request->$option_name_key[$option_index] ?? null;

                        $_variant_options = VariantOptionContent::where('id', $option_name)->select('variant_id', 'index_key')->first();


                        foreach ($languages as $language) {
                            $variation_name = VariantOptionContent::where([['variant_id', $_variant_options->variant_id], ['language_id', $language->id], ['index_key', $_variant_options->index_key]])->select('id', 'language_id', 'option_name')->first();

                            if ($variation_name !== null) {
                                $variantOptionContent = ProductVariantOptionContent::where([['product_variant_option_id', intval($variantOption->id)], ['language_id', $language->id]])->first();

                                if (is_null($variantOptionContent)) {
                                    $variantOptionContent = new ProductVariantOptionContent();
                                }
                                $variantOptionContent->product_variant_option_id = $variantOption->id;
                                $variantOptionContent->user_id = $user_id;
                                $variantOptionContent->item_id = $item_id;
                                $variantOptionContent->language_id = $language->id;
                                $variantOptionContent->option_name = $variation_name->id;
                                $variantOptionContent->save();
                            }
                        }
                    }
                }
            }
            Session::flash('success', __('Created successfully'));
        } else {
            Session::flash('success', __('Updated successfully'));
        }


        return 'success';
    }

    public function variationDelete($id)
    {
        //get_product_variation first
        $product_variation = ProductVariation::where('unique_id', $id)->first();
        if ($product_variation) {
            //get product varition contents
            $product_variation_contents = ProductVariationContent::where('product_variation_id', $id)->get();
            foreach ($product_variation_contents as $item) {
                $item->delete();
            }
            $product_variation->delete();

            //get product variation options
            $product_variation_options = ProductVariantOption::where('unique_id', $id)->get();
            foreach ($product_variation_options as $product_variation_option) {
                //get product_variant_option_contents
                $product_variant_option_contents = ProductVariantOptionContent::where('product_variant_option_id', $product_variation_option->id)->get();
                foreach ($product_variant_option_contents as $product_variant_option_content) {
                    $product_variant_option_content->delete();
                }
                $product_variation_option->delete();
            }

            $count = ProductVariation::where('item_id', $product_variation->item_id)->count();
            if ($count > 0) {
                return 'success';
            } else {
                return 'reload';
            }
        } else {
            return 'error';
        }
    }

    public function variationOptionDelete(Request $request)
    {
        $variant_option = ProductVariantOption::where('id', $request->id)->first();
        if ($variant_option) {
            $option_contents = ProductVariantOptionContent::where('product_variant_option_id', $variant_option->id)->get();
            foreach ($option_contents as $option_content) {
                $option_content->delete();
            }
            $variant_option->delete();
        }
        return 'success';
    }

    public function paymentStatus(Request $request)
    {
        $order = UserOrder::findOrFail($request->order_id);
        $user = User::where('id', $order->user_id)->firstOrFail();

        $dir = public_path('assets/front/invoices/');
        if ($request->payment_status == 'Completed') {
            @unlink($dir . $order->invoice_number);
            $invoice = Common::generateInvoice($order, $user);
        }
        $order->payment_status = $request->payment_status;
        $order->save();

        $be = DB::table('basic_extendeds')
            ->select('is_smtp', 'smtp_host', 'smtp_port', 'encryption', 'smtp_username', 'smtp_password', 'from_mail', 'from_name')
            ->first();;
        $sub = 'Payment Status Updated';

        $to = $order->billing_email;
        $fname = $order->billing_fname;

        if ($be->is_smtp == 1) {
            $mail_body    = 'Hello <strong>' . $fname . '</strong>,<br/>Your payment status is changed to ' . $request->payment_status . '.<br/>Thank you.';

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
            $data['recipient'] = $to;
            $data['subject'] = $sub;
            $data['body'] = $mail_body;
            if ($request->payment_status == 'Completed') {
                $data['invoice'] = $dir . $invoice;
            }
            BasicMailer::sendMail($data);
        }

        Session::flash('success', __('Updated Successfully'));
        return back();
    }

    public function settings()
    {
        $data['shopsettings'] = UserShopSetting::where('user_id', Auth::guard('web')->user()->id)->first();
        return view('user.item.settings', $data);
    }

    public function updateSettings(Request $request)
    {

        $shopsettings = UserShopSetting::where('user_id', Auth::guard('web')->user()->id)->first();
        if (!$shopsettings) {
            $shopsettings  = new UserShopSetting();
        }
        $shopsettings->user_id = Auth::guard('web')->user()->id;
        $shopsettings->item_rating_system = $request->item_rating_system;
        $shopsettings->disqus_comment_system = $request->disqus_comment_system;
        $shopsettings->catalog_mode = $request->catalog_mode;
        $shopsettings->time_format = $request->time_format;
        $shopsettings->tax = $request->tax ? $request->tax : 0.00;
        $shopsettings->save();

        Session::flash('success', __('Updated Successfully'));
        return back();
    }

    public function slider(Request $request)
    {
        $filename = null;
        $request->validate([
            'file' => 'mimes:jpg,jpeg,png|required',
        ]);
        if ($request->hasFile('file')) {
            $filename = Uploader::upload_picture(public_path('assets/front/img/user/items/slider-images'), $request->file('file'));
        }
        return response()->json(['status' => 'success', 'file_id' => $filename]);
    }

    public function sliderRemove(Request $request)
    {
        if (file_exists(public_path('assets/front/img/user/items/slider-images/') . $request->value)) {
            @unlink(public_path('assets/front/img/user/items/slider-images/') . $request->value);
            return response()->json(['status' => 200, 'message' => 'success']);
        } else {
            return response()->json(['status' => 404, 'message' => 'error']);
        }
    }

    public function dbSliderRemove(Request $request)
    {
        $img = UserItemImage::findOrFail($request->id);
        $imageCount = UserItemImage::where('item_id', $img->item_id)->count();

        if ($imageCount > 1) {
            @unlink(public_path('assets/front/img/user/items/slider-images/') . $img->image);
            $img->delete();
            return "success";
        } else {
            return response()->json(['status' => 200, 'message' => 'success']);
        }
    }

    public function subcatGetter(Request $request)
    {
        $data['subcategories'] = UserItemSubCategory::where('category_id', $request->category_id)->get();
        return $data;
    }

    public function setFlashSale($id, Request $request)
    {
        $rules = [
            'flash_amount' => $request->status == 1 ? 'required' : '',
            'start_date' => $request->status == 1 ? 'required' : '',
            'start_time' => $request->status == 1 ? 'required' : '',
            'end_date' => $request->status == 1 ? 'required' : '',
            'end_time' => $request->status == 1 ? 'required' : '',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }

        $item = UserItem::findOrFail($id);
        $item->flash_amount = $request->flash_amount;
        $item->start_date = $request->start_date;
        $item->start_time = $request->start_time;
        $item->end_date = $request->end_date;
        $item->end_time = $request->end_time;
        $item->flash = $request->status;
        $item->save();
        Session::flash('success', __('Updated Successfully'));
        return 'success';
    }
}
