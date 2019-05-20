<?php
header("Content-Type: image/gif");
header ("Pragma-directive: no-cache");
header ("Cache-directive: no-cache");
header ("Cache-control: no-cache");
header ("Pragma: no-cache");
header ("Expires: 0");

session_start();

if(!isset($_SESSION["sid"]))
	$_SESSION["sid"] = gen_uuid();
	


include_once("admin/config.php");

$SESSID = $_SESSION["sid"];
$pagePath = isset($_REQUEST["p"]) ? urlencode($_REQUEST["p"]) : "";
$uIP = getUserIpAddr();
$uAgent = urlencode($_SERVER['HTTP_USER_AGENT']);

$GAStr = "https://www.google-analytics.com/collect?";
$GAStr .= "v=1&"; // version
$GAStr .= "tid=".$_CONFIG['googleAnalytics']."&"; //
$GAStr .= "cid=".$SESSID."&"; //
$GAStr .= "t=pageview&"; //
$GAStr .= "dp=". $pagePath ."&"; //
$GAStr .= "uip=". $uIP ."&"; //
$GAStr .= "ua=". $uAgent ."&"; //

echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
flush();
$ignore = file_get_contents($GAStr);
exit(0);

function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

function getUserIpAddr(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

?>