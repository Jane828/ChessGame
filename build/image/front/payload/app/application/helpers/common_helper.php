<?php
/**
 * Created by PhpStorm.
 * User: nszxyu
 * Date: 2017/11/19
 * Time: 21:57
 */

/**
 * 格式化输出数组
 * @param array $data
 */
function println($data = array()){
    echo "<pre>";
    print_r($data);
    echo  "</pre>";
}


function getRequest(){
    $params = json_decode(file_get_contents('php://input'),true);
    return $params;
}