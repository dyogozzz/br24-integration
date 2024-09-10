<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function handlePost(Request $request)
    {
        $domain = $request->input('DOMAIN');
        $protocol = $request->input('PROTOCOL');
        $lang = $request->input('LANG');
        $appSid = $request->input('APP_SID');

        $redirectUrl = "https://frontend-for-integration.vercel.app/?DOMAIN={$domain}&PROTOCOL={$protocol}&LANG={$lang}&APP_SID={$appSid}";

        return redirect()->to($redirectUrl, 307);
    }
}
