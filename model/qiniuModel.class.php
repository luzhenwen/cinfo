<?php
defined('ACC') || exit('Access Deined!');
class qiniuModel{
	static private $bucket = 'ifusui';
    static private $access_key = 'TQhTo389OIYS0IeZGKE4eYcoReM5ObP1uETVyfED';
    static private $secret_key = 'QwihNsUGL4uvVLUM8kyAnLZ5OQegowVo9bIbfm9x';
    static private $domain = 'ifusui.qiniudn.com';
    static private $del_url = 'http://rs.qbox.me/batch';
    static private $access_token = '';

    /*
    function del
    @dest 删除七牛服务器上的图片
    @param string $pic_name
    @return bool/array
    */
    static public function del($pic_name){
        $pic_name = explode('|', $pic_name);
        $request_body = '';
        foreach ($pic_name as $value) {
            $request_body .= 'op=/delete/' . self::get_entry_uri(self::$bucket, $value) . '&';
        }
        $request_body = rtrim($request_body, '&');
        $token = self::get_access_token(self::$access_key, self::$secret_key, self::$del_url, $request_body);
        $ch = curl_init();
        $curl_opt = array(
            CURLOPT_URL => self::$del_url,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Authorization: QBox ' . $token),
            CURLOPT_POSTFIELDS => $request_body
        );
        curl_setopt_array($ch, $curl_opt);
        curl_exec($ch);
        $curl_return = curl_multi_getcontent($ch);
        curl_close($ch);
        return $curl_return;
    }

    /*
    function get_entry_uri

    @dest 将资源空间（Bucket）和资源名（Key）用“:”连接起来，进行URL安全的Base64编码
    @param string $bucket
    @param string $key
    @return string
    */
    static private function get_entry_uri($bucket, $key){
        return self::urlsafe_base64_encode($bucket . ':' . $key);
    }

    /*
    function urlsafe_base64_encode
    @dest 进行URL安全的base64编码
    @param string $str
    @return string
    */
    static private function urlsafe_base64_encode($str){
        $search = array('+','/');
        $replace = array('-','_');
        return str_replace($search, $replace, base64_encode($str));
    }

    /*
    function get_access_token
    @dest 生成一个登陆七牛服务器的token
    @param string $access_key
    @param string $secret_key
    @param string $url
    @param string $body
    */
    static function get_access_token($access_key, $secret_key, $url, $body){
        $parsed_url = parse_url($url);
        $access = $parsed_url['path'];
        if(isset($parsed_url['query'])){
            $access .= '?' . $parsed_url;
        }
        $access .= "\n";
        if($body){
            $access .= $body;
        }
        $digest = hash_hmac('sha1', $access, $secret_key, true);
        return $access_key . ':' . self::urlsafe_base64_encode($digest);
    }

    static function get_up_token(){
        $deadline = time() + 3600;
        $options = array('scope' => self::$bucket, 'deadline' => $deadline, 'returnBody' => "{'etag':$(etag), statu:1}");
        $options = json_encode($options);
        $encode_flag = self::urlsafe_base64_encode($options);
        $signature = hash_hmac('sha1', $encode_flag, self::$secret_key, true);
        $encode_sign = self::urlsafe_base64_encode($signature);
        return self::$access_key . ':' . $encode_sign . ':' . $encode_flag;
    }
}

