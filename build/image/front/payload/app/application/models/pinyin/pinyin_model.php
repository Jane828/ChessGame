<?php

include_once 'common_model.php';        //加载数据库操作类
include_once 'src/DictLoaderInterface.php';
include_once 'src/Pinyin.php';
include_once 'src/FileDictLoader.php';

use \Overtrue\Pinyin\Pinyin;

class Pinyin_Model extends Pinyin_Common_Model{
    public function __construct() {
        header('Content-Type: text/html; charset=utf-8');
        parent::__construct();
    }


    /**
     * 函数描述：将中文转换成pinyin，会忽略非中文的内容
     * @param        $string
     * @param string $option 如果填"unicode", 返回的拼音将携带音标
     * @return array
     * author 黄欣仕
     * date 2019/2/21
     */
    public function convert($string, $option = Pinyin::NONE){
        $pinyin = new Pinyin();
        return $pinyin->convert($string, $option);
    }

    /**
     * 函数描述：将中文转换成pinyin，会保留标点符号
     * @param      $string
     * @param bool $withTone 可选参数，如果想要返回的拼音有音标，可以填PINYIN_TONE
     * @return string 拼音字符串
     * author 黄欣仕
     * date 2019/2/21
     */
    public function sentence($string, $withTone = false){
        $pinyin = new Pinyin();
        return $pinyin->sentence($string, $withTone);
    }

    /**
     * 函数描述：返回中文拼音首字母，
     * @param        $string
     * @param string $delimiter 指定字母间的连接符
     * @return string
     * author 黄欣仕
     * date 2019/2/21
     */
    public function abbr($string, $delimiter = ''){
        $pinyin = new Pinyin();
        return $pinyin->abbr($string, $delimiter);
    }

}