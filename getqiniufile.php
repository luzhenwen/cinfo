<?php
if(!isset($_POST['fileName'])){
	echo 'Access Deined!';
	exit;
}

require_once('./libary/qiniu/rs.php');
$key = $_POST['fileName'];
$domain = 'ifusui.qiniudn.com';
$accessKey = 'TQhTo389OIYS0IeZGKE4eYcoReM5ObP1uETVyfED';
$secretKey = 'QwihNsUGL4uvVLUM8kyAnLZ5OQegowVo9bIbfm9x';

Qiniu_SetKeys($accessKey, $secretKey);
$baseUrl = Qiniu_RS_MakeBaseUrl($domain, $key);
$getPolicy = new Qiniu_RS_GetPolicy();
$privateUrl = $getPolicy->MakeRequest($baseUrl, null);

echo json_encode(array('statu'=>1,'keyurl'=> $privateUrl));

//echo '{statu:1,url:' . $privateUrl . '}';