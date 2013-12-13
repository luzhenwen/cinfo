<?php
defined('ACC') || exit('Access Deined!');
class config{
    static protected $ins = null;
    protected $cfg = null;

    public function __construct(){
        require(ROOT . 'include/config.inc.php');
        $this->cfg = $_CFG;
    }

    static public function getIns(){
        if(!(self::$ins instanceof self)){
            self::$ins = new self();
        }
        return self::$ins;
    }

    public function __get($val){
        if(empty($this->cfg[$val])){
            return null;
        }else{
            return $this->cfg[$val];
        }
    }

    public function __set($key,$val){
        $this->cfg[$key] = $val;
    }
}