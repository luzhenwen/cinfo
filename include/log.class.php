<?php
defined('ACC') || exit('Access Deined!');
class log{
    const CURR = 'curr.log';
    const ERRCURR = 'errcurr.log';

    static public function write($content){
        $act = 'normal';
        $path = self::isBak($act);
        $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        $remote_addr = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $content = '[' . date('y-m-d H:i:s'). ']' . '[' . $remote_addr . ']' . '[' . $request_uri . ']' . $content . "\r\n";
        $fh = fopen($path,'ab');
        fwrite($fh,$content);
        fclose($fh);
    }

    static public function errWrite($content){
        $act = 'err';
        $path = self::isBak($act);
        $content = '[' . date('y-m-d H:i:s'). ']' . '[' . $_SERVER['REMOTE_ADDR'] . ']' . '[' . $_SERVER['REQUEST_URI'] . ']' . $content . "\r\n";
        $fh = fopen($path,'ab');
        fwrite($fh,$content);
        fclose($fh);
    }

    static protected function isBak($act){
        $path = $act == 'normal' ? ROOT . 'data/log/' . self::CURR : ROOT . 'data/log/' . self::ERRCURR ;

        //clearstatcache(true,$path);
        if(!file_exists($path) || !is_file($path)){
            touch($path);
            return $path;
        }

        if(filesize($path) > 1*1024*1024){
            if(self::Bak($path,$act)){
                touch($path);
                return $path;
            }else{
                return $path;
            }
        }
        return $path;
    }

    static protected function Bak($path,$act){
        if($act == 'normal'){
            $newname = date('Y_m_d_H_i_s') . '_' . mt_rand(10000,99999) . '.log.bak';
            return rename($path,ROOT . 'data/log/' . $newname);
        }

        if($act == 'err'){
            $newname = date('Y_m_d_H_i_s') . '_' . mt_rand(10000,99999) . '.err.log.bak';
            return rename($path,ROOT . 'data/log/' . $newname);
        }
    }
}