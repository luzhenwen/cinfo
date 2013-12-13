<?php
define('ACC',true);
require('../include/init.php');
require_once(ROOT . 'include/logincheck.php');
$cat = new categoryModel();

$data = $_POST;
$act = isset($data['act']) ? $data['act'] : '';

if($act == 'add'){
    $data = $cat->_field($data);
    unset($data['cat_id']);
    if($cat->add($data)){
        echo '添加成功';
    }else{
        echo '添加失败';
    }
}else if($act == 'edit'){
    $dst_catParent = $cat->getParents($data['parent_id']);
    foreach($dst_catParent as $v){
        if($v['cat_id'] == $data['cat_id']){
            echo '不能添加在子栏目里';
            exit;
        }
    }
    
    $data = $cat->_field($data);
    
    $cat_id = $data['cat_id'];
    
    unset($data['cat_id']);
    
    if($cat->update($data,$cat_id) >= 1){
        echo '修改栏目成功';
    }else{
        echo '修改栏目失败';
    }
}else if($_REQUEST['act'] == 'del'){

    $cat_id = $_GET['cat_id'];
    $info = $cat->getInfo($cat_id);
    $cat_list = $cat->select();
    $cat_tree = $cat->catTree($cat_list,$cat_id);
    
    if(!empty($cat_tree) || !empty($info)){
        echo '该栏目下还有子栏目或者信息，不能删除';
        exit;
    }
    
    if($cat->del($cat_id) >= 1){
        echo '删除栏目成功';
        exit;
    }
}