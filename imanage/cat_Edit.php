<?php
//header('Content-type:text/html;charset=utf-8;');
define('ACC',true);
require('../include/init.php');
require_once(ROOT . 'include/logincheck.php');

if(isset($_GET['act'])){
    $act = $_GET['act'];
}else{
    $act = '';
}

$cat = new categoryModel();

if($act == 'add'){
    $cat_list = $cat->select();
    $cat_list = $cat->catTree($cat_list);
    include(ROOT . 'view/admin/templates/catadd.html');
}else if($act == 'edit'){
    $cat_list = $cat->select();
    $cat_list = $cat->catTree($cat_list);
    $cat_id = $_GET['cat_id'];
    $cat_row = $cat->getRow($cat_id);
    include(ROOT . 'view/admin/templates/catadd.html');
}