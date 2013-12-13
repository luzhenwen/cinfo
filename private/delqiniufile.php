<?php
define('ACC', true);
require('../include/init.php');

if(!isset($_POST['filename'])){
	exit('0');
}
if(!isset($_POST['unid']) || $_POST['unid'] !== $_SESSION['unid']){
	exit('-1');
}

$filename = $_POST['filename'];
$re = qiniuModel::del($filename);
print_r($re);