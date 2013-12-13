<?php
defined('ACC') || exit('Access Deined!');
class categoryModel extends model{
    protected $table = 'category';
    protected $pk = 'cat_id';


    //查找子孙树
    //return array
    public function catTree($data,$id=0,$lev=1){
        $arr = array();
        foreach($data as $v){
            if($v['parent_id'] == $id){
                $v['lev'] = $lev;
                $arr[] = $v;
                $arr = array_merge($arr,$this->catTree($data,$v['cat_id'],$lev+1));
            }
        }
        return $arr;
    }

    //查找指定栏目ID的祖宗
    //return array
    public function getParents($cat_id){
        $data = $this->select();
        $parent = array();
        while($cat_id>0){
            foreach($data as $v){
                if($v['cat_id'] == $cat_id){
                    $parent[] = $v;
                    $cat_id = $v['parent_id'];
                }
            }
        }
        return $parent;
    }

    //取出指定栏目下的信息
    public function getInfo($cat_id){
        $sql = 'select * from info where cat_id = \'' . $cat_id . '\'';
        return $this->db->getAll($sql);
    }

    //传一个数字，看看这个数字是不是一个栏目ID
    public function is_cat($cat_id){
        $sql = 'select cat_id FROM ' . $this->table;
        $tmp = $this->db->getAll($sql);
        $data = array();
        foreach ($tmp as $key => $value) {
            $data[] = $value['cat_id'];
        }
        return in_array($cat_id, $data);
    }

    public function getCategoryName($cat_id){
        $sql = 'SELECT cat_name FROM ' . $this->table . ' WHERE cat_id=\'' . $cat_id . '\'';
        return $this->db->getOne($sql);
    }
}