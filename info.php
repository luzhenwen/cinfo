<?php
define('ACC',true);
require('include/init.php');
define('STYLE','content');

$info_id = isset($_GET['id']) ? $_GET['id'] + 0 : exit('未定义错误');

$info = new infoModel();

$info_row = $info->info_row($info_id);

if(empty($info_row)){
    echo '找不到该信息';
    exit;
}

$info->update_click($info_id);
if(!empty($info_row['pic_address'])){
	$pic_address = explode('|', $info_row['pic_address']);
	require_once("./libary/qiniu/rs.php");
	$domain = 'ifusui.qiniudn.com';
	$accessKey = 'TQhTo389OIYS0IeZGKE4eYcoReM5ObP1uETVyfED';
	$secretKey = 'QwihNsUGL4uvVLUM8kyAnLZ5OQegowVo9bIbfm9x';
	Qiniu_SetKeys($accessKey, $secretKey);
	$getPolicy = new Qiniu_RS_GetPolicy();
}



$title = $info_row['title'] . '_' . config::getIns()->site_name;

include(ROOT . 'view/front/templates/content.html');
