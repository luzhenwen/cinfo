<?php
defined('ACC') || exit('Access Deined!');

class imageTool{
    protected $font = '../mysh.ttf';

    static public function createContactImg($number,$save = false){
        if(!is_numeric($number)){
            return false;
        }
        
        if(!$save){
            $save = self::save_dir();
        }
        
        $arr = str_split($number);
        
        $im = imagecreatetruecolor(151,21);
        /*
        $bg_color = imagecolorallocate($im,198,64,56);
        $font_color = imagecolorallocate($im,240,224,177);
        */
        $bg_color = imagecolorallocate($im,241,240,239);
        $font_color = imagecolorallocate($im,198,64,56);
        imagefill($im,0,0,$bg_color);
        
        $offset = 5;
        $i = 0;
        foreach($arr as $v){
            imagettftext($im,18,0,$offset,19,$font_color,ROOT . 'msyh.ttf',$v);
            if($i == 2 || $i == 6){
                $offset += 18;
            }else{
                $offset += 12;
            }
            $i++;
        }
        
        imagepng($im,$save);
        
        imagedestroy($im);
        
        return $save;
    }
    
    static protected function save_dir(){
        $path = ROOT . 'data/image/contact/' . date('Ym/d');
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $name = substr(str_shuffle($str),0,10);
        if(is_dir($path)){
            return $path . '/' . $name . '.png';
        }else{
            mkdir($path,0777,true);
            return $path . '/' . $name . '.png';
        }
    }
}
