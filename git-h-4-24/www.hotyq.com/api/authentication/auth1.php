<?php
/*
 *@author Zjmainstay<http://zjmainstay.cn>
 *
 */
header("Content-type: text/html; charset=utf-8"); 
// 开启错误提示
 ini_set('display_errors','on');
 error_reporting(E_ALL);

$res    = '';
try {
    $client         = new SoapClient('NciicServices.wsdl');
    //$client         = new SoapClient('http://www.webservicex.net/globalweather.asmx?wsdl');
    $licenseCode    = file_get_contents('license.txt');
    $condition      = '<?xml version="1.0" encoding="UTF-8" ?>
<ROWS>
    <INFO>
    <SBM>20131230155100</SBM>
    </INFO>
    <ROW>
        <GMSFHM>公民身份号码</GMSFHM>
        <XM>姓名</XM>
    </ROW>
    <ROW FSD="100022" YWLX="身份证认证测试-错误" >
    <GMSFHM>4408********2231</GMSFHM>
    <XM>白雪</XM>
    </ROW>
    <ROW FSD="100022" YWLX="身份证认证测试-正确">
    <GMSFHM>4408********2232</GMSFHM>
    <XM>白雪</XM>
    </ROW>
    <ROW FSD="100022" YWLX="身份证认证测试-正确">
    <GMSFHM>4207********8554</GMSFHM>
    <XM>雷朋</XM>
    </ROW>
</ROWS>';
    $params = array(
        'inLicense'   => $licenseCode,
        'inConditions' => $condition,
    );
    $res    = $client->nciicCheck($params);
} catch(Exception $e) {
    echo $e->getMessage();
    exit;
}

echo $res->out;
