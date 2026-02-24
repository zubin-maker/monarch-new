<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Uploader;
use App\Models\User\Language;
use App\Models\User\StaticHeroSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class StaticHeroSectionController extends Controller
{
    public function index(Request $request)
    {
        $user_id = Auth::guard('web')->user()->id;
        $language = Language::where('code', $request->language)->where('user_id', $user_id)->first();
        $information['language'] = $language;
        $information['u_langs'] = Language::where('user_id', $user_id)->get();
        $information['data'] = StaticHeroSection::where('language_id', $language->id)
            ->where('user_id', $user_id)
            ->first();
        return view('user.home.hero_section.static_hero_section', $information);
    }

    public function update(Request $request)
    {
        $request->validate([
            'title' => 'nullable|max:255',
            'subtitle' => 'nullable|max:255',
            'btn_name' => 'nullable|max:255',
            'btn_url' => 'nullable|max:255',
        ]);
        $data = StaticHeroSection::where('user_id', Auth::guard('web')->user()->id)->where('language_id', $request->language_id)->first();
        if (empty($data)) {
            $data = new StaticHeroSection();
            $data->user_id = Auth::guard('web')->user()->id;
            $data->language_id = $request->language_id;
        }

        if ($request->hasFile('hero_section_background_image')) {
            $data->background_image = Uploader::update_picture(public_path('assets/front/img/hero-section'), $request->file('hero_section_background_image'), $data->background_image);
        }
        if ($request->hasFile('hero_image')) {
            $data->hero_image = Uploader::update_picture(public_path('assets/front/img/hero-section'), $request->file('hero_image'), $data->hero_image);
        }

        $data->title = $request->title;
        $data->subtitle = $request->subtitle;
        $data->button_text = $request->button_text;
        $data->button_url = $request->button_url;
        $data->save();

        Session::flash('success', __('Updated Successfully'));
        return 'success';
    }
}
