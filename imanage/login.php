<?php
define('ACC',true);
require('../include/init.php');
if(isset($_POST['check']) && $_POST['check'] == 'managelogin'){
    $data = $_POST;
    if($data['username'] === 'luzhenwen' && $data['passwd'] === '1991SMASHERlzw@@'){
        $_SESSION['lev'] = 'manage';
        echo '登陆成功';
    }else{
        echo '账号密码错误';
    }
}else if(isset($_SESSION['lev']) && $_SESSION['lev'] === 'manage'){
    echo '你已经登陆';
}else{
    if(!isset($_SESSION['lev']) || $_SESSION['lev'] !== 'manage'){
        include(ROOT . 'view/admin/templates/login.html');
        exit;
    }
}