<?php

define('ACC',true);
require('./include/init.php');
/*
echo '{"statu":1,"text":"eval(alert(good))","et" : "' . $_POST['etag'] . '"}';
*/

if(!isset($_POST['filedata']) || !isset($_FILES['file']['name'])){
	exit;
}

if(!isset($_SESSION['unid']) || $_SESSION['unid'] != $_POST['unid']){
	exit;
}
echo 555;
require_once('./libary/qiniu/io.php');
require_once('./libary/qiniu/rs.php');
$bucket = 'ifusui';
$accessKey = 'TQhTo389OIYS0IeZGKE4eYcoReM5ObP1uETVyfED';
$secretKey = 'QwihNsUGL4uvVLUM8kyAnLZ5OQegowVo9bIbfm9x';
Qiniu_SetKeys($accessKey, $secretKey);
$putPolicy = new Qiniu_RS_PutPolicy($bucket);
$upToken = $putPolicy->Token(null);
$putExtra = new Qiniu_PutExtra();
$putExtra->Crc32 = 1;

$fileId = $_POST['filedata'];
$tmp = explode('.',$_FILES['file']['name']);
$ext = end($tmp);

if($_FILES['file']['error'] !== 0){
	switch ($_FILES['file']['error']) {
		case 1:
			exit("
			<script>
				var oParent = parent.document.getElementById('filedata" . $fileId . "').parentNode;
				oParent.removeChild(oParent.lastChild);
				oParent.getElementsByTagName('span')[0].innerHTML = '上传图片过大，每张图片不得大于2M';
				oParent.getElementsByTagName('span')[0].style.display = 'inline';
			</script>
				");
			break;

		default:
			exit("
			<script>
				var oParent = parent.document.getElementById('filedata" . $fileId . "').parentNode;
				oParent.removeChild(oParent.lastChild);
				oParent.getElementsByTagName('span')[0].innerHTML = '图片上传过程出错';
				oParent.getElementsByTagName('span')[0].style.display = 'inline';
			</script>
				");
			break;
	}
}

//判断文件后缀，如果不符合，取消上传
if(!in_array(strtolower($ext),array('bmp','jpg'))){
	exit("
		<script>
			var oParent = parent.document.getElementById('filedata" . $fileId . "').parentNode;
			oParent.removeChild(oParent.lastChild);
			oParent.getElementsByTagName('span')[0].innerHTML = '图片格式错误';
			oParent.getElementsByTagName('span')[0].style.display = 'inline';
		</script>
		");
}


if($_FILES['file']['size'] > 2 * 1024 * 1024){
	exit("
		<script>
			var oParent = parent.document.getElementById('filedata" . $fileId . "').parentNode;
			oParent.removeChild(oParent.lastChild);
			oParent.getElementsByTagName('span')[0].innerHTML = '图片太大';
			oParent.getElementsByTagName('span')[0].style.display = 'inline';
		</script>
		");
}

$filename = time() . mt_rand(10000,99999) . '.jpg';

list($ret, $err) = Qiniu_PutFile($upToken, $filename, $_FILES['file']['tmp_name'], $putExtra);

if($err !== null){
    print_r($err);
    exit(0);
}
print_r($ret);
echo "
<script>
	parent.document.getElementById('filedata" . $fileId . "').setAttribute('picaddress','" . $filename . "');
	parent.document.getElementById('filedata" . $fileId . "').parentNode.getElementsByTagName('img')[0].src = './view/front/image/succ.png';
	parent.document.getElementById('release_submit').disabled = '';
</script>
";
