<?php

define('ACC',true);
require('./include/init.php');
define('STYLE','release');

$title = '发布新的信息 ' . config::getIns()->site_name;
//如果没有POST数据和没有SESSION的unid就视为需要发布新的消息，并且在SESSION写入一个唯一验证码
if(!isset($_POST['unid']) || (!isset($_SESSION['unid']))){
    $upToken = qiniuModel::get_up_token();

    //设置一个隐藏的验证码
    $unid = md5(uniqid(mt_rand(10000,99999),true));
    $_SESSION['unid'] = $unid;
    $cat = new categoryModel();
    $cat_data = $cat->select();
    $cat_list = $cat->catTree($cat_data);
    include(ROOT . 'view/front/templates/release.html');
    exit;
}
//如果有POST过来唯一验证码但是和SESSION的唯一验证码不一样
else if($_POST['unid'] !== $_SESSION['unid']){

    $result = array('statu'=>0,'text'=>'信息不一致');
    echo json_encode($result);
    exit;
}

else{
    $data = $_POST;
    //$data = deep_urldecode($data);

    //把上传过后不用的图片都删除掉
    if(isset($data['uploaded_pic'])){
        $uploadedPic = explode('|', $data['uploaded_pic']);
        if(isset($data['pic_address'])){
            $picAddress = explode('|',$data['pic_address']);
        }else{
            $picAddress = array();
        }
        $beDelPic = array_diff($uploadedPic, $picAddress);
        $beDelPic = implode('|', $beDelPic);
        qiniuModel::del($beDelPic);
    }

    if($data['chkcode'] =='请输入验证码' || strtolower($data['chkcode']) !== strtolower($_SESSION['code'])){
        $result = array('statu'=>0,'text'=>'验证码错误');
        echo json_encode($result);
        exit;
    }

    $contact_pattern = '/^(1|0)(\d{10})$/i';

    if(!isset($data['cat_id']) || !is_numeric($data['cat_id'])){
        $result = array('statu'=>0,'text'=>'请选择栏目');
        echo json_encode($result);
        exit;
    }

    if(mb_strlen($data['title'],'utf-8') < 2){
        $result = array('statu'=>0,'text'=>'信息标题不得小于2个字');
        echo json_encode($result);
        exit;
    }

    if(mb_strlen($data['title'],'utf-8') > 9){
        $result = array('statu'=>0,'text'=>'信息标题不得大于9个字');
        echo json_encode($result);
        exit;
    }

    if(mb_strlen($data['content'],'utf-8') < 10){
        $result = array('statu'=>0,'text'=>'信息内容不得小于10个字');
        echo json_encode($result);
        exit;
    }

    if(mb_strlen($data['content'],'utf-8') > 800){
        $result = array('statu'=>0,'text'=>'信息内容不得大于800个字');
        echo json_encode($result);
        exit;
    }

    if(mb_strlen($data['del_key'],'utf-8') > 18){
        $result = array('statu'=>0,'text'=>'删除密码不得大于18个字');
        echo json_encode($result);
        exit;
    }

    if(!preg_match($contact_pattern,$data['contact'])){
        $result = array('statu'=>0,'text'=>'请填写11位联系电话，固话请加区号');
        echo json_encode($result);
        exit;
    }

    $filter = new filterModel();
    $filter->get_word();

    $info = new infoModel();
    $data = $info->autoFill($data);
    $data = $info->_field($data);

    if(!$filter->filter_word($data['title'])){
        $result = array('statu'=>0,'text'=>'标题含有非法字符');
        echo json_encode($result);
        exit;
    }

    if(!$filter->filter_word($data['content'])){
        $result = array('statu'=>0,'text'=>'内容含有非法字符');
        echo json_encode($result);
        exit;
    }

    $data['content'] = htmlspecialchars($data['content']);
    $data['title'] = htmlspecialchars($data['title']);
    $data['post_ip'] = $_SERVER['REMOTE_ADDR'];
    $data['del_time'] = time() + $data['del_time'];
    $data['contact_img'] = str_replace(ROOT,'',imageTool::createContactImg($data['contact']));

    if($info->add($data)){
        $result = array('statu'=>1,'text'=>'信息发布成功');
        echo json_encode($result);
        exit;
    }else{
        $result = array('statu'=>0,'text'=>'信息添加失败');
        echo json_encode($result);
        exit;
    }
}