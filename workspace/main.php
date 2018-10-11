<?php

// クライゼルにアクセスできるipの一覧
// 54.178.130.65
// 13.112.146.151
// 54.95.173.208
// 13.113.2.186
// 54.65.95.211
// 13.230.195.240
// 13.114.53.25
// 54.178.218.193
// 52.198.205.242
// 52.194.74.165
// 18.179.166.74

require_once __DIR__.'/vendor/autoload.php';
require_once "HTTP/Request2.php";

define("KR_RPC_SOAP_URI", 'https://krs.bz/rpc');
define("KR_LOCATION", 'https://krs.bz/rhd-itm/rpc');
define("KR_DBID", 276);
define("ALL", 1);

//===============
define("KR_USER",     getenv('KR_USER'));
define("KR_PASSWORD", getenv('KR_PASSWORD'));
//===============

$soap = new SoapClient(null,array(
    "soap_version" => SOAP_1_2,
    "location"     => KR_LOCATION,
    "uri"          => KR_RPC_SOAP_URI,
    "trace"        => true
)
);

$soap->__setCookie("Cookie-Check", "1");

try{
    $soap->loginSession(KR_USER, KR_PASSWORD);
    echo __LINE__;
    // ダウンロードファイル作成の一括処理を開始
    $batch_job_id = $soap->exportMembers(KR_DBID, ALL, ALL, TRUE);
    // 一括処理の進捗
    $progress = 0;
    while ($progress < 100) {
        $status = $soap->getBatchJobStatus($batch_job_id); $operation = $status["operation"];
        $progress = $status["progress"];
        echo sprintf("%s¥t 進捗 : %2d%¥n", $operation, $progress);
    }
    echo "ダウンロードファイル作成終了¥n";
    // ファイルダウンロード用 URL を取得
    $download_url = $soap->getBatchJobResultUrl();
    // ファイルの取得(HTTP_Request)
    $request = new HTTP_Request2( $download_url ); $request->sendRequest();
    $response_code = $request->getResponseCode(); if ($responce_code == 200) {
        file_put_contents("download_file.csv", $request->getResponseBody());
        echo "ファイルをダウンロードしました。¥n ファイル名 : download_file.csv";
    } else {
        echo "ダウンロードに失敗しました。";
    }
} catch (Exception $e) { // SOAP 例外が発生した場合
    // echo $soap->__getLastResponse;
    var_dump($soap->__getLastRequest());
    var_dump($soap->__getLastRequestHeaders());
    var_dump($soap->__getLastResponse());
    var_dump($soap->__getLastResponseHeaders());
    echo $e->faultstring;
}
