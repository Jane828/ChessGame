<?php

include_once 'common_model.php';        //�������ݿ������
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
     * ����������������ת����pinyin������Է����ĵ�����
     * @param        $string
     * @param string $option �����"unicode", ���ص�ƴ����Я������
     * @return array
     * author ������
     * date 2019/2/21
     */
    public function convert($string, $option = Pinyin::NONE){
        $pinyin = new Pinyin();
        return $pinyin->convert($string, $option);
    }

    /**
     * ����������������ת����pinyin���ᱣ��������
     * @param      $string
     * @param bool $withTone ��ѡ�����������Ҫ���ص�ƴ�������꣬������PINYIN_TONE
     * @return string ƴ���ַ���
     * author ������
     * date 2019/2/21
     */
    public function sentence($string, $withTone = false){
        $pinyin = new Pinyin();
        return $pinyin->sentence($string, $withTone);
    }

    /**
     * ������������������ƴ������ĸ��
     * @param        $string
     * @param string $delimiter ָ����ĸ������ӷ�
     * @return string
     * author ������
     * date 2019/2/21
     */
    public function abbr($string, $delimiter = ''){
        $pinyin = new Pinyin();
        return $pinyin->abbr($string, $delimiter);
    }

}