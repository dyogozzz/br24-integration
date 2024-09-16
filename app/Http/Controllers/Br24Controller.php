<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Conn\Br24Conn;

class Br24Controller extends Controller
{
    public function handlePost(Request $request)
    {
        $domain = $request->input('DOMAIN');
        $protocol = $request->input('PROTOCOL');
        $lang = $request->input('LANG');
        $appSid = $request->input('APP_SID');

        $redirectUrl = "https://frontend-for-integration.vercel.app/?DOMAIN={$domain}&PROTOCOL={$protocol}&LANG={$lang}&APP_SID={$appSid}";

        return response('', 302)->header('Location', $redirectUrl);
    }

    public function getCompanies(Request $request) {
        $queryUrl = 'crm.company.list';
        $queryData = http_build_query(array(      
            'select' => [ "*"]
        ));
        
        $result = Br24Conn::ConnWH($queryData, $queryUrl, 0) ;
        $result = json_decode($result, 1);

        return response($result, 200);
    }

    public function getContacts(Request $request) {
        $queryUrl = 'crm.contact.list';
        $queryData = http_build_query(array(
            'select' => [ "*"]
        ));
        
        $result = Br24Conn::ConnWH($queryData, $queryUrl, 0) ;
        $result = json_decode($result, 1);

        return response($result, 200);
    }
}
