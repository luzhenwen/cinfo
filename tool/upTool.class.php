<?php
defined('ACC') || exit('Access Deined!');
class upTool{
    protected $allowExt = 'csv';
    protected $error = null;
    protected $file = null;
    
    public function upcsv($name){
        
        if(!isset($_FILES[$name])){
            return false;
        }
        
        $this->getFile($name);
        
        $ext = $this->getExt();
        
        if(!$this->isAllowExt()){
            $this->error = '不允许的文件后缀';
            return false;
        }
        
        $dir = ROOT . 'data/csvupload/';
        
        if(!$this->mk_dir($dir)){
            $this->error = '目录创建失败';
            return false;
        }
        
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $newname = date('Ymd') . '_' . substr(str_shuffle($str),0,10) . '.' . $ext;
        
        if(move_uploaded_file($this->file['tmp_name'],$dir . $newname)){
            return $dir . $newname;
        }else{
            return false;
        }
    }
    
    protected function isAllowExt(){
        $ext = $this->getExt();
        $allow = explode(',',$this->allowExt);
        return in_array($ext,$allow);
    }
    
    protected function getExt(){
        $tmp = explode('.',$this->file['name']);
        return end($tmp);
    }
    
    protected function mk_dir($dir){
        if(is_dir($dir)){
            return $dir;
        }else if(is_file($dir)){
            return dirname($dir);
        }
        
        if(mkdir($dir,0777,true)){
            return $dir;
        }else{
            return false;
        }
    }
    
    protected function getFile($key){
        $this->file = $_FILES[$key];
    }
    
    public function getErr(){
        return $this->error;
    }
}