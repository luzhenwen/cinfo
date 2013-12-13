<?php
session_start();
header('Content-Type:image/png;');
$im = imagecreatetruecolor(150,50);
$dst_im = imagecreatetruecolor(150,50);
$str = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789';

//设置背景颜色
$bg_color = imagecolorallocate($im,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));

imagefill($im,0,0,$bg_color);
imagefill($dst_im,0,0,$bg_color);

//验证码的颜色
$str_color = imagecolorallocate($im,mt_rand(0,100),mt_rand(0,100),mt_rand(0,100));

//验证码初始的位置
$str_start_x = mt_rand(0,65);
$str_start_y = mt_rand(22,45);
$font = array('./msyh.ttf','./msyhbd.ttf');

//把验证码写入图片和SESSION
$_SESSION['code'] = '';
for($i = 0 ; $i < 4 ; $i++){
    $code_str = substr(str_shuffle($str),0,1);
    $_SESSION['code'] .= $code_str;
    $font_num = mt_rand(0,1);
    imagettftext($im,mt_rand(18,22),0,$str_start_x+$i*22,$str_start_y,$str_color,$font[$font_num],$code_str);
}

//用正弦曲线复制图片，好让文字扭起来
for($c = 0 ; $c < 50 ; $c++){
    $posX = round(sin($c * 2 * 2 * M_PI / 50) * 1);
    imagecopy($dst_im,$im,$posX,$c,0,$c,150,1);
}


//圆圈的颜色
$ellipse_color = imagecolorallocate($im,198,64,56);

//圆圈的初始位置
$ellipse_start_x = mt_rand(20,130);
$ellipse_start_y = mt_rand(10,40);

//添加圆圈
for($j = 0 ; $j < 15 ; $j++){
    imageellipse($dst_im,$ellipse_start_x,$ellipse_start_y,$j*15,$j*15,$ellipse_color);
}

imagepng($dst_im);