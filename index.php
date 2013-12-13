<?php
define('ACC',true);
require('./include/init.php');

define('STYLE','index');

$cat_id = isset($_GET['cat_id']) ? $_GET['cat_id'] + 0 : '';
$currpage = isset($_GET['page']) ? $_GET['page'] + 0 : 1;

$info = new infoModel();
$cat = new categoryModel();

if(is_numeric($cat_id) && !$cat->is_cat($cat_id)){
	exit('参数有误');
}

//设置每页显示多少条
$perpage = 40;

if($cat_id == 0){
    $cat_id = '';
}else if($cat_id < 1){
    $cat_id = '';
}

$total = $info->count_display_info($cat_id);

/*
这段代码不知何用，先屏蔽掉
if($total == 0){
	$cat_id = '';
}
*/
//计算出总页数
$cnt = ceil($total/$perpage);

if($currpage == 0){
    $currpage = 1;
}if($currpage > $cnt || $currpage < 1){
    $currpage = 1;
}

$data = $info->select_index(($currpage-1)*$perpage,$perpage,$cat_id);

$page = new pageTool($total,$currpage,$perpage);

if($total > $perpage){
	$page_list = $page->show();
}else{
	$page_list = '';
}

//$site_config = config::getIns();

if($cat_name = $cat->getCategoryName($cat_id)){
	$title = $cat_name . '_' . config::getIns()->site_name;
}else{
	$title = config::getIns()->site_name;
}
include(ROOT. 'view/front/templates/index.html');
