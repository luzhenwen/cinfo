<?php
defined('ACC') || exit('Access Deined!');
class pageTool{
    protected $total = 20;
    protected $perpage = 20;
    protected $currpage = 1;

    public function __construct($total,$currpage = false,$perpage = false){
        $this->total = $total;
        if($perpage !== false){
            $this->perpage = $perpage;
        }
        if($currpage !== false){
            $this->currpage = $currpage;
        }
    }

    public function show(){
        $cnt = ceil($this->total/$this->perpage);
        $uri = $_SERVER['REQUEST_URI'];

        $pattern = '/((\/|)page\/(\d+)|^\/$)/';
        $url = preg_replace($pattern, '', $uri);
        $nav = array();
        $nav[] = '<a class="curr" ><span>' . $this->currpage .'</span></a>';
        for($left = $this->currpage-1,$right = $this->currpage +1 ; ($left >= 1 || $right <= $cnt) && count($nav) <= 9 ; $left-- , $right++){
            if($left>=1){
                array_unshift($nav,'<a href="' . $url .'/page/'.$left . '"><span>' . $left . '</span></a>');

            }

            if($right <= $cnt){

                array_push($nav,'<a href="' . $url .'/page/'.$right . '"><span>' . $right . '</span></a>');

            }
        }

        if(count($nav) > 1){
            if($this->currpage > 1) {
                array_unshift($nav,'<a class="chpage" href="' . $url .'/page/'. ($this->currpage-1) . '"><span>上一页</span></a>');
                array_unshift($nav,'<a class="chpage" href="' . $url .'/page/'. 1 . '"><span>首页</span></a>');
            }

            if($this->currpage < $cnt) {
                array_push($nav,'<a class="chpage" href="' . $url .'/page/' . ($this->currpage + 1) . '"><span>下一页</span></a>');
                array_push($nav,'<a class="chpage" href="' . $url .'/page/' . $cnt . '"><span>尾页</span></a>');
            }
            return implode('',$nav);
        }else{
            return false;
        }
    }

    public function searchShow(){
        $cnt = ceil($this->total/$this->perpage);
        $uri = $_SERVER['REQUEST_URI'];
        $ptmp = parse_url($uri);
        $path = $ptmp['path'];
        if(isset($ptmp['query'])){
            $query = $ptmp['query'];
            parse_str($query,$tmp);
            unset($tmp['page']);
            $query = http_build_query($tmp);
            $url = $path . '?' . $query . '&';
        }else{
            $url = $path . '?';
        }

        $nav = array();
        $nav[] = '<a class="curr" ><span>' . $this->currpage .'</span></a>';
        for($left = $this->currpage-1,$right = $this->currpage +1 ; ($left >= 1 || $right <= $cnt) && count($nav) <= 9 ; $left-- , $right++){
            if($left>=1){
                array_unshift($nav,'<a href="http://www.fusui.cc' . $url .'page='.$left . '"><span>' . $left . '</span></a>');

            }

            if($right <= $cnt){

                array_push($nav,'<a href="http://www.fusui.cc' . $url .'page='.$right . '"><span>' . $right . '</span></a>');

            }
        }

        if(count($nav) > 1){
            if($this->currpage > 1) {
                array_unshift($nav,'<a class="chpage" href="http://www.fusui.cc' . $url .'page='. ($this->currpage-1) . '"><span>上一页</span></a>');
                array_unshift($nav,'<a class="chpage" href="http://www.fusui.cc' . $url .'page='. 1 . '"><span>首页</span></a>');
            }

            if($this->currpage < $cnt) {
                array_push($nav,'<a class="chpage" href="http://www.fusui.cc' . $url .'page=' . ($this->currpage + 1) . '"><span>下一页</span></a>');
                array_push($nav,'<a class="chpage" href="http://www.fusui.cc' . $url .'page=' . $cnt . '"><span>尾页</span></a>');
            }
            return implode('',$nav);
        }else{
            return false;
        }
    }

    public function admin_show(){
        $cnt = ceil($this->total/$this->perpage);
        //echo $cnt;
        $uri = $_SERVER['REQUEST_URI'];
        $ptmp = parse_url($uri);
        $path = $ptmp['path'];
        if(isset($ptmp['query'])){
            $query = $ptmp['query'];
            parse_str($query,$tmp);
            unset($tmp['page']);
            $query = http_build_query($tmp);
            $url = $path . '?' . $query . '&';
        }else{
            $url = $path . '?';
        }
        $nav = array();
        $nav[] = '[' . $this->currpage .']';
        for($left = $this->currpage-1,$right = $this->currpage +1 ; ($left >= 1 || $right <= $cnt) && count($nav) <= 9 ; $left-- , $right++){
            if($left>=1){
                array_unshift($nav,'<a href="' . $url .'page='.$left . '">[' . $left . ']</a>');

            }

            if($right <= $cnt){

                array_push($nav,'<a href="' . $url .'page='.$right . '">[' . $right . ']</a>');

            }
        }
        if(count($nav) > 1){
            if($this->currpage > 1){
                array_unshift($nav, '<a href="' . $url .'page=1">[首页]</a>');
            }
            if($this->currpage < $cnt){
                array_push($nav, '<a href="' . $url .'page=' . $cnt . '">[尾页]</a>');
            }
        }
        return implode('',$nav);
    }
}