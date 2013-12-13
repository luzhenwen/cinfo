<?php
defined('ACC') || exit('Access Deined!');
define('DEBUG',false);

if(DEBUG === true){
    error_reporting(E_ALL);
}else{
    error_reporting(0);
}

define('ROOT',str_replace('\\','/',dirname(dirname(__FILE__))) . '/');

require(ROOT . 'include/lib_base.php');
date_default_timezone_set('Asia/Shanghai');
function __autoload($val){
    if(stripos($val,'model') !== false){
        require(ROOT . 'model/' . $val . '.class.php');
    }else if(stripos($val,'tool') !== false){
        require(ROOT . 'tool/' . $val . '.class.php');
    }else{
        require(ROOT . 'include/' . $val . '.class.php');
    }
}
session_start();
$_SESSION['code'] = isset($_SESSION['code']) ? $_SESSION['code'] : 'initcode' . mt_rand(100,999);

if(get_magic_quotes_gpc() === 0){
	$_GET = deep_addslashes($_GET);
	$_POST = deep_addslashes($_POST);
	$_COOKIE = deep_addslashes($_COOKIE);
	$_REQUEST = deep_addslashes($_REQUEST);
}