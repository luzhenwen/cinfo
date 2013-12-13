<?php
define('ACC',true);
require('../include/init.php');
require_once(ROOT . 'include/logincheck.php');
$act = $_REQUEST['act'];

if($act == 'add'){
    $cat = new categoryModel();
    $cat_list = $cat->select();
    $cat_list = $cat->catTree($cat_list);
    include(ROOT . 'view/admin/templates/goodsadd.html');
}else if($act == 'adminadd'){
    $cat = new categoryModel();
    $cat_list = $cat->select();
    include(ROOT . 'view/admin/templates/adminadd.html');
}else if($act == 'edit'){
    $cat = new categoryModel();
    $cat_list = $cat->select();
    $cat_list = $cat->catTree($cat_list);


    $info_id = $_GET['info_id'] + 0;
    $info = new infoModel();
    $info_row = $info->getRow($info_id);
    include(ROOT . 'view/admin/templates/goodsadd.html');
}