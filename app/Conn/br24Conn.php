<?php

namespace App\Conn;

class Br24Conn
{
  public function writeToLog($data, $title = '') {
      $log = "\n------------------------\n";
      $log .= date("Y.m.d G:i:s") . "\n";
      $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
      $log .= print_r($data, 1);
      $log .= "\n------------------------\n";
      file_put_contents(getcwd() . '/hook.log', $log, FILE_APPEND);
      return true;
  }

  public function ConnWH($queryData, $queryUrl, $ssl_verify) {
    $queryUrl1 = 'https://b24-p96ng2.bitrix24.com.br/rest/1/joaysx7awoin2yh2/'.$queryUrl;
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

  public function CompanyList() {
    $queryUrl = 'crm.company.list';
    $queryData = http_build_query(array(
            'filter' => [ "UF_CRM_1588503398" => $cnpj ],
            'select' => [ "UF_CRM_1588548671" ]
        ));
    $result = BitrixConn::ConnWH($queryData, $queryUrl, 0) ;
  }
}