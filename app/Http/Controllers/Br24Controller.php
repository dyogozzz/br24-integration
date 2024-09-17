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

        // if(!isset($companies['result']) && empty($companies['result'])) {
        //     $this->createFields();
        // }

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
            }
        }

        return response($companies, 200);
    }

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
            'fields' => array(
                'TITLE' => $request->company_name,
                'email' => $request->email,
            )
        ));

        $result = Br24Conn::connWH($queryData, $queryUrl, 0);
        $company = json_decode($result, 1);

        if(!isset($company['result'])) {
            return response()->json(['error' => 'Error during company creation'], 500);
        }
        
        $queryUrl = 'crm.contact.add';
        $queryData = http_build_query(array(
            'fields' => array(
                'NAME' => $request->contact_name_1,
                'SECOND_NAME' => $request->contact_second_name_1,
            )
        ));
        
        $result = Br24Conn::connWH($queryData, $queryUrl, 0);
        $contact1 = json_decode($result, 1);

        if(!isset($contact1['result'])) {
            return response()->json(['error' => $contact1], 500);
            // return response()->json(['error' => 'Error during first contact creation'], 500);
        } else {
            $queryUrl = 'crm.company.contact.add';
            $queryData = http_build_query(array(
                'ID' => $company['result'],
                'fields' => array(
                    'CONTACT_ID' => $contact1['result']
                )
            ));
            Br24Conn::connWH($queryData, $queryUrl, 0);
        }
        
        $queryData = http_build_query(array(
            'fields' => array(
                'NAME' => $request->contact_name_2,
                'SECOND_NAME' => $request->contact_second_name_2,
            )
        ));
        
        $result = Br24Conn::connWH($queryData, $queryUrl, 0);
        $contact2 = json_decode($result, 1);

        if(!isset($contact2['result'])) {
            return response()->json(['error' => 'Error during second contact creation'], 500);
        } else {
            $queryUrl = 'crm.company.contact.add';
            $queryData = http_build_query(array(
                'ID' => $company['result'],
                'fields' => array(
                    'CONTACT_ID' => $contact2['result']
                )
            ));
            Br24Conn::connWH($queryData, $queryUrl, 0);
        }

        return response($company, 200);
    }

    public function editCompany(Request $request, $id) {
        $queryUrl = 'crm.company.update';
        $queryData = http_build_query(array(
            'ID' => $id,
            'fields' => array(
                "TITLE" =>  $request->company_name,
                "email" => $request->email
            ),
        ));
    
        $result = Br24Conn::ConnWH($queryData, $queryUrl, 0);
        $company = json_decode($result, 1);
        
        if(isset($request->contact_1_id) && !empty($request->contact_1_id)) {
            $queryUrl = 'crm.contact.update';
            $queryData = http_build_query(array(
                'ID' => $request->contact_1_id,
                'fields' => array(
                    'NAME' => $request->contact_name_1,
                    'SECOND_NAME' => $request->contact_second_name_1,
                ),
            ));
        } else {
            $queryUrl = 'crm.company.contact.add';
            $queryData = http_build_query(array(
                'COMPANY_ID' => $id,
                'NAME' => $request->contact_name_1,
                'SECOND_NAME' => $request->contact_second_name_1,
            ));
        }

        $result = Br24Conn::connWH($queryData, $queryUrl, 0);
        $contact1 = json_decode($result, 1);
        
        if(isset($request->contact_2_id) && !empty($request->contact_2_id)) {
            $queryUrl = 'crm.contact.update';
            $queryData = http_build_query(array(
                'ID' => $request->contact_2_id,
                'fields' => array(
                    'NAME' => $request->contact_name_2,
                    'SECOND_NAME' => $request->contact_second_name_2,
                ),
            ));
        } else {
            $queryUrl = 'crm.company.contact.add';
            $queryData = http_build_query(array(
                'COMPANY_ID' => $id,
                'NAME' => $request->contact_name_2,
                'SECOND_NAME' => $request->contact_second_name_2,
            ));
        }
        
        $result = Br24Conn::connWH($queryData, $queryUrl, 0);
        $contact2 = json_decode($result, 1);

        return response($company, 200);
    }

    public function createFields(){
        $queryUrl = 'crm.company.userfield.add';
        $queryData = http_build_query(array(
            'fields' => array(
                "FIELD_NAME" => "email",
                "EDIT_FORM_LABEL" => "email",
                "LIST_COLUMN_LABEL" => "email",
                "USER_TYPE_ID" => "string",
                "XML_ID" => "email"
            )));

        $result = Br24Conn::ConnWH($queryData, $queryUrl, 0) ;
        $result = json_decode($result, 1);
    }
}
