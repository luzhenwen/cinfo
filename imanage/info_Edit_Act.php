<?php
define('ACC',true);
require('../include/init.php');
require_once(ROOT . 'include/logincheck.php');
if(!isset($_REQUEST['act']) || empty($_REQUEST['act'])){
    exit('ACT ERR');
}
$act = $_REQUEST['act'];
$info = new infoModel();
if($act == 'add'){

    $_POST['content'] = htmlspecialchars($_POST['content'],ENT_HTML401,'UTF-8');
    $_POST['title'] = htmlspecialchars($_POST['title'],ENT_HTML401,'UTF-8');
    $data = $_POST;
    $data = deep_urlencode($data);
    if(!$info->checkContact($data['contact'])){
        echo '手机号码必须是11位数字';
        exit;
    }

    if($tmp = imageTool::createContactImg($data['contact'])){
        $contact_img = str_replace(ROOT,'',$tmp);
        $data['contact_img'] = $contact_img;
    }

    $data['del_time'] = time() + $data['del_time'];
    $data = $info->_field($data);
    $data = $info->autoFill($data);
    $data['post_ip'] = $_SERVER['REMOTE_ADDR'];
    unset($data['info_id']);
    //print_r($data);exit;
    if($info->add($data)){
        echo '信息添加成功';
        exit;
    }else{
        echo '信息添加失败';
        exit;
    }
}else if($act == 'adminadd'){
    $data = $_POST;
    unset($data['act']);

    $phone_pattern = '/^(1|0)(\d{10})$/';
    if(!preg_match($phone_pattern, $data['contact'])){
        echo json_encode(array('status' => 0, 'text' => '联系号码输入错误'));
        exit;
    }

    if(mb_strlen($data['title'],'UTF-8') > 9){
        echo json_encode(array('status' => 0, 'text' => '标题大于9个字符'));
        exit;
    }

    if(!isset($data['cat_id']) || empty($data['cat_id']) || $data['cat_id'] == 0){
        echo json_encode(array('status' => 0, 'text' => '请选择栏目'));
        exit;
    }

    $data['del_time'] = time() + 604800;
    $data['add_time'] = time();
    $data['del_key'] = 'admin090@@';
    $data['post_user'] = '匿名';
    if($tmp = imageTool::createContactImg($data['contact'])){
        $contact_img = str_replace(ROOT,'',$tmp);
        $data['contact_img'] = $contact_img;
    }
    if($info->add($data)){
        echo json_encode(array('status' => 1, 'text' => '信息添加成功'));
        exit;
    }else{
        echo json_encode(array('status' => 0, 'text' => '插入数据库失败'));
    }

}
else if($act =='edit'){

    $info_id = $_POST['info_id'];
    unset($_POST['info_id']);
    $data = $info->_field($_POST);
    $del_time = explode('-',$data['del_time']);
    $data['del_time'] = mktime($del_time[3],$del_time[4],$del_time[5],$del_time[1],$del_time[2],$del_time[0]);
    $data['order_valid_time'] = $data['order_valid_time'] * 86400 + time();
    if($info->update($data,$info_id) >= 1){
        echo '修改成功';
    }else{
        echo '修改失败';
    }
}else if($act == 'del'){
    $info_id = $_GET['info_id'];

    //删除信息内的图片
    $info_img = $info->get_info_img($info_id);
    if(!empty($info_img)){
        $info_img = explode('|', $info_img);
        $qiniu = new qiniuModel();
        foreach ($info_img as $value) {
            $qiniu->del($value);
        }
    }

    if($info->trans($info_id) >= 1){
        echo '移入回收站成功';
    }else{
        echo '移入回收站失败';
    }
}