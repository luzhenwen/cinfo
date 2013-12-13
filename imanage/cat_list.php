<?php
define('ACC',true);
require('../include/init.php');
require_once(ROOT . 'include/logincheck.php');

$cat = new categoryModel();


$cat_list = $cat->select();
$cat_list = $cat->catTree($cat_list);
include(ROOT . 'view/admin/templates/catlist.html');
