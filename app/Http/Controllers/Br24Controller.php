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
        
        $result = Br24Conn::ConnWH($queryData, $queryUrl, 0);
        $companies = json_decode($result, 1);

        $queryUrl = 'crm.contact.list';
        $queryData = http_build_query(array(
            'select' => [ "*"]
        ));
        
        $result = Br24Conn::ConnWH($queryData, $queryUrl, 0);
        $contacts = json_decode($result, 1);

        foreach($contacts['result'] as $contact) {
            $found = array_search((string) $contact['COMPANY_ID'], array_column($companies['result'], 'ID'));
            if($found !== false) {
                if (!isset($companies['result'][$found]['contacts']) || !is_array($companies['result'][$found]['contacts'])) {
                    $companies['result'][$found]['contacts'] = [];
                }
                
                $companies['result'][$found]['contacts'][] = $contact;
            } else {

            }
        }

        return response($companies, 200);
    }



    // public function getCompanies(Request $request) {
    //     $queryUrl = 'crm.company.list';
    //     $queryData = http_build_query(array(      
    //         'select' => [ "*"]
    //     ));
        
    //     $result = Br24Conn::ConnWH($queryData, $queryUrl, 0);
    //     $result = json_decode($result, 1);

    //     return response($result, 200);
    // }

    public function getContacts(Request $request) {
        $queryUrl = 'crm.contact.list';
        $queryData = http_build_query(array(
            'select' => [ "*"]
        ));
        
        $result = Br24Conn::ConnWH($queryData, $queryUrl, 0);
        $result = json_decode($result, 1);

        return response($result, 200);
    }

    public function createCompany(Request $request) {
        $queryUrl = 'crm.company.add';
        $queryData = http_build_query(array(
            'company_name' => $request->company_name,
        ));

        $result = Br24Conn::connWH($queryData, $queryUrl, 0);
        $company = json_decode($result, 1);

        if(!isset($company['ID'])) {
            return response()->json(['error' => 'Error during company creation'], 500);
        }
        
        $queryUrl = 'crm.company.contact.add';
        $queryData = http_build_query(array(
            'name' => $request->contact_name_1,
            'second_name' => $request->contact_second_name_1,
            'company_id'=> $company['ID'],
        ));
        
        $result = Br24Conn::connWH($queryData, $queryUrl, 0);
        $contact1 = json_decode($result, 1);

        if(!isset($contact1['ID'])) {
            return response()->json(['error' => 'Error during first contact creation'], 500);
        }
        
        $queryData = http_build_query(array(
            'name' => $request->contact_name_2,
            'second_name' => $request->contact_second_name_2,
            'company_id'=> $company['ID'],
        ));
        
        $result = Br24Conn::connWH($queryData, $queryUrl, 0);
        $contact2 = json_decode($result, 1);

        if(!isset($contact2['ID'])) {
            return response()->json(['error' => 'Error during second contact creation'], 500);
        }

        response($company, 200);
    }

    public function editCompany(Request $request, $id) {
        $queryUrl = 'crm.company.get';
        $queryData = http_build_query(array(
            'ID' => $id,
        ));

        $result = Br24Conn::connWH($queryData, $queryUrl, 0);
        $company = json_decode($result, 1);

        if(!isset($company['ID'])) {
            return response()->json(['error' => 'Error during company select'], 500);
        }
        
        // $queryUrl = 'crm.company.contact.add';
        // $queryData = http_build_query(array(
        //     'name' => $request->contact_name_1,
        //     'second_name' => $request->contact_second_name_1,
        //     'company_id'=> $company['ID'],
        // ));
        
        // $result = Br24Conn::connWH($queryData, $queryUrl, 0);
        // $contact1 = json_decode($result, 1);

        // if(!isset($contact1['ID'])) {
        //     return response()->json(['error' => 'Error during first contact creation'], 500);
        // }
        
        // $queryData = http_build_query(array(
        //     'name' => $request->contact_name_2,
        //     'second_name' => $request->contact_second_name_2,
        //     'company_id'=> $company['ID'],
        // ));
        
        // $result = Br24Conn::connWH($queryData, $queryUrl, 0);
        // $contact2 = json_decode($result, 1);

        // if(!isset($contact2['ID'])) {
        //     return response()->json(['error' => 'Error during second contact creation'], 500);
        // }

        response($company, 200);
    }
}
