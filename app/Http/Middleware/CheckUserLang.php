<?php

namespace App\Http\Middleware;

use App\Models\Language;
use App\Models\User\Language as UserLanguage;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class CheckUserLang
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $username = !is_null(Auth::guard('web')->user()) ? Auth::guard('web')->user()->username : getUser()->username;

        if (session()->has('user_lang_' . $username)) {
            if (!is_null(getUserNullCheck())) {
                //add user website lang
                app()->setLocale(session()->get('user_lang_' . $username));
            } else {

                //add user dashboard lang
                if (Cookie::has('userDashboardLang')) {
                    $isLang = UserLanguage::where([['code', Cookie::get('userDashboardLang')], ['user_id', Auth::guard('web')->user()->username]])->exists();

                    if ($isLang == true) {
                        $userDashboardLang = Language::where('code', Cookie::get('userDashboardLang'))->first();
                    } else {
                        $userDashboardLang = UserLanguage::where([['user_id', Auth::guard('web')->user()->id]])->first();
                        Cookie::queue('userDashboardLang', $userDashboardLang->code, 60 * 24 * 30);
                    }
                } else {
                    $userDashboardLang = Language::where('is_default', 1)->first();
                    Cookie::queue('userDashboardLang', $userDashboardLang->code, 60 * 24 * 30);
                }

                app()->setLocale('user_' . $userDashboardLang->code);
            }
        } else {

            $default = Language::where('is_default', 1)->first();
            if (!empty($default)) {
                app()->setLocale('user_' . $default->code);
            }
        }

        return $next($request);
    }
}
