<?php

/**
 * @author FreezingTiny
 * @copyright 2015
 * INSERT INTO  `fsql`.`xxx_post_joke` (`j`)VALUES ('a');
 */

require 'dataBase.class.php';
$joke=joke_get();
echo $joke;
$db=new DataBase();
$db->table('xxx_post_joke');
$db->where("`j`= '$joke' ");
$list=$db->select();
if(!$list){
    $dbi = new DataBase();
            $dbi->table('xxx_post_joke');
            $dbi->data(array(array('j' => $joke)));
    echo $dbi->insert();
}else{die('repeat!');}


function joke_get(){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,'http://www.tuling123.com/openapi/api?key=a28fb250e4e954136706ce95915ec64e&info=%E8%AE%B2%E4%B8%AA%E7%AC%91%E8%AF%9D');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    $re = curl_exec($ch);
    curl_close($ch);
	$rej=json_decode($re);
	if($rej->code==100000){
	$output=$rej->text;
	}else{
	$output="";
	}
    return $output;
}

?>