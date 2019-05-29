<?php
class http {
    private static $instance = NULL;//实例对象
    private $host;//地址
    private $port = 80;//端口号
    private $fp = NULL;//socket链接资源
    private $timeout = 120;//socket请求超时时间(s)   
    private $header = array();//请求头部信息
    private $httpVer = 'HTTP/1.0';//请求标准
    private $crlf = "\r\n";//请求分隔符
    private $accept = "*/*";
    private $agent = 'Mozilla/5.0 (Windows NT 6.1; rv:20.0) Gecko/20100101 Firefox/20.0';//伪装浏览器
    private $maxLineLength = 4096;//最大行数长度
    private $maxLength = 1024;//最大数据长度
    private $resultHeader;//返回头部
    private $resultData;//返回内容
    
    /**
     * 获取实例单例
     * @return object 
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new http();
        }
        return self::$instance;
    }
    
    /**
     * 隐藏构造函数
     */
    private function __construct() {        
    }
    
    /**
     * 设置请求头部信息
     * @param array $header 请求头部信息数组 
     */
    public function setHeader($header) {
        $this->header = $header;
    }
    
    /**
     * 发送请求
     * @param string $url   请求链接
     * @param array $data   post请求数据 
     * @param string $method    请求方法post/get     
     */
    public function sendRequest($url, $data=array(), $method='post') {
        $urlArr = parse_url($url);
        if (isset($urlArr['port'])) {
            $this->port = $urlArr['port'];
        }
        switch (strtolower($urlArr['scheme'])) {
            case 'http':
                $this->host = $urlArr['host'];
                break;
            case 'https':
                $this->host = 'ssl://' . $urlArr['host'];
                $this->port = 443;
                break;
            default:
                //echo "Error: wrong url!";
                exit;
                break;
        }
        $this->connect();
        if (strtolower($method) == 'post') {
            $method = 'POST';
        } else {
            $method = 'GET';
        }
        $path = isset($urlArr['path']) ? $urlArr['path'] : '/';
        if (isset($urlArr['query'])) {
            $path .= '?' . $urlArr['query'];
        }
        $httpVer = isset($this->header['httpVer']) ? $this->header['httpVer'] : $this->httpVer;
        $headerStr = "{$method} {$path} {$httpVer}{$this->crlf}";
        $host = isset($urlArr['port']) ? $urlArr['host'] . ':' . $urlArr['port'] : $urlArr['host'];
        $headerStr .= "Host: {$host}{$this->crlf}";
        $accept = isset($this->header['Accept']) ? $this->header['Accept'] : $this->accept;
        $headerStr .= "Accept: {$accept}{$this->crlf}";
        if (isset($this->header['Referer'])) {
            $headerStr .= "Referer: {$this->header['Referer']}{$this->crlf}";
        }
        $agent = isset($this->header['User-Agent']) ? $this->header['User-Agent'] : $this->agent;
        $headerStr .= "User-Agent: {$agent}{$this->crlf}";
        if (isset($this->header['Cookie'])) {
            $headerStr .= "Cookie: {$this->header['Cookie']}{$this->crlf}";
        }
        $dataLength = 0;
        if ($method == 'POST') {            
            if (!empty($data)) {
                //$dataStr = http_build_query($data);
                if(is_array($data))
				{
					$dataStr = http_build_query($data);
				}
                else
				{
					$dataStr = $data;
				}
                $dataLength = strlen($dataStr);
            }            
            $headerStr .= "Content-Type: application/x-www-form-urlencoded{$this->crlf}";
            $headerStr .= "Cache-Control: no-cache{$this->crlf}";
            $headerStr .= "Pragma: no-cache{$this->crlf}";
            $headerStr .= "Content-Length: {$dataLength}{$this->crlf}";
        }
        else
        {
            if (!empty($data)) {
                $dataStr = http_build_query($data);
                $dataLength = strlen($dataStr);
            }            
            $headerStr .= "Content-Type: application/x-www-form-urlencoded{$this->crlf}";
            $headerStr .= "Cache-Control: no-cache{$this->crlf}";
            $headerStr .= "Pragma: no-cache{$this->crlf}";
            $headerStr .= "Content-Length: {$dataLength}{$this->crlf}";
        }
        $headerStr .= $this->crlf;
        if ($dataLength > 0) {
            $headerStr .= $dataStr;
        }
        fwrite($this->fp, $headerStr, strlen($headerStr));
        $this->resultHeader = '';
        while ($curContent = fgets($this->fp, $this->maxLineLength)) {    
            if ($curContent == $this->crlf) {
                break;
            }
            $this->resultHeader .= $curContent;
        }               
        $this->resultData = '';
        do {
            $curContent = fread($this->fp, $this->maxLength);
            //echo "<br>";        
            if (strlen($curContent) == 0) {
                break;
            }
            $this->resultData .= $curContent;
        } while(TRUE);
        $this->disconnect();
        unset($this->fp);
    }
    
    /**
     * 发送请求
     * @param string $url   请求链接
     * @param array $data   post请求数据 
     * @param string $method    请求方法post/get     
     */
    public function downLoad($url, $data=array(), $method='post',$name) {
        $urlArr = parse_url($url);
        if (isset($urlArr['port'])) {
            $this->port = $urlArr['port'];
        }
        switch (strtolower($urlArr['scheme'])) {
            case 'http':
                $this->host = $urlArr['host'];
                break;
            case 'https':
                $this->host = 'ssl://' . $urlArr['host'];
                $this->port = 443;
                break;
            default:
                //echo "Error: wrong url!";
                exit;
                break;
        }
        $this->connect();
        if (strtolower($method) == 'post') {
            $method = 'POST';
        } else {
            $method = 'GET';
        }
        $path = isset($urlArr['path']) ? $urlArr['path'] : '/';
        if (isset($urlArr['query'])) {
            $path .= '?' . $urlArr['query'];
        }
        $httpVer = isset($this->header['httpVer']) ? $this->header['httpVer'] : $this->httpVer;
        $headerStr = "{$method} {$path} {$httpVer}{$this->crlf}";
        $host = isset($urlArr['port']) ? $urlArr['host'] . ':' . $urlArr['port'] : $urlArr['host'];
        $headerStr .= "Host: {$host}{$this->crlf}";
        $accept = isset($this->header['Accept']) ? $this->header['Accept'] : $this->accept;
        $headerStr .= "Accept: {$accept}{$this->crlf}";
        if (isset($this->header['Referer'])) {
            $headerStr .= "Referer: {$this->header['Referer']}{$this->crlf}";
        }
        $agent = isset($this->header['User-Agent']) ? $this->header['User-Agent'] : $this->agent;
        $headerStr .= "User-Agent: {$agent}{$this->crlf}";
        if (isset($this->header['Cookie'])) {
            $headerStr .= "Cookie: {$this->header['Cookie']}{$this->crlf}";
        }
        $dataLength = 0;
        if ($method == 'POST') {            
            if (!empty($data)) {
                $dataStr = http_build_query($data);
                $dataLength = strlen($dataStr);
            }            
            $headerStr .= "Content-Type: application/x-www-form-urlencoded{$this->crlf}";
            $headerStr .= "Cache-Control: no-cache{$this->crlf}";
            $headerStr .= "Pragma: no-cache{$this->crlf}";
            $headerStr .= "Content-Length: {$dataLength}{$this->crlf}";
        }
        else
        {
            if (!empty($data)) {
                $dataStr = http_build_query($data);
                $dataLength = strlen($dataStr);
            }            
            $headerStr .= "Content-Type: application/x-www-form-urlencoded{$this->crlf}";
            $headerStr .= "Cache-Control: no-cache{$this->crlf}";
            $headerStr .= "Pragma: no-cache{$this->crlf}";
            $headerStr .= "Content-Length: {$dataLength}{$this->crlf}";
        }
        $headerStr .= $this->crlf;
        if ($dataLength > 0) {
            $headerStr .= $dataStr;
        }
        fwrite($this->fp, $headerStr, strlen($headerStr));
        $this->resultHeader = '';
        while ($curContent = fgets($this->fp, $this->maxLineLength)) {    
            if ($curContent == $this->crlf) {
                break;
            }
            $this->resultHeader .= $curContent;
        }               
        $this->resultData = '';
        do {
            $curContent = fread($this->fp, $this->maxLength);
            if (strlen($curContent) == 0) {
                break;
            }
            $this->resultData .= $curContent;
        } while(TRUE);
        
        //header("Location: ".$url);
        $time = date("YmdHis");
        $fp=fopen("file/mp3/".$name.$time.".mp3", "w+"); 
        $file = fopen("file/mp3/".$name.$time.".mp3","w");
        fwrite($file,$this->resultData); 
        return $name.$time;
    }
    
    
    /**
     * 获取返回的头部信息
     * @return string 
     */
    public function getResultHeader() {
        return $this->resultHeader;
    }
    
    /**
     * 获取返回的数据信息
     * @return string 
     */
    public function getResultData() {
        return $this->resultData;
    }
    
    /**
     * 发起请求
     */
    private function connect() {
        $this->fp = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
        if (!$this->fp) {
            echo "Error: {$errno}--{$errstr}";
            exit;
        }
    }
    
    /**
     * 结束请求
     */
    private function disconnect() {
        return fclose($this->fp);
    }
    
    public function showRequest($url, $data=array(), $method='post') {
        $urlArr = parse_url($url);
        if (isset($urlArr['port'])) {
            $this->port = $urlArr['port'];
        }
        switch (strtolower($urlArr['scheme'])) {
            case 'http':
                $this->host = $urlArr['host'];
                break;
            case 'https':
                $this->host = 'ssl://' . $urlArr['host'];
                $this->port = 443;
                break;
            default:
                //echo "Error: wrong url!";
                exit;
                break;
        }
        $this->connect();
        if (strtolower($method) == 'post') {
            $method = 'POST';
        } else {
            $method = 'GET';
        }
        $path = isset($urlArr['path']) ? $urlArr['path'] : '/';
        if (isset($urlArr['query'])) {
            $path .= '?' . $urlArr['query'];
        }
        $httpVer = isset($this->header['httpVer']) ? $this->header['httpVer'] : $this->httpVer;
        $headerStr = "{$method} {$path} {$httpVer}{$this->crlf}";
        $host = isset($urlArr['port']) ? $urlArr['host'] . ':' . $urlArr['port'] : $urlArr['host'];
        $headerStr .= "Host: {$host}{$this->crlf}";
        $accept = isset($this->header['Accept']) ? $this->header['Accept'] : $this->accept;
        $headerStr .= "Accept: {$accept}{$this->crlf}";
        if (isset($this->header['Referer'])) {
            $headerStr .= "Referer: {$this->header['Referer']}{$this->crlf}";
        }
        $agent = isset($this->header['User-Agent']) ? $this->header['User-Agent'] : $this->agent;
        $headerStr .= "User-Agent: {$agent}{$this->crlf}";
        if (isset($this->header['Cookie'])) {
            $headerStr .= "Cookie: {$this->header['Cookie']}{$this->crlf}";
        }
        $dataLength = 0;
        if ($method == 'POST') {            
            if (!empty($data)) {
				if(is_array($data))
				{
					$dataStr = http_build_query($data);
				}
                else
				{
					$dataStr = $data;
				}
				$dataLength = strlen($dataStr);
            }            
            $headerStr .= "Content-Type: application/x-www-form-urlencoded{$this->crlf}";
            $headerStr .= "Cache-Control: no-cache{$this->crlf}";
            $headerStr .= "Pragma: no-cache{$this->crlf}";
            $headerStr .= "Content-Length: {$dataLength}{$this->crlf}";
        }
        else
        {
            if (!empty($data)) {
                $dataStr = http_build_query($data);
                $dataLength = strlen($dataStr);
            }            
            $headerStr .= "Content-Type: application/x-www-form-urlencoded{$this->crlf}";
            $headerStr .= "Cache-Control: no-cache{$this->crlf}";
            $headerStr .= "Pragma: no-cache{$this->crlf}";
            $headerStr .= "Content-Length: {$dataLength}{$this->crlf}";
        }
        $headerStr .= $this->crlf;
        if ($dataLength > 0) {
            $headerStr .= $dataStr;
        }
        fwrite($this->fp, $headerStr, strlen($headerStr));
        $this->resultHeader = '';
        while ($curContent = fgets($this->fp, $this->maxLineLength)) {    
            if ($curContent == $this->crlf) {
                break;
            }
            $this->resultHeader .= $curContent;
        }               
        $this->resultData = '';
        do {
            $curContent = fread($this->fp, $this->maxLength);
            if (strlen($curContent) == 0) {
                break;
            }
            $this->resultData .= $curContent;
        } while(TRUE);
        return $this->resultData;
    }
}

?>