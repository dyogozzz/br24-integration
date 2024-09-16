<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
require_once ('../Conn/br24Conn.php'); 

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

    public function geter(Request $request) {

        $queryUrl = 'crm.contact.list';
        $queryData = http_build_query(array(
                
        'select' => [ "ID", "NAME", "LAST_NAME", "UF_CRM_1588503377", "PHONE", "EMAIL"]
        ));
        $result = BitrixConn::ConnWH($queryData, $queryUrl, 0) ;
        $result = json_decode($result, 1);

        return response('', 200)->json();
    }
}
