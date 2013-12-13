<?php
defined('ACC') || exit('Access Deined!');
class filterModel extends model{
	protected $table = 'filter_word';
	protected $pk = 'filter_id';
	protected $key_word = array();

	public function get_word(){
		$tmp = $this->select();
		foreach($tmp as $k=>$v){
			$this->key_word[] = $v['filter_word'];
		}
	}

	public function filter_word($data){
		foreach($this->key_word as $v){
			$pattern = "$v";

			if(preg_match($pattern, $data)){
				return false;
			}
		}
		return true;
	}
}