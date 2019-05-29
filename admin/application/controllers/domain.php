<?php

if (!file_exists("/data/conf/"))
{
    mkdir("/data/conf/", 0700);
}

$config_path = "/data/conf/domains.txt";
if (!file_exists($config_path)) 
{
	copy(__DIR__ . '/../../../domain/domains.txt', $config_path);
}

$config_path = "/data/conf/domain.txt";
if (!file_exists($config_path)) 
{
	  copy(__DIR__ . '/../../../domain/domain.txt', $config_path);
}

$config_path = "/data/conf/callback.txt";
if (!file_exists($config_path)) 
{
	 copy(__DIR__ . '/../../../domain/callback.txt', $config_path);
}

$config_path = "/data/conf/stop.status";
if (!file_exists($config_path)) 
{    
     if (file_exists("/../../../domain/stop.status"))
     copy(__DIR__ . '/../../../domain/stop.status', $config_path);
}

$config_path = "/data/conf/blacklist.txt";
if (!file_exists($config_path))
 {
    copy(__DIR__ . '/../../../domain/blacklist.txt', $config_path);
 }



class Domain extends CI_Controller
{
	/*
		构造函数
	*/
	function __construct()
	{
		header('Content-Type: text/html; charset=utf-8'); 
		parent::__construct();
		$this->load->helper('url');
	}

	function change_domain(){
        $params = file_get_contents('php://input');
        $params_ary = json_decode($params,true);
        $domain =  $params_ary['domain'];

        if(!$domain){
            $this->json("");exit;
        }

        $domain_path = config_item("domain_path");

        $now_callback = file_get_contents($domain_path."callback.txt");
        if($now_callback == $domain){
            $this->json("该域名已经是回调域名");exit;
        }

        $now_domain = file_get_contents($domain_path."domain.txt");

        $result['now'] = $now_domain;

	    $config_path = Game_CONST::APP_PATH . 'application/config/config.php';
	    $content = file_get_contents($config_path);
	    $content = str_replace($now_domain,$domain,$content);
	    file_put_contents($config_path,$content);

        $domain_path = config_item("domain_path");

        file_put_contents($domain_path."domain.txt",$domain);
	    $result = [];
	    $result['domain'] = $domain;
	    $this->json($result);
    }

    function change_callback(){
        $params = file_get_contents('php://input');
        $params_ary = json_decode($params,true);
        $domain =  $params_ary['domain'];

        if(!$domain){
            $this->json("");exit;
        }

        $domain_path = config_item("domain_path");

        $now_domain = file_get_contents($domain_path."domain.txt");

        if($now_domain == $domain){
            $this->json("该域名已经是跳转域名");exit;
        }

        $now_callback = file_get_contents($domain_path."callback.txt");

        $result['now'] = $now_domain;

        $config_path = Game_CONST::APP_PATH . 'application/config/config.php';
        $content = file_get_contents($config_path);
        $content = str_replace($now_callback, $domain, $content);
        file_put_contents($config_path,$content);

        $domain_path = config_item("domain_path");

        file_put_contents($domain_path."callback.txt",$domain);
        $result = [];
        $result['callback'] = $domain;
        $this->json($result);
    }

    function change_domains(){
        $params = file_get_contents('php://input');
        $params_ary = json_decode($params,true);
        $domains =  $params_ary['domains'];

        if(!$domains){
            $this->json([]);
        }
        $domains = trim($domains);
        $domains = trim($domains,"\n");

        $domain_path = config_item("domain_path");

        file_put_contents($domain_path."domains.txt",$domains);
        $result = [];
        $result['domains'] = $domains;
        $result['list']    = explode("\n",$domains);
        $this->json($result);
    }

    public function change_auto_state(){
        $domain_path = config_item("domain_path");

        $state_file = $domain_path."stop.status";
        if(file_exists($state_file)){
            unlink($state_file);
        }else{
            file_put_contents($state_file,'stop');
        }
        $this->json([]);
    }

    public function get_info(){
	    $result = [];
	    $domain_path = config_item("domain_path");

	    $now_domain = file_get_contents($domain_path."domain.txt");
        $result['now'] = $now_domain;

        $callback = file_get_contents($domain_path."callback.txt");
        $result['callback'] = $callback;

        $domains = file_get_contents($domain_path."domains.txt");
        $domains = trim($domains,"\n");
        $domains = trim($domains);
        $result['list'] = explode("\n",$domains);
        $result['domains'] = $domains;
        $result['black']  = file_get_contents($domain_path."blacklist.txt");

        $result['auto'] = false;
        if(file_exists($domain_path."stop.status")){
            $result['auto'] = true;
        }

        $this->json($result);
    }

    private function json($result){
        if(is_array($result)){
            echo json_encode($result);
        }else{
            echo $result;
        }
    }
	
}
