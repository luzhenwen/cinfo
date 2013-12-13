<?php
define('ACC',true);
require('../include/init.php');
require_once(ROOT . 'include/logincheck.php');
$info = new infoModel();
$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : '';

if($act == 'trash'){
    if(isset($_GET['admin_search'])){
        $data = $_GET;

        $cat_id = $data['cat_id'] + 0;

        $type = isset($data['type']) ? $data['type'] : '';

        $key_word = isset($data['keyword']) ? $data['keyword'] : '';

        $total = $info->count_trans_info($cat_id);

        $currpage = isset($_GET['page']) ? $_GET['page'] : 1;

        $perpage = 60;

        $info_list = $info->search_trans_info(($currpage-1)*$perpage,$perpage,$cat_id,$type,$key_word);

        $page = new pageTool($total,$currpage,$perpage);

        $nav = $page->admin_show();

        $cat = new categoryModel();

        $cat_list = $cat->select();

        $cat_list = $cat->catTree($cat_list);
        include(ROOT . 'view/admin/templates/infolist.html');
        exit;
    }else{
        $total = $info->count_trans_info();
        $perpage = 60;
        $currpage = isset($_GET['page']) ? $_GET['page'] : 1;
        $info_list = $info->select_trash_info(($currpage-1)*$perpage, $perpage);
        $page = new pageTool($total, $currpage,$perpage);
        $nav = $page->admin_show();

        $cat = new categoryModel();
        $cat_list = $cat->select();
        $cat_list = $cat->catTree($cat_list);
        include(ROOT . 'view/admin/templates/infolist.html');
        exit;
    }
}else{
    if(isset($_GET['admin_search'])){


        $data = $_GET;

        $cat_id = $data['cat_id'] + 0;

        $type = isset($data['type']) ? $data['type'] : '';

        $key_word = isset($data['keyword']) ? $data['keyword'] : '';

        $total = $info->count_display_info($cat_id);

        $currpage = isset($_GET['page']) ? $_GET['page'] : 1;

        $perpage = 20;

        $info_list = $info->search_info(($currpage-1)*$perpage,$perpage,$cat_id,$type,$key_word);

        $page = new pageTool($total,$currpage);

        $nav = $page->admin_show();

        $cat = new categoryModel();

        $cat_list = $cat->select();

        $cat_list = $cat->catTree($cat_list);
        include(ROOT . 'view/admin/templates/infolist.html');
        exit;
    }else{
        $total = $info->count_display_info();
        $currpage = isset($_GET['page']) ? $_GET['page'] : 1;
        $perpage = 20;
        $info_list = $info->select_info(($currpage-1)*$perpage,$perpage);
        $page = new pageTool($total,$currpage);
        $nav = $page->admin_show();

        $cat = new categoryModel();

        $cat_list = $cat->select();

        $cat_list = $cat->catTree($cat_list);
        include(ROOT . 'view/admin/templates/infolist.html');
        exit;
    }
}