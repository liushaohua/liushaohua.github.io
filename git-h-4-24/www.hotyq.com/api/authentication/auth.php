<?php
/*
 *@author Zjmainstay<http://zjmainstay.cn>
 *
 */

header("Content-type: text/html; charset=utf-8"); 
// 开启错误提示
// ini_set('display_errors','on');
// error_reporting(E_ALL);
//$client         = new SoapClient();
$res    = '';
try {
    $client         = new SoapClient('NciicServices.wsdl');
    $licenseCode    = file_get_contents('license.txt');
    $condition      = '<?xml version="1.0" encoding="UTF-8" ?>
<ROWS>  
    <INFO>
    <SBM>hyyqhyyq53289</SBM>
    </INFO>
    <ROW>
        <GMSFHM>公民身份号码</GMSFHM>
        <XM>姓名</XM>
    </ROW>
    <ROW FSD="100022" YWLX="身份证认证" >
    <GMSFHM>130203198206110018</GMSFHM>
    <XM>王屹</XM>
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

$dom = new DOMDocument();
$dom->loadXML($res->out);
print_r(getArray($dom->documentElement));

function getArray($node) {
  $array = false;

  if ($node->hasAttributes()) {
    foreach ($node->attributes as $attr) {
      $array[$attr->nodeName] = $attr->nodeValue;
    }
  }

  if ($node->hasChildNodes()) {
    if ($node->childNodes->length == 1) {
      $array[$node->firstChild->nodeName] = getArray($node->firstChild);
    } else {
      foreach ($node->childNodes as $childNode) {
      if ($childNode->nodeType != XML_TEXT_NODE) {
        $array[$childNode->nodeName][] = getArray($childNode);
      }
    }
  }
  } else {
    return $node->nodeValue;
  }
  return $array;
}
echo $res->out;

