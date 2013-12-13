<?php
defined('ACC') || exit('Access Deined!');
class mysql{
    protected $cfg = null;
    protected $conn = null;
    static protected $ins = null;
    
    public function __construct(){
        $this->cfg = config::getIns();
        
        $this->conn($this->cfg->host,$this->cfg->user,$this->cfg->passwd);
        $this->sel_db($this->cfg->db);
        $this->set_char($this->cfg->char);
    }
    
    static public function getIns(){
        if(!(self::$ins instanceof self)){
            self::$ins = new self();
        }
        
        return self::$ins;
    }
    
    protected function conn($h,$u,$p){
        $this->conn = mysql_connect($h,$u,$p) or die('连接数据库错误');
    }
    
    protected function sel_db($db){
        $sql = 'USE ' . $db;
        $this->query($sql);
    }
    
    protected function set_char($char){
        $sql = 'SET NAMES ' . $char;
        $this->query($sql);
    }
    
    public function query($sql){
        log::write($sql);
        $result = mysql_query($sql,$this->conn);
        if(!$result){
            log::errWrite($sql);
        }
        return $result;
    }
    
    public function getAll($sql){
        $result = $this->query($sql);
        $arr = array();
        while(($row = mysql_fetch_assoc($result)) != false){
            $arr[] = $row;
        }
        return $arr;
    }
    
    public function getRow($sql){
        $result = $this->query($sql);
        return mysql_fetch_assoc($result);
    }
    
    public function getOne($sql){
        $result = $this->query($sql);
        $re = mysql_fetch_row($result);
        return $re[0];
    }
    
    public function autoExecute($table,$data,$act='insert',$where = 'WHERE 1 LIMIT 1'){
        //UPDATE goods SET goods_name = '你好' WHERE goods_id = '1';
        //INSERT INTO goods(goods_id,goods,name) VALUES('1','你好');
        if($act == 'update'){
            $sql = 'UPDATE ' . $table . ' SET ';
            foreach($data as $k=>$v){
                $sql .= $k . '=\'' . $v .'\',';
            }
            $sql = rtrim($sql,',');
            $sql .= ' ' . $where;
            return $this->query($sql);
        }
        
        $sql = 'INSERT INTO ' . $table . '(' . implode(array_keys($data),',') . ') VALUES(\'' . implode('\',\'',$data) .'\')';
        return $this->query($sql);
    }
    
    public function insert_id(){
        return mysql_insert_id($this->conn);
    }
    
    public function affect_row(){
        return mysql_affected_rows($this->conn);
    }
}