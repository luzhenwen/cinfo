<?php
define('ACC',true);
require('./include/init.php');
define('STYLE','index');

if(!isset($_GET['search_input']) || empty($_GET['search_input'])){
    header('Location:index.php');
    exit;
}
$info = new infoModel();

//获得搜索关键字
$_GET['search_input'] = htmlspecialchars($_GET['search_input']);
$key_word = $_GET['search_input'];

//获得当前页码
$currpage = isset($_GET['page']) ? $_GET['page'] + 0 : 1;

//设置每一页显示多少条信息
$perpage = 40;

//获得该关键字的总信息条数
$total = $info->search_count($key_word);

//实例一个页码对象
$page = new pageTool($total,$currpage,$perpage);

//获得页码导航
if($page->searchShow()){
   $page_nav = $page->searchShow();
}else{
    $page_nav = '';
}

//$pattern = '/('. $key_word . ')/';

$data = $info->search($key_word,($currpage-1)*$perpage,$perpage);

if(empty($data)){
    $empty_info = true;
}

$key_word = stripcslashes($key_word);

$title = $key_word . '_搜索信息 - ' . config::getIns()->site_name;

include(ROOT . 'view/front/templates/search.html');