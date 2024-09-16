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

        foreach($contacts as $contact) {
            // return response($contact, 200);
            // $found = array_search($contact[0]->COMPANY_ID, array_column($companies, 'ID'));
            $found = array_search($contact[0]['COMPANY_ID'], array_column($companies, 'ID'));
            
            return response($found, 200);
        }

        return response($result, 200);
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

        $queryUrl = 'crm.company.contact.add';
        $queryData = http_build_query(array(
            'name' => $request->contact_name_1,
            'second_name' => $request->contact_second_name_1,
            'company_id'=> $company->id,
        ));
        
        $result = Br24Conn::connWH($queryData, $queryUrl, 0);
        $contact1 = json_decode($result, 1);
        $company->contacts = array($contact1);
        
        $queryData = http_build_query(array(
            'name' => $request->contact_name_2,
            'second_name' => $request->contact_second_name_2,
            'company_id'=> $company->id,
        ));

        $result = Br24Conn::connWH($queryData, $queryUrl, 0);
        $contact2 = json_decode($result, 1);
        $company->contacts[] = $contact2;

        response($company, 200);
    }
}
