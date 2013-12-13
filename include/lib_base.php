<?php
defined('ACC') || exit('Access Deined!');
function deep_addslashes($data){
    if(!is_array($data)){
        $data = addslashes($data);
    }else{
        foreach($data as $k=>$v){
            $data[$k] = deep_addslashes($v);
        }
    }
    return $data;
}

function deep_urlencode($data){
	if(!is_array($data)){
		$data = urlencode($data);
	}else{
		foreach ($data as $key => $value) {
			$data[$key] = deep_urlencode($value);
		}
	}
	return $data;
}

function deep_urldecode($data){
	if(!is_array($data)){
		$data = urldecode($data);
	}else{
		foreach($data as $key => $value){
			$data[$key] = deep_urldecode($value);
		}
	}
	return $data;
}