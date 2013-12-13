<?php
define('ACC',true);
require('./include/init.php');
$act = isset($_POST['act']) ? $_POST['act'] : 'default';

if($act == 'default'){

    //如果没传ACT过来就退出，因为不知道想要做什么动作
    exit('未定义错误');

}if($act == 'del'){
    $info_id = isset($_POST['iid']) ? $_POST['iid'] + 0 : false ;

    //如果没传信息的ID过来就退出
    if(!$info_id){
        echo 2;
        exit;
    }

    $del_key = isset($_POST['del_key']) ? $_POST['del_key'] : false ;

    //如果没传删除密码过来就退出
    if(!$del_key){
        echo 3;
        exit;
    }

    $info = new infoModel();
    $pic_address_tmp = $info->get_info_img($info_id);

    if(!empty($pic_address_tmp)){
        $pic_address = explode('|', $pic_address_tmp);
        require_once(ROOT . 'libary/qiniu/rs.php');
        $qiniu = new qiniuModel();
        foreach ($pic_address as $spa) {
            $qiniu->del($spa);
        }
    }

    if($info->delete_info($info_id,$del_key) > 0 ){
        echo 1;
    }else{
        echo 0;
    }
}