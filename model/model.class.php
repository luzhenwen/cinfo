<?php
defined('ACC') || exit('Access Deined!');
class model{
    protected $table = null;
    protected $pk = null;
    protected $db = null;
    protected $fill = null;


    public function __construct(){
        $this->db = mysql::getIns();
    }

    public function add($data){
        return $this->db->autoExecute($this->table,$data);
    }

    public function del($id){
        $sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->pk . '=\'' . $id . '\'';

        $this->db->query($sql);

        return $this->db->affect_row();
    }

    public function update($data,$id){
        $this->db->autoExecute($this->table,$data,'update','WHERE ' . $this->pk . '=\'' . $id . '\'');
        return $this->db->affect_row();
    }

    public function select(){
        $sql = 'SELECT * FROM ' . $this->table;
        return $this->db->getAll($sql);
    }

    public function getRow($id){
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE ' . $this->pk . '=\'' . $id . '\'';
        return $this->db->getRow($sql);
    }

    public function getCol(){
        $sql = 'DESC ' . $this->table;
        $result = $this->db->query($sql);
        $arr = array();
        while(($row = mysql_fetch_assoc($result)) !== false){
            $arr[] = $row['Field'];
        }

        return $arr;
    }

    public function _field($data){
        $col = $this->getCol();
        $arr = array();
        foreach($data as $k=>$v){
            if(in_array($k,$col)){
                $arr[$k] = $v;
            }
        }

        return $arr;
    }

    public function autoFill($data){
        foreach($this->fill as $v){
            if(!isset($data[$v[0]]) || empty($data[$v[0]])){
                switch($v[1]){
                    case 'function':
                        $data[$v[0]] = $v[2]();
                        break;
                    case 'value':
                        $data[$v[0]] = $v[2];
                        break;
                }
            }
        }

        return $data;
    }

    public function affect_row(){
        return $this->db->affect_row();
    }

    public function insert_id(){
        return $this->db->insert_id();
    }
}