<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User\Language;
use App\Models\User\UserItemCategory;
use App\Models\User\UserItemSubCategory;
use App\Models\Variant;
use App\Models\VariantContent;
use App\Models\VariantOption;
use App\Models\VariantOptionContent;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Session;

class VariantController extends Controller
{
    public function index(Request $request)
    {
        $user_id = Auth::guard('web')->user()->id;
        $language = Language::where([['code', $request->language], ['user_id', $user_id]])->first();
        $data['languages'] = Language::where('user_id', $user_id)->get();
        $data['variants'] = VariantContent::where([['user_id', $user_id], ['language_id', $language->id]])->orderBy('created_at', 'DESC')->get();

        return view('user.item.variant.index', $data);
    }

    public function create()
    {
        return view('user.item.variant.create');
    }

    public function get_subcategory(Request $request)
    {
        $subcategories = UserItemSubCategory::where('category_id', $request->category_id)->get();
        return $subcategories;
    }

    public function store(Request $request)
    {
        $user_id = Auth::guard('web')->user()->id;
        $user_languages = Language::where('user_id', $user_id)->get();

        //validation logic here
        $validator =  $this->getValidation($user_languages, $request);
        if ($validator->fails()) {
            return Response::json([
                'errors' => $validator->getMessageBag()->toArray()
            ], 400);
        }
        if (is_null($request->option_names)) {
            session()->flash('warning', __('You need to add at least one option.'));
            return "success";
        }

        // Step 2: Save the Variant
        $variant = Variant::create(['user_id' => $user_id]);

        // Step 3: Save Variant Contents
        $d_category = UserItemCategory::where('id', $request->category_id)->first();
        $d_subcategory = UserItemSubCategory::where('id', $request->sub_category_id)->first();
        $category_unique_id = $d_category->unique_id;
        $subcategory_unique_id = $d_subcategory ? $d_subcategory->unique_id : null;

        foreach ($request->input('variant_names') as $languageCode => $variantNames) {
            //skip if no variant names are actually provided (empty or all null)
            if (empty(array_filter($variantNames, 'strlen'))) {
                continue;
            }
            $language = Language::where('code', $languageCode)->where('user_id', $user_id)->first();
            if (!$language) continue;

            // get localized category and subcateory ids
            $category = UserItemCategory::where('unique_id', $category_unique_id)
                ->where('language_id', $language->id)
                ->first();

            $subcategory = $subcategory_unique_id ?
                UserItemSubCategory::where('unique_id', $subcategory_unique_id)
                ->where('language_id', $language->id)
                ->first() : null;

            //save variant contents
            foreach ($variantNames as $index => $variantName) {
                if (empty($variantName)) continue; // skip empty names
                VariantContent::create([
                    'category_id' => $category->id ?? null,
                    'sub_category_id' => $subcategory->id ?? null,
                    'language_id' => $language->id,
                    'user_id' => $user_id,
                    'name' => $variantName,
                    'variant_id' => $variant->id
                ]);
            }

            // If options are more than 0
            if (isset($request->option_names[$languageCode]) && count($request->option_names[$languageCode]) > 0) {
                $variantOption = VariantOption::create([
                    'user_id' => $user_id,
                    'variant_id' => $variant->id
                ]);

                foreach ($request->option_names[$languageCode] as $key => $optionName) {
                    $options = is_array($optionName) ? $optionName : [$optionName];
                    foreach ($options as $singleOption) {
                        if (empty($singleOption)) continue; // skip empty options
                        $this->saveVariantOptionContent(
                            $variant->id,
                            $variantOption->id,
                            $user_id,
                            $languageCode,
                            $singleOption,
                            $key
                        );
                    }
                }
            }
        }

        Session::flash('success', __('Created successfully'));
        return "success";
    }

    public function edit($id)
    {
        $data['variant'] = Variant::where('id', $id)->firstOrFail();
        return view('user.item.variant.edit', $data);
    }

    // public function update(Request $request, $id)
    // {
    //     $user_id = Auth::guard('web')->user()->id;
    //     $user_languages = Language::where('user_id', $user_id)->get();

    //     //validation logic here
    //     $validator =  $this->getValidation($user_languages, $request);
    //     if ($validator->fails()) {
    //         return Response::json([
    //             'errors' => $validator->getMessageBag()->toArray()
    //         ], 400);
    //     }

    //     if (is_null($request->option_names)) {
    //         session()->flash('warning', 'You need to add at least one option.');
    //         return "success";
    //     }
    //     // Step 1: Update the Variant
    //     $variant = Variant::where('id', $id)->where('user_id', $user_id)->firstOrFail();

    //     $d_category = UserItemCategory::where('id', $request->category_id)->first();
    //     $d_subcategory = UserItemSubCategory::where('id', $request->sub_category_id)->first();
    //     $category_unique_id = $d_category->unique_id;
    //     $subcategory_unique_id = @$d_subcategory->unique_id;

    //     // Step 2: Update or Create Variant Contents
    //     foreach ($request->input('variant_names') as $languageCode => $variantNames) {
    //         // skip if no valid variant names for this language
    //         if (empty(array_filter($variantNames, 'strlen'))) {
    //             continue;
    //         }

    //         $language_id = Language::where([['code', $languageCode], ['user_id', $user_id]])->first()->id;
    //         $category = UserItemCategory::where([['unique_id', $category_unique_id], ['language_id', $language_id]])
    //             ->first();
    //         $subcategory = UserItemSubCategory::where([['unique_id', $subcategory_unique_id], ['language_id', $language_id]])->first() ?? NULL;

    //         //update or create variant contents
    //         foreach ($variantNames as $index => $variantName) {
    //             if (empty($variantName)) continue;

    //             VariantContent::updateOrCreate(
    //                 [
    //                     'variant_id' => $variant->id,
    //                     'language_id' => $language_id,
    //                     'user_id' => $user_id
    //                 ],
    //                 [
    //                     'category_id' => $category->id ?? null,
    //                     'sub_category_id' => $subcategory->id ?? null,
    //                     'name' => $variantName
    //                 ]
    //             );
    //         }
    //     }

    //     // Step 3: Update or Create Variant Options and Contents
    //     $variantOptions = $request->input('option_names');
    //     foreach ($variantOptions as $languageCode => $optionGroups) {
    //         foreach ($optionGroups as $key => $optionValues) {
    //             // Ensure $optionValues is an array
    //             $options = is_array($optionValues) ? $optionValues : [$optionValues];
    //             // Get or create the VariantOption model
    //             $variantOption = VariantOption::firstOrCreate([
    //                 'variant_id' => $variant->id,
    //                 'user_id' => $user_id,
    //             ]);

    //             foreach ($options as $optionName) {
    //                 if (empty($optionName)) continue;
    //                 $this->saveVariantOptionContent(
    //                     $variant->id,
    //                     $variantOption->id,
    //                     $user_id,
    //                     $languageCode,
    //                     $optionName,
    //                     $key
    //                 );
    //             }
    //         }
    //     }

    //     Session::flash('success', __('Updated Successfully'));
    //     return "success";
    // }

       public function update(Request $request, $id)
        {
    $user_id = Auth::guard('web')->user()->id;

    // Validation
    $rules = [];
    if ($request->hasFile('image')) {
        $rules['image'] = 'mimes:jpeg,png,svg,jpg';
    }
    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
        return response()->json($validator->errors());
    }

    if (empty($request->input('option_names.en'))) {
        return response()->json(['error' => 'You must add at least one option.']);
    }

    // Fetch variant
    $variant = Variant::where('id', $id)->where('user_id', $user_id)->firstOrFail();

    $category = UserItemCategory::findOrFail($request->category_id);
    // $subcategory = $request->sub_category_id ? UserItemSubCategory::find($request->sub_category_id) : null;

    $englishLang = \App\Models\User\Language::where([
        ['user_id', $user_id],
        ['id', 17],
    ])->firstOrFail();

    // Update Variant Content (English)
    $variantName = $request->input('variant_names.en.0');
    VariantContent::updateOrCreate(
        [
            'variant_id'  => $variant->id,
            'language_id' => $englishLang->id,
            'user_id'     => $user_id,
        ],
        [
            'category_id'     => $category->id,
            // 'sub_category_id' => $subcategory?->id,
            'name'            => $variantName,
        ]
    );

    // Update/Create Options (English)
    foreach ($request->input('option_names.en', []) as $key => $optionValues) {
        foreach ((array) $optionValues as $idx => $optionName) {
            if (empty(trim($optionName))) continue;

            $image = $request->file("images.en.{$key}.{$idx}");

            $this->saveVariantOptionContent(
                variant_id: $variant->id,
                userId: $user_id,
                languageCode: 'en',
                optionName: $optionName,
                key: $key,
                image: $image
            );
        }
    }

     Session::flash('success', __('Updated Successfully'));
    return "success";
}


    // private function saveVariantOptionContent($variant_id, $variantOptionId = null, $userId, $languageCode, $optionName, $key)
    // {
    //     $conditions = [
    //         'variant_id' => $variant_id,
    //         'language_id' => \App\Models\User\Language::where([['code', $languageCode], ['user_id', $userId]])->first()->id,
    //         'index_key' => $key,
    //         'user_id' => $userId
    //     ];
    //     // Add the variant_option_id only if it's not null
    //     if ($variantOptionId != null) {
    //         $conditions['variant_option_id'] = $variantOptionId;
    //     }
    //     $variantOptionContent = VariantOptionContent::firstOrNew($conditions);
    //     $variantOptionContent->option_name = $optionName;
    //     $variantOptionContent->save();
    // }

     private function saveVariantOptionContent($variant_id, $userId, $languageCode, $optionName, $key, $image = null,$variantOptionId= null)
      {
    $language = \App\Models\User\Language::where([
        ['code', $languageCode],
        ['user_id', $userId]
    ])->first();

    if (!$language) return;

    $conditions = [
        'variant_id'  => $variant_id,
        'language_id' => $language->id,
        'index_key'   => $key,
        'user_id'     => $userId,
    ];
 if ($variantOptionId != null) {
             $conditions['variant_option_id'] = $variantOptionId;
        }
    $content = VariantOptionContent::firstOrNew($conditions);
    $content->option_name = $optionName;

    if ($image instanceof \Illuminate\Http\UploadedFile) {
        $dir = public_path('assets/front/img/user/items/variant_options/');

        if ($content->image && file_exists(public_path($content->image))) {
            @unlink(public_path($content->image));
        }

        $name = \App\Http\Helpers\Uploader::upload_picture($dir, $image);
        $content->image = 'assets/front/img/user/items/variant_options/' . $name;
    }

    $content->save();
}
    public function delete($id)
    {
        $user_id = Auth::guard('web')->user()->id;
        $variant = Variant::where([['id', $id], ['user_id', $user_id]])->firstOrFail();

        //delete variant content
        $variant_contents = VariantContent::where('variant_id', $variant->id)->get();
        foreach ($variant_contents as $variant_content) {
            $variant_content->delete();
        }

        //delete variant option
        $variant_options = VariantOption::where([['variant_id', $id], ['user_id', $user_id]])->get();
        foreach ($variant_options as $variant_option) {
            $variant_option->delete();
        }
        //delete variant option contents
        $variation_option_contents = VariantOptionContent::where('variant_id', $id)->get();
        foreach ($variation_option_contents as $variation_option_content) {
            $variation_option_content->delete();
        }

        $variant->delete();
        Session::flash('success', __('Deleted successfully'));
        return back();
    }
    public function bulk_delete(Request $request)
    {
        $user_id = Auth::guard('web')->user()->id;
        $ids = $request->ids;

        foreach ($ids as $id) {
            $variant = Variant::where([['id', $id], ['user_id', $user_id]])->firstOrFail();

            //delete variant content
            $variant_contents = VariantContent::where('variant_id', $variant->id)->get();
            foreach ($variant_contents as $variant_content) {
                $variant_content->delete();
            }

            //delete variant option
            $variant_options = VariantOption::where([['variant_id', $id], ['user_id', $user_id]])->get();
            foreach ($variant_options as $variant_option) {
                $variant_option->delete();
            }
            //delete variant option contents
            $variation_option_contents = VariantOptionContent::where('variant_id', $id)->get();
            foreach ($variation_option_contents as $variation_option_content) {
                $variation_option_content->delete();
            }

            $variant->delete();
            Session::flash('success', __('Deleted successfully'));
        }
        return 'success';
    }

    public function delete_option(Request $request)
    {
        $options = VariantOptionContent::where('index_key', $request->index)->get();
        foreach ($options as $option) {
            $option->delete();
        }
        return 'success';
    }

    public function getValidation($user_languages, $request)
    {
        $rules = [
            'category_id' => 'required',
        ];
        foreach ($user_languages as $user_language) {
            $code = $user_language->code;
            $langName = ' ' . $user_language->name . ' ' . __('language');
            if ($user_language->is_default == 1) {
                $rules["variant_names.{$code}"] = 'required|array|min:1';
                $rules["variant_names.{$code}.*"] = 'required';
                $messages["variant_names.{$code}.*.required"] = __('The variant name is required for') . $langName;

                $rules["option_names.{$code}"] = 'required|array|min:1';
                $rules["option_names.{$code}.*.*"] = 'required';
                $messages["option_names.{$code}.*.*.required"] = __('The option name is required for') . $langName;
            } else {
                // Only require options if variants exist for this language
                if (
                    $request->has("variant_names.{$code}") &&
                    is_array($request->input("variant_names.{$code}")) &&
                    !empty(array_filter($request->input("variant_names.{$code}")))
                ) {
                    $rules["variant_names.{$code}"] = 'required|array|min:1';
                    $rules["variant_names.{$code}.*"] = 'required';
                    $messages["variant_names.{$code}.*.required"] = __('The variant name is required for') . $langName;

                    $rules["option_names.{$code}"] = 'required|array|min:1';
                    $rules["option_names.{$code}.*.*"] = 'required';
                    $messages["option_names.{$code}.*.*.required"] = __('The option name is required for') . $langName;
                }
                // if no variant names are provided, skip the option name storing
                if (!empty($request->input("option_names.{$code}.*.*"))) {
                    if (!empty(array_filter($request->input("option_names.{$code}.*.*")))) {
                        $rules["variant_names.{$code}.*"] = 'required';
                        $messages["variant_names.{$code}.*.required"] = __('The variant name is required for') . $langName;
                    }
                }
            }
        }
        $validator = Validator::make($request->all(), $rules, $messages);
        return $validator;
    }
}
