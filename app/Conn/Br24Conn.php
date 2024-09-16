<?php

namespace App\Conn;

class Br24Conn
{
  public static function ConnWH($queryData, $queryUrl, $ssl_verify) {
    $queryUrl1 = 'https://b24-p96ng2.bitrix24.com.br/rest/1/k8p27m2ggj28ql5b/'.$queryUrl;
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYPEER => $ssl_verify,
    CURLOPT_POST => 1,
    CURLOPT_HEADER => 0,
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $queryUrl1,
    CURLOPT_POSTFIELDS => $queryData,
    ));

    $result = curl_exec($curl);
    curl_close($curl);

    return $result;
  }

  // public function CompanyList() {
  //   $queryUrl = 'crm.company.list';
  //   $queryData = http_build_query(array(
  //       'filter' => [ "UF_CRM_1588503398" => $cnpj ],
  //       'select' => [ "UF_CRM_1588548671" ]
  //     ));
  //   $result = BitrixConn::ConnWH($queryData, $queryUrl,0) ;
  // }
  
  // public function ContactList() {
  //   $queryUrl = 'crm.contact.list';
  //   $queryData = http_build_query(array(
  //     'select' => [ "ID", "NAME", "LAST_NAME", "UF_CRM_1588503377", "PHONE", "EMAIL"]
  //     ));
  //   $result = BitrixConn::ConnWH($queryData, $queryUrl, 0);
  // }
}