<?php
define('ACC',true);
require('../include/init.php');
require_once(ROOT . 'include/logincheck.php');


if(isset($_POST['act']) && $_POST['act'] == 'edit'){
	$filter = new filterModel();
	$data = $filter->_field($_POST);
	//print_r($data);exit;
	$filter_id = $data['filter_id'];
	unset($data['filter_id']);

	if($filter->update($data, $filter_id)){
		echo 1;
	}else{
		echo 0;
	}
}else if(isset($_POST['act']) && $_POST['act'] == 'del'){
	$filter = new filterModel();
	$data = $_POST;
	if($filter->del($data['filter_id'])){
		echo 1;
	}else{
		echo 0;
	}
}else if(isset($_POST['act']) && $_POST['act'] == 'add'){
	$filter = new filterModel();
	$data = $filter->_field($_POST);
	if($filter->add($data)){
		echo $filter->insert_id();
	}else{
		echo 0;
	}
}else{
	$filter = new filterModel();
	$data = $filter->select();
	//print_r($data);exit;
	include(ROOT . 'view/admin/templates/filter.html');
}