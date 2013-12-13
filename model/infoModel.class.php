<?php
defined('ACC') || exit('Access Deined!');

class infoModel extends model{
    protected $table = 'info';
    protected $pk = 'info_id';
    protected $fill = array(
                            array('add_time','function','time'),
                            array('post_user','value','匿名')
                            );

    //查询出所有可显示的信息，并且用左链接把信息栏目添加上去
    public function select_info($offset = 0,$limit = 20){
        $sql = 'SELECT * FROM info LEFT JOIN category ON info.cat_id = category.cat_id WHERE display=1 ORDER BY add_time DESC LIMIT ' . $offset . ',' . $limit;
        return $this->db->getAll($sql);
    }

    //查询出所有在回收站的信息，并且用左链接把信息栏目添加上去
    public function select_trash_info($offset = 0, $limit = 20){
        $sql = 'SELECT * FROM info LEFT JOIN category ON info.cat_id = category.cat_id WHERE display=0 ORDER BY add_time DESC LIMIT ' . $offset . ',' . $limit;
        return $this->db->getAll($sql);
    }

    //取出在栏目页上显示所需要的字段（标题，内容，联系号码的图片）
    public function select_index($offset = 0,$limit = 20,$cat_id = ''){
        if(is_numeric($cat_id)){
            $cat_id = ' AND cat_id= \'' . $cat_id . '\'';
        }
        $sql = 'SELECT info_id,title,content,contact_img,pic_address,post_order FROM ' . $this->table . ' WHERE display=1' . $cat_id . ' ORDER BY post_order desc,add_time desc limit ' . $offset . ',' . $limit;

        return $this->db->getAll($sql);
    }
    /*
    function trans
    @dest 把信息移入回收站，同时删除联系号码图片
    @param int $info_id
    @return int
    */
    public function trans($info_id){
        $contact_img = $this->get_contact_img($info_id);
        if(is_file(ROOT . $contact_img)){
            unlink(ROOT . $contact_img);
        }

        $sql = 'UPDATE ' . $this->table . ' SET display=0 WHERE ' . $this->pk . '=\'' . $info_id .'\'';
        $this->db->query($sql);
        return $this->db->affect_row();
    }

    //用正则表达式验证电话号码
    public function checkContact($num){
        $pattern = '/^(\d{11})$/';

        return preg_match($pattern,$num);
    }

    //统计指定栏目下可显示的信息，用于分页
    public function count_display_info($cat_id=''){
        if($cat_id){
            $cat_id = ' AND cat_id=\'' . $cat_id . '\'';
        }
        $sql = 'select count(*) from ' . $this->table . ' WHERE display=1' . $cat_id;
        return $this->db->getOne($sql);
    }

    //统计回收站下的信息条数
    public function count_trans_info($cat_id=''){
        if($cat_id){
            $cat_id = ' AND cat_id=\'' . $cat_id . '\'';
        }
        $sql = 'SELECT count(*) FROM ' . $this->table . ' WHERE display=0' . $cat_id;
        return $this->db->getOne($sql);
    }

    //前台搜索信息
    public function search($key,$offset = 0, $limit = 20){

        $sql = 'SELECT info_id,title,content,contact_img,pic_address FROM ' . $this->table . ' WHERE display=1 AND (title LIKE \'%' . $key . '%\' OR content LIKE \'%' . $key . '%\') ORDER BY post_order  DESC,add_time DESC limit ' . $offset . ',' . $limit ;
        return $this->db->getAll($sql);
    }

    public function search_count($key){
        $sql = 'SELECT COUNT(*) FROM ' . $this->table . ' WHERE title like \'%' . $key . '%\' AND display=1';
        return $this->db->getOne($sql);
    }

    //后台搜索可显示的信息
    public function search_info($offset = 0 , $limit = 20 , $cat_id = '' , $type = '' , $key_word = ''){
        if($cat_id){
            $cat_id = ' AND ' . $this->table . '.cat_id=\'' . $cat_id . '\'';
        }else{
            $cat_id = '';
        }

        if($type){
            if($key_word){
                if($type=='title'){
                    $type = ' AND ' . $type . ' like \'%' . $key_word . '%\'';
                }else if($type == 'info_id' || $type == 'contact'){
                    $type = ' AND ' . $type . '=\'' . $key_word . '\'';
                }
            }else{
                $type = '';
            }
        }

        $sql = 'SELECT * FROM info LEFT JOIN category ON info.cat_id = category.cat_id WHERE display=1 ' . $cat_id . $type . ' limit ' . $offset . ',' . $limit;

        return $this->db->getAll($sql);
    }

    //后台搜索回收站的信息
    public function search_trans_info($offset = 0 , $limit = 20 , $cat_id = '' , $type = '' , $key_word = ''){
        if($cat_id){
            $cat_id = ' AND ' . $this->table . '.cat_id=\'' . $cat_id . '\'';
        }else{
            $cat_id = '';
        }

        if($type){
            if($key_word){
                if($type=='title'){
                    $type = ' AND ' . $type . ' like \'%' . $key_word . '%\'';
                }else if($type == 'info_id' || $type == 'contact'){
                    $type = ' AND ' . $type . '=\'' . $key_word . '\'';
                }
            }else{
                $type = '';
            }
        }

        $sql = 'SELECT * FROM info LEFT JOIN category ON info.cat_id = category.cat_id WHERE display=0 ' . $cat_id . $type . ' limit ' . $offset . ',' . $limit;

        return $this->db->getAll($sql);
    }

    /*
    function info_row
    @dest 返回单条信息
    @param int $id
    @return array
    */
    public function info_row($id){
        $sql = 'SELECT info_id,title,content,contact,contact_img,add_time,click_count,pic_address FROM ' . $this->table . ' WHERE info_id=\'' . $id . '\' AND display=1';
        return $this->db->getRow($sql);
    }

    /*
    function update_click
    @dest 用于在打开信息详细页面的时候更新点击数
    @param int $id
    @return bool
    */
    public function update_click($id){
        $sql = 'UPDATE ' . $this->table . ' SET click_count = click_count+1 WHERE info_id=\'' . $id . '\' AND display=1';
        return $this->db->query($sql);
    }

    /*
    function delete_info
    @dest 在前台删除信息用的，同时删除联系图片
    @param int $id
    @param string $del_key
    @return int
    */
    public function delete_info($id,$del_key){
        //删除联系图片
        $contact_img_path = ROOT;
        $contact_img_path .= $this->get_contact_img($id);
        if(is_file($contact_img_path)){
            unlink($contact_img_path);
        }

        $sql = 'UPDATE ' . $this->table . ' SET display=0 WHERE info_id=\'' . $id . '\' AND del_key=\'' . $del_key . '\'';
        $this->db->query($sql);
        return $this->db->affect_row();
    }

    /*
    function get_contact_img
    @dest 单独获取联系图片地址
    @param int $id
    @return string
    */
    public function get_contact_img($id){
        $sql = 'SELECT contact_img FROM ' . $this->table . ' WHERE info_id=\'' . $id . '\'';
        return $this->db->getOne($sql);
    }

    /*
    function get_info_img
    @dest 获得指定信息内的图片地址
    @param int $id
    @return string
    */
    public function get_info_img($id){
        $sql = 'SELECT pic_address FROM ' . $this->table . ' WHERE info_id=\'' . $id . '\'';
        return $this->db->getOne($sql);
    }

    /*
    function cancel_order
    @dest 取消已经过了置顶有效期的置顶
    @return int
    */
    public function cancel_order(){
        $curr_time = time();
        $sql = 'UPDATE info SET post_order=0 WHERE order_valid_time < ' .  $curr_time;
        $this->db->query($sql);
        return $this->db->affect_row();
    }
}